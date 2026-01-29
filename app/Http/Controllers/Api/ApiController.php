<?php

namespace App\Http\Controllers\Api;

use OpenAPI\Annotations as OA;

/**
 * @OA\Info(
 *     title="TeamCore API",
 *     version="1.0.0",
 *     description="REST API for TeamCore HR Management System",
 *     contact={
 *         "name": "TeamCore Support",
 *         "url": "https://teamcore.local",
 *         "email": "support@teamcore.local"
 *     }
 * )
 * @OA\Server(
 *     url="http://localhost:8000/api/v1",
 *     description="Development Server"
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth"
 * )
 */
class ApiController
{
    // Base controller for API documentation
}
