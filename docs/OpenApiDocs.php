<?php
namespace Docs;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "PHP API",
    version: "1.0.0",
    description: "User management API"
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Local server"
)]
class OpenApiDocs
{
    // =========================
    // CREATE USER
    // =========================
    #[OA\PathItem(path: "/v1/users/create.php")]
    #[OA\Post(
        summary: "Create a new user",
        tags: ["Users"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["first_name", "last_name", "email", "password"],
                properties: [
                    new OA\Property(property: "first_name", type: "string", example: "Enoch"),
                    new OA\Property(property: "last_name", type: "string", example: "Olisa"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "enoch@example.com"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "StrongPassword123!"),
                    new OA\Property(property: "phone", type: "string", nullable: true, example: "+1 647 123 4567"),
                    new OA\Property(property: "address_line1", type: "string", nullable: true, example: "123 Main Street"),
                    new OA\Property(property: "city", type: "string", nullable: true, example: "Milton"),
                    new OA\Property(property: "province", type: "string", nullable: true, example: "ON"),
                    new OA\Property(property: "postal_code", type: "string", nullable: true, example: "K1A 0B1"),
                    new OA\Property(property: "date_of_birth", type: "string", format: "date", nullable: true, example: "1990-05-20")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "User created successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "User was created successfully.")
                    ]
                )
            ),
            new OA\Response(
                response: 409,
                description: "Email already exists",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unable to create user. Email already exists.")
                    ]
                )
            ),
            new OA\Response(
                response: 503,
                description: "Service unavailable",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Unable to create user. Service is currently unavailable.")
                    ]
                )
            )
        ]
    )]
    public function createUser() {}

    // =========================
    // UPDATE USER
    // =========================
    #[OA\PathItem(path: "/v1/users/update.php")]
    #[OA\Put(
        summary: "Update an existing user",
        tags: ["Users"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "enoch@example.com"),
                    new OA\Property(property: "first_name", type: "string", nullable: true, example: "Enoch"),
                    new OA\Property(property: "last_name", type: "string", nullable: true, example: "Olisa"),
                    new OA\Property(property: "phone", type: "string", nullable: true, example: "+1 647 123 4567"),
                    new OA\Property(property: "address_line1", type: "string", nullable: true, example: "123 Main Street"),
                    new OA\Property(property: "city", type: "string", nullable: true, example: "Milton"),
                    new OA\Property(property: "province", type: "string", nullable: true, example: "ON"),
                    new OA\Property(property: "postal_code", type: "string", nullable: true, example: "K1A 0B1"),
                    new OA\Property(property: "date_of_birth", type: "string", format: "date", nullable: true, example: "1990-05-20")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "User updated successfully",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "User updated successfully.")
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: "Bad request",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "No valid updateable fields were provided.")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "User does not exist",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "User does not exist.")
                    ]
                )
            )
        ]
    )]
    public function updateUser() {}
}