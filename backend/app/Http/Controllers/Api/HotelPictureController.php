<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelPicture;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Contrôleur API pour la gestion des photos des hôtels.
 * Utilise le trait ApiResponse pour des réponses JSON uniformes.
 */
class HotelPictureController extends Controller
{
    use ApiResponse;

    /**
     * Upload d’une ou plusieurs photos pour un hôtel.
     */
    public function store(Request $request, Hotel $hotel): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pictures' => 'required|array|min:1',
            'pictures.*' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
        ], [
            'pictures.required' => 'Au moins une image est requise',
            'pictures.*.image' => 'Le fichier doit être une image',
            'pictures.*.mimes' => 'Formats acceptés : jpeg, jpg, png, webp',
            'pictures.*.max' => 'Taille maximale : 5MB par image',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $uploadedPictures = [];
        $maxPosition = $hotel->pictures()->max('position') ?? -1;

        foreach ($request->file('pictures') as $index => $file) {
            $filename = time() . '_' . $index . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs("hotels/{$hotel->id}", $filename, 'public');

            $picture = $hotel->pictures()->create([
                'filepath' => $path,
                'filesize' => $file->getSize(),
                'position' => $maxPosition + $index + 1,
            ]);

            $uploadedPictures[] = $picture;
        }

        $count = count($uploadedPictures);
        $message = $count === 1 
            ? '1 photo uploadée avec succès' 
            : "{$count} photos uploadées avec succès";

        return $this->createdResponse($uploadedPictures, $message);
    }

    /**
     * Mise à jour d’une photo (seule la position peut être modifiée).
     */
    public function update(Request $request, Hotel $hotel, HotelPicture $picture): JsonResponse
    {
        if ($picture->hotel_id !== $hotel->id) {
            return $this->errorResponse(
                'Cette photo n\'appartient pas à cet hôtel',
                null,
                403
            );
        }

        $validator = Validator::make($request->all(), [
            'position' => 'required|integer|min:0',
        ], [
            'position.required' => 'La position est requise',
            'position.integer' => 'La position doit être un nombre entier',
            'position.min' => 'La position doit être supérieure ou égale à 0',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator->errors());
        }

        $picture->update(['position' => $request->position]);

        return $this->successResponse($picture, 'Position de la photo mise à jour avec succès');
    }

    /**
     * Suppression d’une photo.
     */
    public function destroy(Hotel $hotel, HotelPicture $picture): JsonResponse
    {
        if ($picture->hotel_id !== $hotel->id) {
            return $this->errorResponse(
                'Cette photo n\'appartient pas à cet hôtel',
                null,
                403
            );
        }

        $picture->delete(); // Le fichier est supprimé automatiquement via le boot() du modèle

        return $this->successResponse(null, 'Photo supprimée avec succès');
    }
}
