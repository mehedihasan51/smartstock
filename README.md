# SmartStock — AI-Powered Inventory Management

A practical full-stack assessment project for NT Tech Innovation. Stack: Laravel 12 API, MySQL, React + Vite, and a simple explainable demand/reorder heuristic (no paid AI API required).

## Features
- Token-based authentication using Laravel Sanctum
- Admin and Staff roles; protected API endpoints
- Product, category, supplier CRUD
- Sales workflow with transaction-safe stock reduction and validation
- Dashboard summary and low-stock alerts
- Explainable demand estimate/reorder recommendations based on recent sales
- React dashboard, products, sales, recommendations, login
- Feature test for stock reduction on a sale

## Requirements
- PHP 8.2+, Composer
- MySQL 8+ (or compatible MariaDB)
- Node.js 20+
- Git

## Setup (Windows)

### 1. Create the Laravel backend
Open PowerShell in the folder where you want the project:

```powershell
composer create-project laravel/laravel backend "^12.0"
cd backend
composer require laravel/sanctum
php artisan install:api
```

Copy the files from this ZIP's `backend-overlay` folder into the newly created `backend` folder, preserving directory structure. Allow overwrite when prompted.

### 2. Configure MySQL
Create a database in phpMyAdmin or MySQL:
```sql
CREATE DATABASE smartstock CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```
Copy `.env.example` to `.env` if needed and set:
```env
APP_NAME=SmartStock
APP_URL=http://127.0.0.1:8000
FRONTEND_URL=http://localhost:5173
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smartstock
DB_USERNAME=root
DB_PASSWORD=
```
Then run:
```powershell
php artisan key:generate
php artisan migrate --seed
php artisan serve
```
API runs at `http://127.0.0.1:8000`.

### 3. Start the React frontend
Open a second terminal at the ZIP's `frontend` folder:
```powershell
npm install
copy .env.example .env
npm run dev
```
Open `http://localhost:5173`.

### Demo account
- Email: `admin@smartstock.test`
- Password: `Password123!`

Change demo credentials before any real deployment. Do not commit `.env`.

## API overview
- `POST /api/register` (disabled in the UI; public registration creates Staff)
- `POST /api/login`
- `POST /api/logout` (auth required)
- `GET /api/me`
- `GET/POST /api/products`
- `GET/PUT/DELETE /api/products/{product}`
- `GET/POST /api/categories`
- `GET/POST /api/suppliers`
- `GET/POST /api/sales`
- `GET /api/dashboard`
- `GET /api/ai/reorder-recommendations`

All business endpoints require a Sanctum bearer token. Product/category/supplier writes and deletes require Admin. Staff can view inventory and record sales. The API validates request data and prevents sales above available stock.

## AI/ML feature explanation
The recommender is an intentionally small, explainable heuristic rather than a claim of a trained deep-learning model. It uses current stock, reorder threshold, and average daily sales over the last 30 days:
- `estimated_days_remaining = current_stock / average_daily_units`
- `recommended_reorder_qty = max(0, target_stock - current_stock)`
- urgency is `critical` when stock is at/below zero or estimated coverage is <= 3 days; `high` when <= 7 days or stock is below reorder level; otherwise `normal`.
- target stock is 14 days of average demand, but never below the product's reorder level.

Inputs: product stock, reorder level, and completed sale items from the last 30 days.
Outputs: estimated days remaining, urgency, and suggested reorder quantity.
Limitations: sparse/new products have little history; promotions, seasonality, supplier lead time and external demand shocks are not modeled.
Evaluation: verify recommendations against a few hand-calculated sample products; compare predicted coverage with observed stock-outs during a pilot. Do not describe it as a trained ML model; it is a basic data-driven decision-support feature.

## Git and testing
Make meaningful commits, for example:
1. `chore: scaffold Laravel API and React app`
2. `feat: add inventory CRUD and role protection`
3. `feat: add sales workflow and stock validation`
4. `feat: add demand-based reorder recommendations`
5. `test: cover stock deduction on sale`

Run backend tests:
```powershell
php artisan test
```

## Architecture
React (Vite) -> JSON REST API -> Laravel controllers/services/models -> MySQL. Sanctum issues personal access tokens. Sales are wrapped in a database transaction and lock product rows to avoid two simultaneous sales overselling the same stock.

## Known limitations
- No purchase-order receiving, invoices, multi-branch inventory, or password-reset email.
- The recommendation logic is a transparent heuristic, not a general-purpose forecasting model.
- This is a local assessment starter. Review dependencies, configure production CORS/HTTPS, rotate credentials, and harden rate limits before deployment.
