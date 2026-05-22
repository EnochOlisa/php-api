# php-api
A repo with different resources, all built with PHP.

## Description
This repository contains various PHP resources, including APIs, libraries, and tools.

## Resources for User and Session Management
### Users: Has 3 endpoints; create.php, view.php, and update.php. 
- The create.php endpoint allows a user create a profile.
- The view.php endpoint allows the user to view their profile information.
- The update.php endpoint allows the user to update their information, such as their name and password.

### Sessions: Has 2 endpoints; login.php and logout.php.
- The login.php endpoint allows a user to log in to their account.
- The logout.php endpoint allows a user to log out of their account.

## Resources for E-commerce Management
### Products: Has 4 endpoints; add.php, view.php, update.php, and delete.php.
- The add.php endpoint allows an admin to add a product to the inventory.
- The view.php endpoint allows users to view the products available in the inventory.
- The update.php endpoint allows an admin to update the product information, such as price and description. Also, when a user makes an order or does a refund, this endpoint is called.
- The delete.php endpoint allows an admin to remove a product from the inventory.

### Carts: Has 4 endpoints; add.php, view.php, update.php, and remove.php.
- The add.php endpoint allows a user to add a product to their cart.
- The view.php endpoint allows users to view the products in their cart.
- The update.php endpoint allows a user to update the quantity of a product in their cart.
- The remove.php endpoint allows a user to remove a product from their cart.

## Swagger/OpenAPI documentation 
- Clone the repository.
- Set up the project, configure the database connection, server, and ensure that all dependencies are installed.
- Navigate to the project directory and run the following command to generate the Swagger documentation:
- Via terminal, install the dependency using composer: composer require zircote/swagger-php
- Check that it is installed by running: composer show zircote/swagger-php
- Still inside the root folder, via terminal, run this: vendor\bin\openapi docs -o openapi.yaml
- Start your local server and navigate to http://localhost:8000/swagger-ui.html to view the Swagger documentation.


