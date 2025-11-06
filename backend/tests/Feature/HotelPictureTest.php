<?php

namespace Tests\Feature;

use App\Models\Hotel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HotelPictureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test : Upload une photo
     */
    public function test_can_upload_single_picture(): void
    {
        $hotel = Hotel::factory()->create();
        $file = UploadedFile::fake()->image('hotel.jpg', 800, 600);

        $response = $this->postJson("/api/hotels/{$hotel->id}/pictures", [
            'pictures' => [$file],
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'code' => 201,
                'message' => '1 photo uploadée avec succès',
            ])
            ->assertJsonStructure([
                'success',
                'code',
                'message',
                'data' => [
                    '*' => ['id', 'hotel_id', 'filepath', 'filesize', 'position'],
                ],
            ]);

        $this->assertDatabaseHas('hotel_pictures', [
            'hotel_id' => $hotel->id,
            'position' => 0,
        ]);

        // Vérifier que le fichier a été stocké
        $picture = $hotel->pictures()->first();
        $this->assertTrue(Storage::disk('public')->exists($picture->filepath));
    }

    /**
     * Test : Upload plusieurs photos
     */
    public function test_can_upload_multiple_pictures(): void
    {
        $hotel = Hotel::factory()->create();
        $files = [
            UploadedFile::fake()->image('hotel1.jpg'),
            UploadedFile::fake()->image('hotel2.jpg'),
            UploadedFile::fake()->image('hotel3.jpg'),
        ];

        $response = $this->postJson("/api/hotels/{$hotel->id}/pictures", [
            'pictures' => $files,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'code' => 201,
                'message' => '3 photos uploadées avec succès',
            ])
            ->assertJsonCount(3, 'data');

        $this->assertDatabaseCount('hotel_pictures', 3);
        
        // Vérifier les positions
        $this->assertDatabaseHas('hotel_pictures', ['hotel_id' => $hotel->id, 'position' => 0]);
        $this->assertDatabaseHas('hotel_pictures', ['hotel_id' => $hotel->id, 'position' => 1]);
        $this->assertDatabaseHas('hotel_pictures', ['hotel_id' => $hotel->id, 'position' => 2]);
    }

    /**
     * Test : Upload sans fichier (validation)
     */
    public function test_cannot_upload_without_pictures(): void
    {
        $hotel = Hotel::factory()->create();

        $response = $this->postJson("/api/hotels/{$hotel->id}/pictures", [
            'pictures' => [],
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'code' => 422,
                'message' => 'Erreur de validation',
            ])
            ->assertJsonValidationErrors(['pictures']);
    }

    /**
     * Test : Upload avec fichier non-image (validation)
     */
    public function test_cannot_upload_non_image_file(): void
    {
        $hotel = Hotel::factory()->create();
        $file = UploadedFile::fake()->create('document.pdf', 1000);

        $response = $this->postJson("/api/hotels/{$hotel->id}/pictures", [
            'pictures' => [$file],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['pictures.0']);
    }

    /**
     * Test : Upload avec fichier trop volumineux (validation)
     */
    public function test_cannot_upload_oversized_file(): void
    {
        $hotel = Hotel::factory()->create();
        $file = UploadedFile::fake()->image('huge.jpg')->size(6000); // 6MB (max: 5MB)

        $response = $this->postJson("/api/hotels/{$hotel->id}/pictures", [
            'pictures' => [$file],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['pictures.0']);
    }

    /**
     * Test : Les positions s'incrémentent correctement
     */
    public function test_positions_increment_correctly(): void
    {
        $hotel = Hotel::factory()->create();

        // Premier upload
        $this->postJson("/api/hotels/{$hotel->id}/pictures", [
            'pictures' => [UploadedFile::fake()->image('photo1.jpg')],
        ]);

        // Deuxième upload
        $this->postJson("/api/hotels/{$hotel->id}/pictures", [
            'pictures' => [
                UploadedFile::fake()->image('photo2.jpg'),
                UploadedFile::fake()->image('photo3.jpg'),
            ],
        ]);

        $pictures = $hotel->pictures()->orderBy('position')->get();
        
        $this->assertEquals(0, $pictures[0]->position);
        $this->assertEquals(1, $pictures[1]->position);
        $this->assertEquals(2, $pictures[2]->position);
    }

    /**
     * Test : Mettre à jour la position d'une photo
     */
    public function test_can_update_picture_position(): void
    {
        $hotel = Hotel::factory()->create();
        $picture = $hotel->pictures()->create([
            'filepath' => 'hotels/1/test.jpg',
            'filesize' => 1024,
            'position' => 0,
        ]);

        $response = $this->patchJson("/api/hotels/{$hotel->id}/pictures/{$picture->id}", [
            'position' => 5,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200,
                'message' => 'Position de la photo mise à jour avec succès',
                'data' => [
                    'position' => 5,
                ],
            ]);

        $this->assertDatabaseHas('hotel_pictures', [
            'id' => $picture->id,
            'position' => 5,
        ]);
    }

    /**
     * Test : Mettre à jour une photo sans position (validation)
     */
    public function test_cannot_update_picture_without_position(): void
    {
        $hotel = Hotel::factory()->create();
        $picture = $hotel->pictures()->create([
            'filepath' => 'hotels/1/test.jpg',
            'filesize' => 1024,
            'position' => 0,
        ]);

        $response = $this->patchJson("/api/hotels/{$hotel->id}/pictures/{$picture->id}", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['position']);
    }

    /**
     * Test : Ne peut pas mettre à jour une photo d'un autre hôtel
     */
    public function test_cannot_update_picture_from_another_hotel(): void
    {
        $hotel1 = Hotel::factory()->create();
        $hotel2 = Hotel::factory()->create();
        
        $picture = $hotel2->pictures()->create([
            'filepath' => 'hotels/2/test.jpg',
            'filesize' => 1024,
            'position' => 0,
        ]);

        $response = $this->patchJson("/api/hotels/{$hotel1->id}/pictures/{$picture->id}", [
            'position' => 1,
        ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'code' => 403,
                'message' => 'Cette photo n\'appartient pas à cet hôtel',
            ]);
    }

    /**
     * Test : Supprimer une photo
     */
    public function test_can_delete_picture(): void
    {
        $hotel = Hotel::factory()->create();
        
        $picture = $hotel->pictures()->create([
            'filepath' => "hotels/{$hotel->id}/test.jpg",
            'filesize' => 1024,
            'position' => 0,
        ]);

        $response = $this->deleteJson("/api/hotels/{$hotel->id}/pictures/{$picture->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'code' => 200,
                'message' => 'Photo supprimée avec succès',
            ]);

        // Vérifier que la photo est supprimée de la BDD
        $this->assertDatabaseMissing('hotel_pictures', [
            'id' => $picture->id,
        ]);
    }

    /**
     * Test : Ne peut pas supprimer une photo d'un autre hôtel
     */
    public function test_cannot_delete_picture_from_another_hotel(): void
    {
        $hotel1 = Hotel::factory()->create();
        $hotel2 = Hotel::factory()->create();
        
        $picture = $hotel2->pictures()->create([
            'filepath' => 'hotels/2/test.jpg',
            'filesize' => 1024,
            'position' => 0,
        ]);

        $response = $this->deleteJson("/api/hotels/{$hotel1->id}/pictures/{$picture->id}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'code' => 403,
            ]);

        // La photo ne doit pas être supprimée
        $this->assertDatabaseHas('hotel_pictures', [
            'id' => $picture->id,
        ]);
    }

    /**
     * Test : La suppression d'un hôtel supprime ses photos (cascade BDD)
     */
    public function test_deleting_hotel_deletes_pictures(): void
    {
        $hotel = Hotel::factory()->create();
        
        $hotel->pictures()->create([
            'filepath' => "hotels/{$hotel->id}/test1.jpg",
            'filesize' => 1024,
            'position' => 0,
        ]);
        
        $hotel->pictures()->create([
            'filepath' => "hotels/{$hotel->id}/test2.jpg",
            'filesize' => 1024,
            'position' => 1,
        ]);

        // Supprimer l'hôtel
        $hotel->delete();

        // Vérifier que les photos sont supprimées de la BDD grâce au ON DELETE CASCADE
        $this->assertDatabaseCount('hotel_pictures', 0);
    }
}