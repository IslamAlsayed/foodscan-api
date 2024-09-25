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

```bash
cp .env.example .env
```

### open .env and editing

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=foodscan_api_2
DB_USERNAME=root
DB_PASSWORD=
```

## 3. Migrate Tables and Seed Data

```bash
php artisan migrate --seed
```

### or

```bash
php artisan migrate:refresh --seed
```

## 4. Generate JWT Secret Key

```bash
php artisan jwt:secret
```

## 4. Generate Application Key

```bash
php artisan key:generate
```

## 5. Mail Configuration

### open .env and editing

```bash
MAIL_HOST=mailpit
```

### to

```bash
MAIL_HOST=localhost
```

## 6. Running the Application

```bash
php artisan serve
```
