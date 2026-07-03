# Tor1 — Running Shoes E-Commerce

PHP-based running shoe store with product catalog, cart, checkout, user accounts, and admin panel.

## Setup

```bash
# 1. Create database
mysql -u root -p -e "CREATE DATABASE runningdb"

# 2. Import schema + seed data
mysql -u root -p runningdb < running_shoes_website_db.sql
mysql -u root -p runningdb < seed.sql

# 3. Configure env
cp .env.example .env
# Edit .env with your DB credentials

# 4. Start dev server
php -S localhost:8000
```

## Stack

- **Backend:** PHP 8+, MariaDB/MySQL via PDO
- **Frontend:** Vanilla JS, jQuery, GSAP (animations)
- **Cart & Auth:** Session-based

## Features

- Gender-based catalog (men/women), new arrivals, sale items, search
- Product detail with color swatches and size selection
- Shopping cart (add/remove) and checkout with shipping rules
- User authentication (register/login) and account dashboard
- Admin panel for products, variants, orders, refunds, inventory
- Variant-level discount system (percentage/fixed)
- Server-side filtering (price, color, material, size)
- Fully responsive design
