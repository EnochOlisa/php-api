# php-api
A repo with different resources, all built with PHP.

## Description
This repository contains various PHP resources, including APIs, libraries, and tools.

## Resources
- **Users**: Has 3 endpoints; create.php, view.php, and update.php. 
- The create.php endpoint allows a user create a profile.
- The view.php endpoint allows the user to view their profile information.
- The update.php endpoint allows the user to update their information, such as their name and password.

- **Sessions**: Has 2 endpoints; login.php and logout.php.
- The login.php endpoint allows a user to log in to their account.
- The logout.php endpoint allows a user to log out of their account.

## Swagger/OpenAPI documentation 
- Clone the repository.
- Set up the project, configure the database connection, server, and ensure that all dependencies are installed.
- Navigate to the project directory and run the following command to generate the Swagger documentation:
- Via terminal, install the dependency using composer: composer require zircote/swagger-php
- Check that it is installed by running: composer show zircote/swagger-php
- Still inside the root folder, via terminal, run this: vendor\bin\openapi docs -o openapi.yaml
- Start your local server and navigate to http://localhost:8000/swagger-ui.html to view the Swagger documentation.


