<?php

namespace Database\Seeders;

use App\Models\Hotel;
use Illuminate\Database\Seeder;

/**
 * Seeder pour la table des hôtels.
 * Crée 10 hôtels avec des informations complètes pour tests ou développement.
 */
class HotelSeeder extends Seeder
{
    /**
     * Exécute le seeder.
     */
    public function run(): void
    {
        $hotels = [
            [
                'name' => 'Le Grand Hôtel Paris',
                'address_1' => '123 Boulevard Haussmann',
                'address_2' => null,
                'zip_code' => '75008',
                'city' => 'Paris',
                'country' => 'France',
                'longitude' => 2.3522,
                'latitude' => 48.8566,
                'description' => 'Un hôtel de luxe au cœur de Paris, offrant une vue imprenable sur les Champs-Élysées. Chambres spacieuses avec décoration haussmannienne, restaurant gastronomique étoilé et spa de luxe avec piscine intérieure.',
                'max_capacity' => 150,
                'price_per_night' => 250.00,
            ],
            [
                'name' => 'Hôtel de la Mer',
                'address_1' => '45 Promenade des Anglais',
                'address_2' => null,
                'zip_code' => '06000',
                'city' => 'Nice',
                'country' => 'France',
                'longitude' => 7.2619,
                'latitude' => 43.7102,
                'description' => 'Hôtel les pieds dans l\'eau avec accès direct à la plage privée. Vue panoramique sur la Baie des Anges, terrasse sur le toit et restaurant méditerranéen.',
                'max_capacity' => 80,
                'price_per_night' => 180.00,
            ],
            [
                'name' => 'Château de Versailles Hotel',
                'address_1' => '10 Rue de la Paroisse',
                'address_2' => null,
                'zip_code' => '78000',
                'city' => 'Versailles',
                'country' => 'France',
                'longitude' => 2.1301,
                'latitude' => 48.8049,
                'description' => 'Hôtel historique à proximité du célèbre château de Versailles. Architecture classique du XVIIIe siècle, jardins à la française et service de conciergerie premium.',
                'max_capacity' => 60,
                'price_per_night' => 200.00,
            ],
            [
                'name' => 'Lyon Centre Hotel',
                'address_1' => '28 Rue de la République',
                'address_2' => 'Place Bellecour',
                'zip_code' => '69002',
                'city' => 'Lyon',
                'country' => 'France',
                'longitude' => 4.8357,
                'latitude' => 45.7640,
                'description' => 'Hôtel moderne au centre de Lyon, idéal pour découvrir la gastronomie lyonnaise. Proche des bouchons traditionnels, vue sur la Saône et parking sécurisé.',
                'max_capacity' => 100,
                'price_per_night' => 120.00,
            ],
            [
                'name' => 'Bordeaux Wine Hotel',
                'address_1' => '15 Quai des Chartrons',
                'address_2' => null,
                'zip_code' => '33000',
                'city' => 'Bordeaux',
                'country' => 'France',
                'longitude' => -0.5792,
                'latitude' => 44.8378,
                'description' => 'Hôtel boutique au cœur du quartier des vins avec dégustation quotidienne. Cave à vin exceptionnelle avec plus de 500 références, sommelier sur place.',
                'max_capacity' => 40,
                'price_per_night' => 160.00,
            ],
            [
                'name' => 'Mont-Blanc Resort',
                'address_1' => '50 Route du Mont-Blanc',
                'address_2' => null,
                'zip_code' => '74400',
                'city' => 'Chamonix',
                'country' => 'France',
                'longitude' => 6.8694,
                'latitude' => 45.9237,
                'description' => 'Resort de montagne avec vue panoramique sur le Mont-Blanc et spa thermal. Accès direct aux pistes de ski, jacuzzi extérieur chauffé et restaurant d\'altitude.',
                'max_capacity' => 120,
                'price_per_night' => 300.00,
            ],
            [
                'name' => 'Hôtel du Vieux Port',
                'address_1' => '8 Quai du Port',
                'address_2' => null,
                'zip_code' => '13002',
                'city' => 'Marseille',
                'country' => 'France',
                'longitude' => 5.3698,
                'latitude' => 43.2965,
                'description' => 'Hôtel traditionnel avec vue sur le Vieux-Port et Notre-Dame de la Garde. Ambiance provençale authentique, cuisine méditerranéenne et terrasse panoramique.',
                'max_capacity' => 70,
                'price_per_night' => 140.00,
            ],
            [
                'name' => 'Strasbourg Cathedral Inn',
                'address_1' => '22 Rue du Dôme',
                'address_2' => 'Quartier de la Petite France',
                'zip_code' => '67000',
                'city' => 'Strasbourg',
                'country' => 'France',
                'longitude' => 7.7521,
                'latitude' => 48.5734,
                'description' => 'Hôtel alsacien authentique près de la cathédrale. Architecture à colombages typique, winstub sur place avec spécialités locales et marché de Noël en décembre.',
                'max_capacity' => 50,
                'price_per_night' => 110.00,
            ],
            [
                'name' => 'Toulouse Capitole Suites',
                'address_1' => '5 Place du Capitole',
                'address_2' => null,
                'zip_code' => '31000',
                'city' => 'Toulouse',
                'country' => 'France',
                'longitude' => 1.4437,
                'latitude' => 43.6047,
                'description' => 'Hôtel design au cœur de la ville rose. Suites spacieuses avec kitchenette, rooftop avec bar à cocktails et vue à 360° sur Toulouse.',
                'max_capacity' => 90,
                'price_per_night' => 135.00,
            ],
            [
                'name' => 'Bretagne Ocean Lodge',
                'address_1' => '33 Boulevard de la Plage',
                'address_2' => null,
                'zip_code' => '29100',
                'city' => 'Douarnenez',
                'country' => 'France',
                'longitude' => -4.3333,
                'latitude' => 48.0928,
                'description' => 'Lodge face à l\'océan Atlantique en Bretagne. Décoration marine, crêperie traditionnelle, location de vélos et excursions en bateau pour observer les dauphins.',
                'max_capacity' => 35,
                'price_per_night' => 95.00,
            ],
        ];

        foreach ($hotels as $hotelData) {
            Hotel::create($hotelData);
        }

        $this->command->info('10 hôtels créés avec succès !');
    }
}