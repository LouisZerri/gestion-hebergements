<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Gestion spécifique pour les routes API
        if ($request->is('api/*') || $request->expectsJson()) {
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Gérer les exceptions pour les routes API avec des réponses JSON cohérentes
     */
    protected function handleApiException($request, Throwable $exception): JsonResponse
    {
        // Erreur de validation
        if ($exception instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'code' => 422,
                'message' => 'Erreur de validation',
                'errors' => $exception->errors(),
            ], 422);
        }

        // Modèle non trouvé (Hotel, HotelPicture, etc.)
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => 'Ressource non trouvée',
            ], 404);
        }

        // Route non trouvée
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => 'Endpoint non trouvé',
            ], 404);
        }

        // Méthode HTTP non autorisée
        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'success' => false,
                'code' => 405,
                'message' => 'Méthode HTTP non autorisée',
            ], 405);
        }

        // Erreur serveur générique
        $statusCode = $exception instanceof HttpExceptionInterface
            ? $exception->getStatusCode()
            : 500;

        return response()->json([
            'success' => false,
            'code' => $statusCode,
            'message' => config('app.debug') 
                ? $exception->getMessage() 
                : 'Une erreur est survenue',
        ], $statusCode);
    }
}