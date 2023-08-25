<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];
    public function register()
    {
        $this->renderable(function (Exception $e, $request) {
            return $this->handleException($request, $e);
        });
    }
    public function handleException($request, Exception $exception)
    {
        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            return response()->json(["res" => false, "message" => "Error de autorización, no tiene permisos"], 403);
        }
        if ($exception instanceof RouteNotFoundException) {
            return response()->json(["res" => false, "message" => "Error de autenticación"], 401);
        }
        if($exception instanceof HttpException){
            return response()->json(["res" => false, "message" => "Error de ruta"], 404);
        }
        if ($exception instanceof AuthorizationException) {
            return response()->json(["res" => false, "message" => "Error de autorización, no tiene permisos"], 403);
        }
    }
}
