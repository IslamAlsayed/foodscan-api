create model
php artisan make:model 'model name' --migration

create factory
php artisan make:factory 'model name'Factory --model='model name'

create seeder
php artisan make:seed 'model name'Seeder

run seeder
php artisan db:seed --class='seeder name'
