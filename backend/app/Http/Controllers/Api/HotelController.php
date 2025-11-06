<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HotelRequest;
use App\Models\Hotel;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Contrôleur API pour la gestion des hôtels.
 * Utilise le trait ApiResponse pour des réponses JSON uniformes.
 */
class HotelController extends Controller
{
    use ApiResponse;

    /**
     * Retourne la liste des hôtels avec filtres, tri et pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Hotel::with('pictures');

        // Filtres optionnels
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->has('country')) {
            $query->where('country', 'like', '%' . $request->country . '%');
        }

        if ($request->has('min_price')) {
            $query->where('price_per_night', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price_per_night', '<=', $request->max_price);
        }

        if ($request->has('min_capacity')) {
            $query->where('max_capacity', '>=', $request->min_capacity);
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Validation des champs de tri autorisés
        $allowedSortFields = ['name', 'city', 'price_per_night', 'max_capacity', 'created_at'];
        if (in_array($sortBy, $allowedSortFields)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination (max 100 par page)
        $perPage = min($request->get('per_page', 15), 100);
        $hotels = $query->paginate($perPage);

        return $this->successResponse($hotels, 'Liste des hôtels récupérée avec succès');
    }

    /**
     * Crée un nouvel hôtel.
     */
    public function store(HotelRequest $request): JsonResponse
    {
        $hotel = Hotel::create($request->validated());
        $hotel->load('pictures');

        return $this->createdResponse($hotel, 'Hôtel créé avec succès');
    }

    /**
     * Affiche un hôtel spécifique avec ses photos.
     */
    public function show(Hotel $hotel): JsonResponse
    {
        $hotel->load('pictures');
        
        return $this->successResponse($hotel, 'Détails de l\'hôtel récupérés avec succès');
    }

    /**
     * Met à jour un hôtel existant.
     */
    public function update(HotelRequest $request, Hotel $hotel): JsonResponse
    {
        $hotel->update($request->validated());
        $hotel->load('pictures');

        return $this->successResponse($hotel, 'Hôtel mis à jour avec succès');
    }

    /**
     * Supprime un hôtel.
     */
    public function destroy(Hotel $hotel): JsonResponse
    {
        $hotelName = $hotel->name;
        $hotel->delete();

        return $this->successResponse(null, "L'hôtel \"{$hotelName}\" a été supprimé avec succès");
    }

    /**
     * Recherche des hôtels par nom, ville, pays ou adresse.
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->get('q', '');
        
        if (empty($search)) {
            return $this->errorResponse('Le paramètre de recherche "q" est requis', null, 400);
        }

        $hotels = Hotel::with('pictures')
            ->where(function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%")
                    ->orWhere('address_1', 'like', "%{$search}%");
            })
            ->paginate(15);

        return $this->successResponse($hotels, "Résultats de recherche pour \"{$search}\"");
    }
}
