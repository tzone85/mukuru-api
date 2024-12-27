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
git clone https://github.com/tzone85/mukuru-api.git
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

## API Documentation

The API documentation is available through Scribe, which provides detailed information about all endpoints, request/response formats, and example usage.

### Accessing the Documentation

1. Generate the documentation (if not already generated):
```bash
php artisan scribe:generate
```

2. Visit the documentation at:
```
http://localhost:8000/docs
```

The documentation includes:
- Detailed API endpoint descriptions
- Request/response examples
- Interactive API testing interface
- Authentication details
- OpenAPI/Swagger specification
- Postman collection

You can also find the following files:
- OpenAPI specification: `public/docs/openapi.yaml`
- Postman collection: `public/docs/collection.json`

## API Endpoints

### Currencies

- `GET /api/currencies` - List all currencies
- `GET /api/currencies/{id}` - Get specific currency details

### Currency Endpoints

#### Get Currency by ID

```http
GET /api/currency/{id}
```

**Parameters:**

- `id` (required) - The unique identifier of the currency

**Example Request:**

```bash
curl -X GET http://127.0.0.1:8000/api/currency/1
```

**Success Response (200 OK):**

```json
{
  "data": {
    "id": 1,
    "code": "ZAR",
    "name": "South African Rand",
    "symbol": "R",
    "rate": 18.65,
    "surcharge_percentage": 7.5,
    "discount_percentage": 0,
    "created_at": "2024-12-12T14:27:22.000000Z",
    "updated_at": "2024-12-12T14:27:22.000000Z"
  }
}
```

**Error Response (404 Not Found):**

```json
{
  "error": "Currency not found"
}
```

### Currency Conversion

- `POST /api/get-foreign-currency-amount` - Convert USD to foreign currency
- `POST /api/get-total-amount` - Convert foreign currency to USD

### Orders

- `POST /api/orders` - Create a new currency exchange order

## Example API Usage

### Get Currency by ID

```bash
# Request
curl -X GET http://127.0.0.1:8000/api/currencies/1

# Response
{
    "id": 1,
    "code": "ZAR",
    "name": "South African Rand",
    "symbol": "R",
    "rate": 18.65,
    "surcharge_percentage": 7.5,
    "discount_percentage": 0,
    "created_at": "2024-12-12T14:20:53.000000Z",
    "updated_at": "2024-12-12T14:20:53.000000Z"
}
```

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

The project includes comprehensive unit and feature tests for all API endpoints. To run the tests:

1. Set up the testing environment:

```bash
cp .env.example .env.testing
```

2. Configure your testing database in `.env.testing`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mukurudb_testing
DB_USERNAME=root
DB_PASSWORD=
```

3. Create the testing database:

```bash
mysql -u root -e "CREATE DATABASE mukurudb_testing"
```

4. Run all tests:

```bash
php artisan test
```

Or run specific test suites:

```bash
# Run only currency-related tests
php artisan test --filter=CurrencyTest

# Run only order-related tests
php artisan test --filter=OrderTest
```

### Test Coverage

The test suite covers:

#### Currency Operations

- Currency listing and retrieval
- Currency details validation
- Non-existent currency handling

#### Currency Conversion

- USD to foreign currency conversion
- Foreign currency to USD conversion
- Exchange rate calculations
- Surcharge and discount applications

#### Input Validation

- Required fields validation
- Numeric input validation
- Positive amount validation
- Invalid currency handling

#### Error Handling

- 404 responses for non-existent resources
- 422 responses for validation errors
- Proper error message formatting

### Continuous Integration

The test suite is automatically run on every push to the repository using GitHub Actions.

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
