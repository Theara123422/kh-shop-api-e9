# Laravel Project

Welcome to the **Laravel Project**! This guide will help you set up the project on your local machine and contribute effectively.

## 📋 Prerequisites

Ensure you have the following installed on your system:

- PHP >= 8.2
- Composer >= 2.x
- MySQL Database
- Node.js (for frontend assets) >= 18.x
- Git
- Wampp Server

## 🚀 Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/Theara123422/kh-shop-api-e9
cd repo-name
```

### 2. Set Up Environment

1. Copy the example environment file:

```bash
cp .env.example .env
```

2. Update the `.env` file with your local configuration:

```bash
APP_NAME=LaravelApp
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kh-shop-api-db
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Install Dependencies

```bash
composer install
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Set Up the Database

1. Ensure your database is created and configured.
2. Run migrations and seeders:

```bash
php artisan migrate
```

### 6. Compile Frontend Assets (if applicable)

```bash
npm run dev
```

### 7. Run the Application

```bash
php artisan serve
```

Visit [http://localhost:8000](http://localhost:8000) in your browser.

## 🛠️ Project Structure

```
├── app/            # Application logic (Models, Controllers, Services)
├── bootstrap/      # Framework bootstrapping
├── config/         # Configuration files
├── database/       # Migrations, Seeders, Factories
├── public/         # Public assets (CSS, JS, Images)
├── resources/      # Blade templates, JS, and CSS
├── routes/         # Application routes (web.php, api.php)
├── storage/        # Logs, Cache, and Uploaded files
└── tests/          # Unit and Feature tests
```

## 🧪 Running Tests

Ensure tests pass before pushing changes:

```bash
php artisan test
```

## 📤 Contributing

Follow these steps to contribute:

1. Create a feature branch:

```bash
git checkout -b feature/your-feature
```

2. Commit your changes:

```bash
git commit -m "Add: Your feature description"
```

3. Push to GitHub:

```bash
git push origin feature/your-feature
```

4. Open a pull request (PR) against the `develop` branch.

## 📦 Deployment

1. Ensure your `.env` file is properly configured.
2. Run migrations on the production server:

```bash
php artisan migrate --force
```

3. Optimize for production:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [PHP Documentation](https://www.php.net/docs.php)

## 📄 License

This project is licensed under the **MIT License**.

