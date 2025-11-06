<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * Modèle représentant un hôtel.
 */
class Hotel extends Model
{
    use HasFactory;

    /**
     * Champs pouvant être remplis en masse.
     */
    protected $fillable = [
        'name',
        'address_1',
        'address_2',
        'zip_code',
        'city',
        'country',
        'longitude',
        'latitude',
        'description',
        'max_capacity',
        'price_per_night',
    ];

    /**
     * Conversion automatique des types pour certains attributs.
     */
    protected $casts = [
        'longitude' => 'float',
        'latitude' => 'float',
        'price_per_night' => 'float',
        'max_capacity' => 'integer',
    ];

    /**
     * Boot du modèle pour gérer la suppression des fichiers
     */
    protected static function boot()
    {
        parent::boot();

        // Avant de supprimer l'hôtel
        static::deleting(function ($hotel) {
            // Supprimer toutes les photos (BDD + fichiers)
            foreach ($hotel->pictures as $picture) {
                // Supprimer le fichier physique
                if (Storage::disk('public')->exists($picture->filepath)) {
                    Storage::disk('public')->delete($picture->filepath);
                }
                
                // Supprimer l'enregistrement BDD (au cas où cascade ne fonctionne pas)
                $picture->delete();
            }

            // Supprimer le dossier de l'hôtel s'il est vide
            $hotelFolder = "hotels/{$hotel->id}";
            if (Storage::disk('public')->exists($hotelFolder)) {
                $files = Storage::disk('public')->files($hotelFolder);
                if (empty($files)) {
                    Storage::disk('public')->deleteDirectory($hotelFolder);
                }
            }
        });
    }

    /**
     * Relation : un hôtel possède plusieurs photos.
     */
    public function pictures(): HasMany
    {
        return $this->hasMany(HotelPicture::class)->orderBy('position');
    }

    /**
     * Attribut virtuel : renvoie l’adresse complète de l’hôtel.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_1,
            $this->address_2,
            $this->zip_code . ' ' . $this->city,
            $this->country
        ]);

        return implode(', ', $parts);
    }
}
