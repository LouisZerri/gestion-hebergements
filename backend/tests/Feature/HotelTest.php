<?php

namespace Tests\Feature;

use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Teste les fonctionnalités API pour la gestion des hôtels.
 */
class HotelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Vérifie que la liste des hôtels fonctionne même si elle est vide.
     */
    public function test_can_list_hotels_when_empty(): void
    {
        $response = $this->getJson('/api/hotels');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200,
                'message' => 'Liste des hôtels récupérée avec succès',
            ])
            ->assertJsonStructure([
                'success',
                'code',
                'message',
                'data' => [
                    'current_page',
                    'data',
                    'total',
                ],
            ]);
    }

    /**
     * Vérifie que la liste des hôtels retourne les données existantes.
     */
    public function test_can_list_hotels_with_data(): void
    {
        Hotel::factory()->count(3)->create();

        $response = $this->getJson('/api/hotels');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200,
            ])
            ->assertJsonCount(3, 'data.data');
    }

    /**
     * Vérifie la création d'un hôtel via l'API.
     */
    public function test_can_create_hotel(): void
    {
        $hotelData = [
            'name' => 'Hôtel Test',
            'address_1' => '123 Rue Test',
            'address_2' => null,
            'zip_code' => '75001',
            'city' => 'Paris',
            'country' => 'France',
            'longitude' => 2.3522,
            'latitude' => 48.8566,
            'description' => 'Un hôtel de test',
            'max_capacity' => 100,
            'price_per_night' => 150.00,
        ];

        $response = $this->postJson('/api/hotels', $hotelData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'code' => 201,
                'message' => 'Hôtel créé avec succès',
                'data' => [
                    'name' => 'Hôtel Test',
                    'city' => 'Paris',
                    'price_per_night' => 150.00,
                ],
            ]);

        $this->assertDatabaseHas('hotels', [
            'name' => 'Hôtel Test',
            'city' => 'Paris',
        ]);
    }

    /**
     * Vérifie que la création échoue si la longitude est invalide.
     */
    public function test_cannot_create_hotel_with_invalid_longitude(): void
    {
        $hotelData = [
            'name' => 'Hôtel Test',
            'address_1' => '123 Rue Test',
            'zip_code' => '75001',
            'city' => 'Paris',
            'country' => 'France',
            'longitude' => 200,
            'latitude' => 48.8566,
            'max_capacity' => 100,
            'price_per_night' => 150.00,
        ];

        $response = $this->postJson('/api/hotels', $hotelData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['longitude']);
    }

    /**
     * Vérifie que la création échoue si la capacité maximale dépasse 200.
     */
    public function test_cannot_create_hotel_with_capacity_over_200(): void
    {
        $hotelData = [
            'name' => 'Hôtel Test',
            'address_1' => '123 Rue Test',
            'zip_code' => '75001',
            'city' => 'Paris',
            'country' => 'France',
            'longitude' => 2.3522,
            'latitude' => 48.8566,
            'max_capacity' => 250,
            'price_per_night' => 150.00,
        ];

        $response = $this->postJson('/api/hotels', $hotelData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['max_capacity']);
    }

    /**
     * Vérifie la récupération d'un hôtel spécifique.
     */
    public function test_can_show_hotel(): void
    {
        $hotel = Hotel::factory()->create([
            'name' => 'Mon Hôtel',
            'city' => 'Lyon',
        ]);

        $response = $this->getJson("/api/hotels/{$hotel->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200,
                'data' => [
                    'id' => $hotel->id,
                    'name' => 'Mon Hôtel',
                    'city' => 'Lyon',
                ],
            ]);
    }

    /**
     * Vérifie la mise à jour d'un hôtel.
     */
    public function test_can_update_hotel(): void
    {
        $hotel = Hotel::factory()->create([
            'name' => 'Ancien Nom',
            'price_per_night' => 100.00,
        ]);

        $updateData = [
            'name' => 'Nouveau Nom',
            'address_1' => $hotel->address_1,
            'zip_code' => $hotel->zip_code,
            'city' => $hotel->city,
            'country' => $hotel->country,
            'longitude' => $hotel->longitude,
            'latitude' => $hotel->latitude,
            'max_capacity' => $hotel->max_capacity,
            'price_per_night' => 200.00,
        ];

        $response = $this->putJson("/api/hotels/{$hotel->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200,
                'message' => 'Hôtel mis à jour avec succès',
                'data' => [
                    'name' => 'Nouveau Nom',
                    'price_per_night' => 200.00,
                ],
            ]);

        $this->assertDatabaseHas('hotels', [
            'id' => $hotel->id,
            'name' => 'Nouveau Nom',
            'price_per_night' => 200.00,
        ]);
    }

    /**
     * Vérifie la suppression d'un hôtel.
     */
    public function test_can_delete_hotel(): void
    {
        $hotel = Hotel::factory()->create();

        $response = $this->deleteJson("/api/hotels/{$hotel->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200,
            ]);

        $this->assertDatabaseMissing('hotels', [
            'id' => $hotel->id,
        ]);
    }

    /**
     * Vérifie la recherche d'hôtels par nom ou ville.
     */
    public function test_can_search_hotels(): void
    {
        Hotel::factory()->create(['name' => 'Hôtel Paris Centre', 'city' => 'Paris']);
        Hotel::factory()->create(['name' => 'Hôtel Lyon', 'city' => 'Lyon']);
        Hotel::factory()->create(['name' => 'Hôtel Paris Nord', 'city' => 'Paris']);

        $response = $this->getJson('/api/hotels/search?q=Paris');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200,
            ])
            ->assertJsonCount(2, 'data.data');
    }

    /**
     * Vérifie que la recherche sans paramètre renvoie une erreur.
     */
    public function test_cannot_search_without_query(): void
    {
        $response = $this->getJson('/api/hotels/search');

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'code' => 400,
                'message' => 'Le paramètre de recherche "q" est requis',
            ]);
    }

    /**
     * Vérifie le filtrage des hôtels par ville.
     */
    public function test_can_filter_hotels_by_city(): void
    {
        Hotel::factory()->create(['city' => 'Paris']);
        Hotel::factory()->create(['city' => 'Lyon']);
        Hotel::factory()->create(['city' => 'Paris']);

        $response = $this->getJson('/api/hotels?city=Paris');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data.data');
    }

    /**
     * Vérifie le filtrage des hôtels par prix.
     */
    public function test_can_filter_hotels_by_price(): void
    {
        Hotel::factory()->create(['price_per_night' => 50.00]);
        Hotel::factory()->create(['price_per_night' => 150.00]);
        Hotel::factory()->create(['price_per_night' => 250.00]);

        $response = $this->getJson('/api/hotels?min_price=100&max_price=200');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data.data');
    }

    /**
     * Vérifie le tri des hôtels par prix.
     */
    public function test_can_sort_hotels_by_price(): void
    {
        Hotel::factory()->create(['name' => 'Hôtel A', 'price_per_night' => 200.00]);
        Hotel::factory()->create(['name' => 'Hôtel B', 'price_per_night' => 100.00]);
        Hotel::factory()->create(['name' => 'Hôtel C', 'price_per_night' => 150.00]);

        $response = $this->getJson('/api/hotels?sort_by=price_per_night&sort_order=asc');

        $response->assertStatus(200);
        
        $data = $response->json('data.data');
        $this->assertEquals(100.00, $data[0]['price_per_night']);
        $this->assertEquals(150.00, $data[1]['price_per_night']);
        $this->assertEquals(200.00, $data[2]['price_per_night']);
    }
}
