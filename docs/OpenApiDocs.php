<?php

namespace Docs;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "PHP API",
    version: "1.1.0",
    description: "API for User Management, Session Control, Products, and Shopping Cart operations."
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Local Development Server"
)]
#[OA\SecurityScheme(
    securityScheme: "SessionCookieAuth",
    type: "apiKey",
    name: "PHPSESSID",
    in: "cookie",
    description: "HTTP-only session cookie created after login."
)]
#[OA\Schema(
    schema: "ApiMessageResponse",
    properties: [
        new OA\Property(property: "message", type: "string", example: "Operation completed successfully.")
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "ApiErrorResponse",
    properties: [
        new OA\Property(property: "message", type: "string", example: "Request could not be completed.")
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "SessionUser",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 42),
        new OA\Property(property: "email", type: "string", format: "email", example: "<USER_EMAIL>")
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "LoginRequest",
    required: ["email", "password"],
    properties: [
        new OA\Property(property: "email", type: "string", format: "email", example: "<USER_EMAIL>"),
        new OA\Property(property: "password", type: "string", format: "password", example: "<PASSWORD>")
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "LoginResponse",
    properties: [
        new OA\Property(property: "message", type: "string", example: "Login successful."),
        new OA\Property(property: "user", ref: "#/components/schemas/SessionUser")
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "UserCreateRequest",
    required: ["first_name", "last_name", "email", "password"],
    properties: [
        new OA\Property(property: "first_name", type: "string", example: "First"),
        new OA\Property(property: "last_name", type: "string", example: "Last"),
        new OA\Property(property: "email", type: "string", format: "email", example: "<USER_EMAIL>"),
        new OA\Property(property: "password", type: "string", format: "password", example: "<PASSWORD>"),
        new OA\Property(property: "phone", type: "string", nullable: true, example: "<PHONE_NUMBER>"),
        new OA\Property(property: "date_of_birth", type: "string", format: "date", nullable: true, example: "2000-01-01")
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "UserProfile",
    properties: [
        new OA\Property(property: "first_name", type: "string", example: "First"),
        new OA\Property(property: "last_name", type: "string", example: "Last"),
        new OA\Property(property: "phone", type: "string", nullable: true, example: "<PHONE_NUMBER>"),
        new OA\Property(property: "address_line1", type: "string", nullable: true, example: "<ADDRESS_LINE_1>"),
        new OA\Property(property: "city", type: "string", nullable: true, example: "City"),
        new OA\Property(property: "province", type: "string", nullable: true, example: "Province"),
        new OA\Property(property: "postal_code", type: "string", nullable: true, example: "<POSTAL_CODE>"),
        new OA\Property(property: "date_of_birth", type: "string", format: "date", nullable: true, example: "2000-01-01")
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "UserProfileResponse",
    properties: [
        new OA\Property(property: "authenticated", type: "boolean", example: true),
        new OA\Property(property: "user", ref: "#/components/schemas/UserProfile")
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "UserUpdateRequest",
    required: ["email"],
    properties: [
        new OA\Property(
            property: "email",
            type: "string",
            format: "email",
            description: "Must match the authenticated session email.",
            example: "<USER_EMAIL>"
        ),
        new OA\Property(property: "first_name", type: "string", nullable: true, example: "First"),
        new OA\Property(property: "last_name", type: "string", nullable: true, example: "Last"),
        new OA\Property(property: "phone", type: "string", nullable: true, example: "<PHONE_NUMBER>"),
        new OA\Property(property: "address_line1", type: "string", nullable: true, example: "<ADDRESS_LINE_1>"),
        new OA\Property(property: "city", type: "string", nullable: true, example: "City"),
        new OA\Property(property: "province", type: "string", nullable: true, example: "Province"),
        new OA\Property(property: "postal_code", type: "string", nullable: true, example: "<POSTAL_CODE>"),
        new OA\Property(property: "date_of_birth", type: "string", format: "date", nullable: true, example: "2000-01-01")
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "Product",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1001),
        new OA\Property(property: "name", type: "string", example: "Product Name"),
        new OA\Property(property: "sku", type: "string", example: "SKU-001"),
        new OA\Property(property: "description", type: "string", nullable: true, example: "Product description."),
        new OA\Property(property: "price", type: "number", format: "float", example: 19.99),
        new OA\Property(property: "quantity", type: "integer", example: 25),
        new OA\Property(property: "created_at", type: "string", format: "date-time", nullable: true),
        new OA\Property(property: "updated_at", type: "string", format: "date-time", nullable: true)
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "ProductCreateRequest",
    required: ["name", "sku", "price", "quantity"],
    properties: [
        new OA\Property(property: "name", type: "string", example: "Product Name"),
        new OA\Property(property: "sku", type: "string", example: "SKU-001"),
        new OA\Property(property: "description", type: "string", nullable: true, example: "Product description."),
        new OA\Property(property: "price", type: "number", format: "float", example: 19.99),
        new OA\Property(property: "quantity", type: "integer", example: 25)
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "ProductUpdateRequest",
    required: ["id"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1001),
        new OA\Property(property: "name", type: "string", nullable: true, example: "Updated Product Name"),
        new OA\Property(property: "sku", type: "string", nullable: true, example: "SKU-001"),
        new OA\Property(property: "description", type: "string", nullable: true, example: "Updated product description."),
        new OA\Property(property: "price", type: "number", format: "float", nullable: true, example: 24.99),
        new OA\Property(property: "quantity", type: "integer", nullable: true, example: 20)
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "ProductDeleteRequest",
    required: ["id"],
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1001)
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "ProductListResponse",
    properties: [
        new OA\Property(
            property: "products",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/Product")
        )
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "CartItem",
    properties: [
        new OA\Property(property: "product_id", type: "integer", example: 1001),
        new OA\Property(property: "name", type: "string", example: "Product Name"),
        new OA\Property(property: "sku", type: "string", example: "SKU-001"),
        new OA\Property(property: "price", type: "number", format: "float", example: 19.99),
        new OA\Property(property: "quantity", type: "integer", example: 2),
        new OA\Property(property: "subtotal", type: "number", format: "float", example: 39.98)
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "CartMutationRequest",
    required: ["product_id", "quantity"],
    properties: [
        new OA\Property(property: "product_id", type: "integer", example: 1001, description: "Target product identifier to add or adjust."),
        new OA\Property(property: "quantity", type: "integer", example: 2, description: "Quantity requested.")
    ],
    type: "object"
)]
#[OA\Schema(
    schema: "CartViewResponse",
    properties: [
        new OA\Property(property: "cart_id", type: "integer", example: 5001),
        new OA\Property(
            property: "items",
            type: "array",
            items: new OA\Items(ref: "#/components/schemas/CartItem")
        ),
        new OA\Property(property: "grand_total", type: "number", format: "float", example: 39.98)
    ],
    type: "object"
)]
class OpenApiDocs
{
    // =========================
    // SESSIONS: LOGIN
    // =========================
    #[OA\PathItem(path: "/v1/sessions/login.php")]
    #[OA\Post(
        summary: "User login",
        description: "Authenticates a user and creates an HTTP-only session cookie.",
        tags: ["Sessions"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/LoginRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Login successful. Session cookie is set.",
                content: new OA\JsonContent(ref: "#/components/schemas/LoginResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Incomplete data.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Invalid credentials.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            )
        ]
    )]
    public function login(): void
    {
    }

    // =========================
    // SESSIONS: LOGOUT
    // =========================
    #[OA\PathItem(path: "/v1/sessions/logout.php")]
    #[OA\Post(
        summary: "User logout",
        description: "Destroys the active session and clears the session cookie.",
        tags: ["Sessions"],
        security: [["SessionCookieAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Logout successful.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            )
        ]
    )]
    public function logout(): void
    {
    }

    // =========================
    // USERS: CREATE
    // =========================
    #[OA\PathItem(path: "/v1/users/create.php")]
    #[OA\Post(
        summary: "Register a new user",
        description: "Creates a new user profile.",
        tags: ["Users"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/UserCreateRequest")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "User created.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Invalid or incomplete request data.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 409,
                description: "Email already exists.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            )
        ]
    )]
    public function createUser(): void
    {
    }

    // =========================
    // USERS: VIEW PROFILE
    // =========================
    #[OA\PathItem(path: "/v1/users/view.php")]
    #[OA\Get(
        summary: "View active user profile",
        description: "Retrieves current user details from the database based on the active session.",
        tags: ["Users"],
        security: [["SessionCookieAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Authenticated profile data retrieved.",
                content: new OA\JsonContent(ref: "#/components/schemas/UserProfileResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Unauthenticated. No active session.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 404,
                description: "User record no longer exists.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            )
        ]
    )]
    public function viewUser(): void
    {
    }

    // =========================
    // USERS: UPDATE
    // =========================
    #[OA\PathItem(path: "/v1/users/update.php")]
    #[OA\Put(
        summary: "Update existing user",
        description: "Updates profile fields for the authenticated user.",
        tags: ["Users"],
        security: [["SessionCookieAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/UserUpdateRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Profile updated successfully.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Invalid request data.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Not logged in.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "Forbidden. Email mismatch with session.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            )
        ]
    )]
    public function updateUser(): void
    {
    }

    // =========================
    // PRODUCTS: ADD
    // =========================
    #[OA\PathItem(path: "/v1/products/add.php")]
    #[OA\Post(
        summary: "Add a product",
        description: "Adds a new product to the inventory. Intended for administrative inventory management.",
        tags: ["Products"],
        security: [["SessionCookieAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/ProductCreateRequest")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Product created.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Invalid or incomplete product data.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Authentication required.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "Administrative access required.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 409,
                description: "Product SKU already exists.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            )
        ]
    )]
    public function addProduct(): void
    {
    }

    // =========================
    // PRODUCTS: VIEW
    // =========================
    #[OA\PathItem(path: "/v1/products/view.php")]
    #[OA\Get(
        summary: "View products",
        description: "Retrieves products available in the inventory.",
        tags: ["Products"],
        parameters: [
            new OA\Parameter(
                name: "id",
                description: "Optional product ID filter.",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "sku",
                description: "Optional SKU filter.",
                in: "query",
                required: false,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Products retrieved.",
                content: new OA\JsonContent(ref: "#/components/schemas/ProductListResponse")
            ),
            new OA\Response(
                response: 404,
                description: "No products found.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            )
        ]
    )]
    public function viewProducts(): void
    {
    }

    // =========================
    // PRODUCTS: UPDATE
    // =========================
    #[OA\PathItem(path: "/v1/products/update.php")]
    #[OA\Put(
        summary: "Update a product",
        description: "Updates product information such as price, description, or inventory quantity.",
        tags: ["Products"],
        security: [["SessionCookieAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/ProductUpdateRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Product updated.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Invalid request data.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Authentication required.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "Administrative access required.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 404,
                description: "Product not found.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            )
        ]
    )]
    public function updateProduct(): void
    {
    }

    // =========================
    // PRODUCTS: DELETE
    // =========================
    #[OA\PathItem(path: "/v1/products/delete.php")]
    #[OA\Delete(
        summary: "Delete a product",
        description: "Removes a product from the inventory.",
        tags: ["Products"],
        security: [["SessionCookieAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/ProductDeleteRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Product deleted.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Invalid request data.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Authentication required.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 403,
                description: "Administrative access required.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 404,
                description: "Product not found.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            )
        ]
    )]
    public function deleteProduct(): void
    {
    }

    // =========================
    // CART: VIEW
    // =========================
    #[OA\PathItem(path: "/v1/cart/view.php")]
    #[OA\Get(
        summary: "View active shopping cart",
        description: "Retrieves the authenticated user's active cart items and calculated grand total.",
        tags: ["Cart"],
        security: [["SessionCookieAuth" => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: "Cart retrieved.",
                content: new OA\JsonContent(ref: "#/components/schemas/CartViewResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Access denied. Login is required to view cart items.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error while retrieving cart data.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            )
        ]
    )]
    public function viewCart(): void
    {
    }

    // =========================
    // CART: ADD ITEM
    // =========================
    #[OA\PathItem(path: "/v1/cart/add.php")]
    #[OA\Post(
        summary: "Add item to shopping cart",
        description: "Appends an item amount to the active cart. Increments quantity sequentially if the item already exists.",
        tags: ["Cart"],
        security: [["SessionCookieAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/CartMutationRequest")
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Product entry appended successfully.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Invalid request payload layout.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Access denied. Login required.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 404,
                description: "Product ID not found in system catalog.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 422,
                description: "Unprocessable volume requests exceeding stock volumes.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Internal database handshake failure.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            )
        ]
    )]
    public function addToCart(): void
    {
    }

    // =========================
    // CART: UPDATE QUANTITY
    // =========================
    #[OA\PathItem(path: "/v1/cart/update.php")]
    #[OA\Patch(
        summary: "Update cart item volume",
        description: "Modifies the explicit total volume of an item already located within the customer's active cart container.",
        tags: ["Cart"],
        security: [["SessionCookieAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: "#/components/schemas/CartMutationRequest")
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Item quantities updated successfully.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Invalid structural formats passed up.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Access denied. Login required.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 404,
                description: "Target item product combination does not exist inside your active session footprint.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 422,
                description: "Modified parameters exceed warehouse inventory.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Internal data storage engine exception error.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            )
        ]
    )]
    public function updateCartQuantity(): void
    {
    }

    // =========================
    // CART: REMOVE ITEM
    // =========================
    #[OA\PathItem(path: "/v1/cart/remove.php")]
    #[OA\Delete(
        summary: "Purge product asset from active cart container",
        description: "Completely destroys a item line row out of the user's shopping cart space using target query boundaries.",
        tags: ["Cart"],
        security: [["SessionCookieAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "product_id",
                description: "Numeric product identifier primary key.",
                in: "query",
                required: true,
                schema: new OA\Schema(type: "integer", example: 1001)
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Line item wiped cleanly.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiMessageResponse")
            ),
            new OA\Response(
                response: 400,
                description: "Bad Request: Parameter missing or invalid formatting types passed.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 401,
                description: "Access denied. Authentication mandatory.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 404,
                description: "Resource not found within your active cart footprint mapping layers.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            ),
            new OA\Response(
                response: 500,
                description: "Internal database query processing error.",
                content: new OA\JsonContent(ref: "#/components/schemas/ApiErrorResponse")
            )
        ]
    )]
    public function removeCartItem(): void
    {
    }
}