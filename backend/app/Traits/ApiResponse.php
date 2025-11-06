<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;

/**
 * Trait pour centraliser les réponses JSON de l'API.
 * Fournit des méthodes pour succès, erreurs, validations et ressources créées.
 */
trait ApiResponse
{
    /**
     * Retourne une réponse JSON de succès.
     *
     * @param mixed $data Les données à inclure (optionnel)
     * @param string $message Message descriptif
     * @param int $code Code HTTP (par défaut 200)
     */
    protected function successResponse($data = null, string $message = '', int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'code' => $code,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Retourne une réponse JSON d'erreur.
     *
     * @param string $message Message descriptif
     * @param mixed $errors Détails des erreurs (optionnel)
     * @param int $code Code HTTP (par défaut 400)
     */
    protected function errorResponse(string $message, $errors = null, int $code = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'code' => $code,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Retourne une réponse JSON pour une ressource créée.
     *
     * @param mixed $data Les données de la ressource
     * @param string $message Message descriptif (par défaut "Ressource créée avec succès")
     */
    protected function createdResponse($data, string $message = 'Ressource créée avec succès'): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }

    /**
     * Retourne une réponse JSON pour une ressource non trouvée.
     *
     * @param string $message Message descriptif (par défaut "Ressource non trouvée")
     */
    protected function notFoundResponse(string $message = 'Ressource non trouvée'): JsonResponse
    {
        return $this->errorResponse($message, null, 404);
    }

    /**
     * Retourne une réponse JSON pour une erreur de validation.
     *
     * @param mixed $errors Détails des erreurs
     * @param string $message Message descriptif (par défaut "Erreur de validation")
     */
    protected function validationErrorResponse($errors, string $message = 'Erreur de validation'): JsonResponse
    {
        return $this->errorResponse($message, $errors, 422);
    }
}
