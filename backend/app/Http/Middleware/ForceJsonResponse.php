<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ForceJsonResponse
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        try {
            return $next($request);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'code' => 422,
                'message' => 'Erreur de validation',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => 'Ressource non trouvée',
            ], 404);
        } catch (NotFoundHttpException $e) {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => 'Endpoint non trouvé',
            ], 404);
        } catch (Throwable $e) {
            $statusCode = ($e instanceof HttpExceptionInterface)
                ? $e->getStatusCode()
                : 500;

            return response()->json([
                'success' => false,
                'code' => $statusCode,
                'message' => config('app.debug') ? $e->getMessage() : 'Une erreur est survenue',
            ], $statusCode);
        }
    }
}
