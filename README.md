# Clone the repository
git clone https://github.com/yourusername/repo-name.git

# Navigate into the project directory
cd repo-name

# Install dependencies
composer install

# Copy environment file and set up
cp .env.example .env
php artisan key:generate

# Set up database (update .env with credentials)
php artisan migrate --seed

# Serve the application
php artisan serve
