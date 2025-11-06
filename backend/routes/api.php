<?php

use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\HotelPictureController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Routes pour l'API de gestion des hôtels et de leurs photos.
| Les routes sont regroupées sous le préfixe /hotels.
|
*/

// Récupérer les informations de l'utilisateur authentifié
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Routes pour les hôtels
Route::prefix('hotels')->group(function () {

    // Liste des hôtels avec filtres, tri et pagination
    Route::get('/', [HotelController::class, 'index']);

    // Recherche d'hôtels par nom, ville, pays ou adresse
    Route::get('/search', [HotelController::class, 'search']);

    // Détail d'un hôtel spécifique
    Route::get('/{hotel}', [HotelController::class, 'show']);

    // Création d'un nouvel hôtel
    Route::post('/', [HotelController::class, 'store']);

    // Mise à jour d'un hôtel existant
    Route::match(['put', 'patch'], '/{hotel}', [HotelController::class, 'update']);

    // Suppression d'un hôtel
    Route::delete('/{hotel}', [HotelController::class, 'destroy']);

    // Routes pour les photos d'hôtel
    Route::post('/{hotel}/pictures', [HotelPictureController::class, 'store']);       // Upload de photo(s)
    Route::patch('/{hotel}/pictures/{picture}', [HotelPictureController::class, 'update']); // Mise à jour de la position d'une photo
    Route::delete('/{hotel}/pictures/{picture}', [HotelPictureController::class, 'destroy']); // Suppression d'une photo
});
