<?php
namespace Docs;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "PHP API",
    version: "1.0.0",
    description: "API for User Management and Session Control (2026 Standards)"
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Local Development Server"
)]
class OpenApiDocs
{
    // =========================
    // SESSIONS: LOGIN
    // =========================
    #[OA\PathItem(path: "/v1/sessions/login.php")]
    #[OA\Post(
        summary: "User Login",
        tags: ["Sessions"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", example: "enoch@example.com"),
                    new OA\Property(property: "password", type: "string", example: "StrongPassword123!")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login successful. Sets HTTP-only session cookie.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Login successful."),
                        new OA\Property(property: "user", type: "object", properties: [
                            new OA\Property(property: "id", type: "integer", example: 42),
                            new OA\Property(property: "email", type: "string", example: "enoch@example.com")
                        ])
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Invalid credentials"),
            new OA\Response(response: 400, description: "Incomplete data")
        ]
    )]
    public function login() {}

    // =========================
    // SESSIONS: LOGOUT
    // =========================
    #[OA\PathItem(path: "/v1/sessions/logout.php")]
    #[OA\Post(
        summary: "User Logout",
        tags: ["Sessions"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Session destroyed and cookie cleared.",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Logout successful.")
                    ]
                )
            )
        ]
    )]
    public function logout() {}

    // =========================
    // USERS: CREATE
    // =========================
    #[OA\PathItem(path: "/v1/users/create.php")]
    #[OA\Post(
        summary: "Register a new user",
        tags: ["Users"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["first_name", "last_name", "email", "password"],
                properties: [
                    new OA\Property(property: "first_name", type: "string"),
                    new OA\Property(property: "last_name", type: "string"),
                    new OA\Property(property: "email", type: "string", format: "email"),
                    new OA\Property(property: "password", type: "string", format: "password"),
                    new OA\Property(property: "phone", type: "string", nullable: true),
                    new OA\Property(property: "date_of_birth", type: "string", format: "date", nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "User created"),
            new OA\Response(response: 409, description: "Email already exists")
        ]
    )]
    public function createUser() {}

    // =========================
    // USERS: VIEW (PROFILE)
    // =========================
    #[OA\PathItem(path: "/v1/users/view.php")]
    #[OA\Get(
        summary: "View active user profile",
        description: "Retrieves current user details from DB based on active session email.",
        tags: ["Users"],
        responses: [
            new OA\Response(
                response: 200,
                description: "Authenticated profile data retrieved",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "authenticated", type: "boolean", example: true),
                        new OA\Property(property: "user", type: "object", properties: [
                            new OA\Property(property: "first_name", type: "string"),
                            new OA\Property(property: "last_name", type: "string"),
                            new OA\Property(property: "phone", type: "string", nullable: true),
                            new OA\Property(property: "address_line1", type: "string", nullable: true),
                            new OA\Property(property: "city", type: "string", nullable: true),
                            new OA\Property(property: "province", type: "string", nullable: true),
                            new OA\Property(property: "postal_code", type: "string", nullable: true),
                            new OA\Property(property: "date_of_birth", type: "string", format: "date", nullable: true)
                        ])
                    ]
                )
            ),
            new OA\Response(response: 401, description: "Unauthenticated - No active session"),
            new OA\Response(response: 404, description: "User record no longer exists in DB")
        ]
    )]
    public function viewUser() {}

    // =========================
    // USERS: UPDATE
    // =========================
    #[OA\PathItem(path: "/v1/users/update.php")]
    #[OA\Put(
        summary: "Update existing user",
        tags: ["Users"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", description: "Must match session email"),
                    new OA\Property(property: "first_name", type: "string", nullable: true),
                    new OA\Property(property: "last_name", type: "string", nullable: true),
                    new OA\Property(property: "phone", type: "string", nullable: true),
                    new OA\Property(property: "address_line1", type: "string", nullable: true),
                    new OA\Property(property: "city", type: "string", nullable: true),
                    new OA\Property(property: "province", type: "string", nullable: true),
                    new OA\Property(property: "postal_code", type: "string", nullable: true),
                    new OA\Property(property: "date_of_birth", type: "string", format: "date", nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Profile updated successfully"),
            new OA\Response(response: 401, description: "Not logged in"),
            new OA\Response(response: 403, description: "Forbidden - Email mismatch with session")
        ]
    )]
    public function updateUser() {}
}