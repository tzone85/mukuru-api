# Mukuru API

A Laravel-based currency exchange API that handles currency conversions and order management.

## Requirements

- PHP >= 8.1
- MySQL >= 5.7
- Composer
- Node.js & NPM (for frontend assets)

## Features

- Currency listing and details
- Currency conversion (USD to foreign currency and vice versa)
- Order management with surcharge and discount calculations
- RESTful API endpoints

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/mukuru-api.git
cd mukuru-api
```

2. Install PHP dependencies:
```bash
composer install
```

3. Environment Setup:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your `.env` file with your database settings:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mukurudb
DB_USERNAME=root
DB_PASSWORD=
```

5. Create the database:
```bash
mysql -u root -e "CREATE DATABASE mukurudb"
```

6. Run migrations and seed the database:
```bash
php artisan migrate --seed
```

## Running the Application

Start the development server:
```bash
php artisan serve
```

The API will be available at `http://127.0.0.1:8000`

## API Endpoints

### Currencies
- `GET /api/currencies` - List all currencies
- `GET /api/currencies/{id}` - Get specific currency details

### Currency Conversion
- `POST /api/get-foreign-currency-amount` - Convert USD to foreign currency
- `POST /api/get-total-amount` - Convert foreign currency to USD

### Orders
- `POST /api/orders` - Create a new currency exchange order

## Example API Usage

### Create an Order
```bash
curl -X POST http://127.0.0.1:8000/api/orders \
  -H "Content-Type: application/json" \
  -d '{
    "currency": "ZAR",
    "foreign_currency_amount": 1000,
    "total_amount": 75.1574
  }'
```

## Available Currencies

- ZAR (South African Rand)
- GBP (British Pound)
- EUR (Euro)
- KES (Kenyan Shilling)

## Testing

Run the test suite:
```bash
php artisan test
```

## Project Structure

```bash
.
├── app/
│   ├── Http/Controllers/    # API Controllers
│   ├── Model/              # Database Models
│   ├── Repository/         # Repository Pattern Classes
│   └── Service/           # Business Logic Services
├── database/
│   ├── migrations/        # Database Migrations
│   └── seeds/            # Database Seeders
└── routes/
    └── api.php           # API Routes
```

## Last Updated

2024-12-12

## License

This project is open-sourced software licensed under the MIT license.
