# Bakso Keju Greenville

#### Requirements

1. PHP 8.0 or higher
2. Composer 2 or higher
3. MySQL

#### Instalation Steps

1. Open your terminal.
1. Copy `.env` file with `cp .env.example .env`.
1. Set environment variables on `.env` file using your favorite text editor.
1. Install dependencies with

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
```

1. Generate `APP_KEY` with `sail artisan key:generate`
1. Run migrations with `sail artisan migrate`
1. Seed data for initial setup `sail artisan db:seed`
1. Open `http://laravel.test` on your browser.

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
  - Reseller
  - Order Source
  - Branch
  - Percentage Discount
  - Total Discount
  - Total Line Items Quantity
  - Total Line Items Price
  - Total Price

- Order Line Item
  - Order
  - Product
  - Price
  - Quantity
  - Total

- Reseller
  - Name
  - Percentage Discount

- Inventory
  - Product
  - Branch
  - Quantity
  - Note
  - Created by

- Product Inventory
  - Branch
  - Product
  - Quantity

# Relationship

- Product depends on Category
- Inventory depends on Product and Branch
- Order depends on Reseller, Order Source, and Branch
- Line item depends on Order and Product
- Inventory item depends on Product and Branch

# Special permissions

- Allow create inventories with negative quantity
