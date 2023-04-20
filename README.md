# mukuru-api
```bash
.
├── README.md
├── app
│   ├── Console
│   │   └── Kernel.php
│   ├── Exceptions
│   │   └── Handler.php
│   ├── Http
│   │   ├── Controllers
│   │   ├── Kernel.php
│   │   ├── Middleware
│   │   └── Requests
│   ├── Model
│   │   ├── Currency.php
│   │   └── Order.php
│   ├── Providers
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   ├── BroadcastServiceProvider.php
│   │   ├── EventServiceProvider.php
│   │   └── RouteServiceProvider.php
│   ├── Repository
│   │   ├── CurrencyRepository.php
│   │   ├── ExchangeRateDbRepository.php
│   │   ├── JsonRatesRepository.php
│   │   └── OrderRepository.php
│   ├── Service
│   │   ├── ExchangeRateInterface.php
│   │   └── ExchangeRateService.php
│   └── User.php
├── artisan
├── bootstrap
│   ├── app.php
│   └── cache
├── composer.json
├── config
│   ├── app.php
│   ├── auth.php
│   ├── broadcasting.php
│   ├── cache.php
│   ├── database.php
│   ├── filesystems.php
│   ├── mail.php
│   ├── queue.php
│   ├── services.php
│   ├── session.php
│   └── view.php
├── database
│   ├── factories
│   │   └── UserFactory.php
│   ├── migrations
│   │   ├── 2014_10_12_000000_create_users_table.php
│   │   ├── 2014_10_12_100000_create_password_resets_table.php
│   │   ├── 2018_07_03_134540_create_order_table.php
│   │   └── 2018_07_03_134548_create_currency_table.php
│   └── seeds
│       ├── CurrencySeeder.php
│       └── DatabaseSeeder.php
├── package.json
├── phpunit.xml
├── public
│   ├── css
│   │   └── app.css
│   ├── favicon.ico
│   ├── index.php
│   ├── js
│   │   └── app.js
│   ├── robots.txt
│   └── web.config
├── resources
│   ├── assets
│   │   ├── js
│   │   └── sass
│   ├── lang
│   │   └── en
│   └── views
│       └── welcome.blade.php
├── routes
│   ├── api.php
│   ├── channels.php
│   ├── console.php
│   └── web.php
├── server.php
├── storage
│   ├── app
│   │   └── public
│   ├── framework
│   │   ├── cache
│   │   ├── sessions
│   │   ├── testing
│   │   └── views
│   └── logs
├── tests
│   ├── CreatesApplication.php
│   ├── Feature
│   │   ├── ExampleTest.php
│   │   └── ExchangeRateTest.php
│   ├── TestCase.php
│   └── Unit
│       └── ExampleTest.php
└── webpack.mix.js
```

#### Requires:
- php > 7.0
- MySQL 5.6
- composer

### Database Migration

- php artisan migrate --seed

### Manual Setup Instructions

- Run composer install
- Copy .env.tpl and update database configurations

### Running the application

- Run php artisan serve --port 8000
