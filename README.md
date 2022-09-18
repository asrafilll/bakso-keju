# Bakso Keju Greenville

#### Requirements

1. PHP 8.0 or higher
2. Composer 2 or higher
3. MySQL

#### Instalation Steps

1. Open your terminal.
1. Copy `.env` file with `cp .env.example .env`.
1. Set environment variables on `.env` file using your favorite text editor.
1. Install dependencies with `composer install`.
1. Generate `APP_KEY` with `php artisan key:generate`
1. Run migrations with `php artisan migrate`
1. Seed data for initial setup `php artisan db:seed`
1. Run app with `php artisan serve`
1. Open `http://127.0.0.1:8000` on your browser.

# Entities

- Product category
  - Name

- Product
  - Name
  - Price
  - Product category

- Order Source
  - Name

- Branch
  - Name
  - Order number prefix
  - Next order number

- Order
  - Order Number
  - Customer
  - Order Source
  - Branch
  - Discount
  - Quantity
  - Total

- Line Item
  - Order
  - Product
  - Price
  - Discount
  - Quantity
  - Total

- Customer
  - Name
  - Discount

- Inventory
  - Product
  - Branch
  - Quantity
  - Note

- Inventory Item
  - Branch
  - Product
  - Quantity

# Relationship

- Product depends on Category
- Inventory depends on Product and Branch
- Order depends on Customer, Order Source, and Branch
- Line item depends on Order and Product
