# Matat Task - Laravel WooCommerce Integration

This Laravel application is designed to fetch orders from a WooCommerce store and store them in a database. It also provides a REST API for listing orders with support for search, filtering, sorting, and pagination, as well as syncing new and updated orders.

## Requirements

- PHP >= 8.1
- MySQL or any other database supported by Laravel
- Composer

## Installation

1. Clone the repository:

   ```bash
    git clone https://github.com/your-username/matat-task.git
   
    cd matat-task
   
    composer install
   
    cp .env.example .env
   
    php artisan key:generate
   
    php artisan migrate
   
    php artisan serve

## Usage
- To sync new and updated orders from the WooCommerce API, run the following command:
  ```bash
  php artisan orders:sync
  
This command is scheduled to run daily at 12 PM using Laravel's task scheduling.

- To delete orders that has not been updated since last 3 months, run the following command:
  ```bash
  php artisan orders:delete-old

This command is scheduled to run weekly using Laravel's task scheduling. On live environment you have to run cron jobs to schedule the commands.

- To access the REST API endpoints, you can use tools like Postman or cURL. The available endpoints are:

    - GET `/api/orders`: Retrieve a list of orders with support for search, filtering, sorting, and pagination.
    - POST `/api/orders/sync`: Sync new and updated orders from the WooCommerce API.
