# FoodScan API

This is the backend API for the FoodScan project. Follow the steps below to set up and run the project.

## 1. Cloning the Repository

First, clone the repository to your local machine:

```bash
git clone https://github.com/IslamAlsayed/foodScan_api.git
cd foodScan_api
```

## 2. Install Dependencies

```bash
composer update
```

## 3. Set Up Environment File

### Create a .env file and set up your database configuration

```bash
cp .env.example .env
```

### 4. Edit the .env file

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=foodscan_api_2
DB_USERNAME=root
DB_PASSWORD=
```

## 5. Migrate Tables and Seed Data

### Run the following command to migrate the database tables and seed the initial data

```bash
php artisan migrate --seed
```

### Or refresh and seed in one step

```bash
php artisan migrate:refresh --seed
```

## 6. Generate JWT Secret Key

### Generate the JWT secret key

```bash
php artisan jwt:secret
```

## 7. Generate Application Key

### Generate the application key:

```bash
php artisan key:generate
```

## 8. Mail Configuration

### Change the following line in the .env file

```bash
MAIL_HOST=mailpit
```

### to

```bash
MAIL_HOST=localhost
```

## 9. Running the Application

### You can now run the application using the built-in PHP server

```bash
php artisan serve
```

## 10. Running the Application

### The API should now be running at http://localhost:8000

### You can now run the application using the built-in PHP server

```bash
php artisan serve
```
