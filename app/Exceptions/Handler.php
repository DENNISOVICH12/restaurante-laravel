<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
public function render($request, \Throwable $e)
{
    // Para rutas de API forzamos JSON siempre
    if ($request->is('api/*')) {

        // 422 - Validación
        if ($e instanceof \Illuminate\Validation\ValidationException) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors'  => $e->errors(),
            ], 422);
        }

        // 404 - Modelo no encontrado
        if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            return response()->json([
                'error' => ['code' => 404, 'message' => 'Recurso no encontrado']
            ], 404);
        }

        // 404 - Ruta no encontrada
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return response()->json([
                'error' => ['code' => 404, 'message' => 'Ruta no encontrada']
            ], 404);
        }

        // 405 - Método no permitido
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            return response()->json([
                'error' => ['code' => 405, 'message' => 'Método no permitido']
            ], 405);
        }

        // 401 - No autenticado (si luego usas tokens)
        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            return response()->json([
                'error' => ['code' => 401, 'message' => 'No autenticado']
            ], 401);
        }

        // 500 - Genérico
        return response()->json([
            'error' => ['code' => 500, 'message' => 'Error interno del servidor']
        ], 500);
    }

    // Para rutas no-API, comportamiento por defecto (HTML)
    return parent::render($request, $e);
}

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
