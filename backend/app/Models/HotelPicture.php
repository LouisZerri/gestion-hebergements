<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Modèle représentant une photo liée à un hôtel.
 */
class HotelPicture extends Model
{
    use HasFactory;

    /**
     * Champs pouvant être remplis en masse.
     */
    protected $fillable = [
        'hotel_id',
        'filepath',
        'filesize',
        'position',
    ];

    /**
     * Conversion automatique des types pour certains attributs.
     */
    protected $casts = [
        'filesize' => 'integer',
        'position' => 'integer',
        'hotel_id' => 'integer',
    ];

    /**
     * Relation : la photo appartient à un hôtel.
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    /**
     * Attribut virtuel : URL complète du fichier stocké.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->filepath);
    }

    /**
     * Événement : supprime le fichier physique lorsque le modèle est supprimé.
     */
    protected static function booted(): void
    {
        static::deleting(function (HotelPicture $picture) {
            if (Storage::exists($picture->filepath)) {
                Storage::delete($picture->filepath);
            }
        });
    }
}
