# (EMP) - Laravel Project

Welcome to the (EMP). This Laravel-based project is designed to manage data with authentication, middleware-based access control, data import from Excel, and manual data entry. It also includes a feature to send email notifications to users.

## Table of Contents

-   [Installation](#installation)
-   [Environment Setup](#environment-setup)
-   [Authentication](#authentication)
-   [Middleware Access Control](#middleware-access-control)
-   [Data Import from Excel](#data-import-from-excel)
-   [Manual Data Entry](#manual-data-entry)
-   [Mailing to Users](#mailing-to-users)
-   [Contributing](#contributing)

## Installation

To get started with this project, follow these steps:

1. **Clone the Repository**  
   Clone this project to your local environment using Git:

    ```bash
    git clone https://github.com/ParthGuptaZignuts/emp-laravel.git

    Install all the dependencies using composer

     composer install
    ```

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

set the database connection in .env file

    DB_PASSWORD=YOUR_DB_PASSWORD

Generate a new application key

    php artisan key:generate

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Run the database seeder and you're done

    php artisan db:seed

Link storage

    php artisan storage:link

Start the local development server

    php artisan serve
# EMP-backend-
