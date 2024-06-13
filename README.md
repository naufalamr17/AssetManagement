# MLP ASSET MANAGEMENT Application Installation Guide

This guide will walk you through the steps to install the application.

## Prerequisites

Before you begin, make sure you have the following software installed:

- Git
- Composer

## Installation Steps

1. Clone this GitHub repository:

    ```sh
    git clone https://github.com/naufalamr17/AssetManagement.git
    ```

2. Install Composer:

    ```sh
    composer install
    ```

3. Generate the .env file and application key:

    ```sh
    cp .env.example .env
    php artisan key:generate
    ```

4. Run

    ```sh
    php artisan migrate:fresh --seed
    ```

5. For excel, run this command:

    ```sh
    composer require maatwebsite/excel
    ```

6. Run the web application:

    ```sh
    php artisan serve
    ```