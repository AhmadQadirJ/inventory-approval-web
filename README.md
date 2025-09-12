composer install
cp .env.example .env
php artisan key:generate
# (Setelah ini, edit file .env untuk database)
php artisan migrate
npm install
npm run dev
php artisan storage:link
