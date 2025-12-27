php artisan migrate
php artisan migrate:fresh // drops everything, only use in dev

composer run dev

php artisan make:controller UserController
php artisan make:controller PostController
php artisan make:migration create_posts_table
php artisan make:model Post
php artisan make:component Nav // .blade.php file and php Class
php artisan make:component Nav/NavTop --view // only .blade.php file, nested within views/components/nav/

composer require laravel/horizon
php artisan horizon:install
composer.json:
"dev": [
"Composer\\Config::disableProcessTimeout",
"npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74,#a78bfa\" \"php artisan serve\" \"php artisan queue:listen --tries=1\" \"php artisan pail --timeout=0\" \"php artisan horizon\" \"npm run dev\" --names=server,queue,logs,horizon,vite --kill-others"
],
php artisan horizon

php artisan make:job

php artisan livewire:make Projects/Index
php artisan livewire:make Projects/FormModal

php artisan make:class Services/ProjectService
