<?php

namespace App\Swagger;

/**
 * @OA\Info(
 *   title="Restaurante API",
 *   version="1.0.0",
 *   description="Documentación del backend (Laravel + Swagger)."
 * )
 *
 * @OA\Server(
 *   url=L5_SWAGGER_CONST_HOST,
 *   description="Servidor local"
 * )
 * @OA\Tag(
 *   name="Usuarios",
 *   description="CRUD de usuarios"
 * )
 * @OA\Tag(
 *   name="Menu Items",
 *   description="CRUD de ítems del menú"
 * )
 * @OA\Tag(
 *   name="Menú",
 *   description="Endpoints para visualizar el menú"
 * )
 */
class OpenApi {}
