<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\HotelPicture;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer 10 hôtels
        Hotel::factory(10)->create()->each(function ($hotel) {
            // Générer entre 1 et 3 photos pour chaque hôtel
            $numberOfPictures = rand(1, 3);
            
            for ($i = 0; $i < $numberOfPictures; $i++) {
                $this->createDummyImage($hotel, $i);
            }
        });
    }

    /**
     * Créer une image placeholder pour un hôtel
     */
    private function createDummyImage(Hotel $hotel, int $position): void
    {
        // Créer le dossier de l'hôtel
        $hotelFolder = "hotels/{$hotel->id}";
        Storage::disk('public')->makeDirectory($hotelFolder);

        // Générer un nom de fichier unique
        $filename = time() . "_{$position}_" . uniqid() . '.jpg';
        $filepath = "{$hotelFolder}/{$filename}";
        $fullPath = storage_path("app/public/{$filepath}");

        // Dimensions aléatoires pour varier
        $width = rand(800, 1200);
        $height = rand(600, 900);

        // Couleurs de fond variées
        $colors = [
            ['bg' => '4A90E2', 'text' => 'FFFFFF'], // Bleu
            ['bg' => '50C878', 'text' => 'FFFFFF'], // Vert
            ['bg' => 'F39C12', 'text' => 'FFFFFF'], // Orange
            ['bg' => '9B59B6', 'text' => 'FFFFFF'], // Violet
            ['bg' => 'E74C3C', 'text' => 'FFFFFF'], // Rouge
            ['bg' => '34495E', 'text' => 'FFFFFF'], // Gris foncé
            ['bg' => '1ABC9C', 'text' => 'FFFFFF'], // Turquoise
            ['bg' => 'E67E22', 'text' => 'FFFFFF'], // Carotte
        ];
        $color = $colors[array_rand($colors)];

        // Texte à afficher (nom de l'hôtel + position)
        $text = urlencode(substr($hotel->name, 0, 20) . " #" . ($position + 1));
        
        // Utiliser placehold.co (alternative à via.placeholder.com)
        $imageUrl = "https://placehold.co/{$width}x{$height}/{$color['bg']}/{$color['text']}/jpeg?text={$text}";

        // Télécharger l'image avec curl (plus fiable que file_get_contents)
        $ch = curl_init($imageUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $imageContent = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($imageContent !== false && $httpCode === 200) {
            // Sauvegarder l'image
            File::put($fullPath, $imageContent);
            $filesize = filesize($fullPath);

            // Créer l'enregistrement en base
            HotelPicture::create([
                'hotel_id' => $hotel->id,
                'filepath' => $filepath,
                'filesize' => $filesize,
                'position' => $position,
            ]);
            
            echo "✓ Photo créée pour {$hotel->name} (position {$position})\n";
        } else {
            echo "✗ Erreur de téléchargement pour {$hotel->name} (position {$position})\n";
        }
    }
}