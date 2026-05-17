# 💰 ApkaBudget - Financial Management System

ApkaBudget is a lightweight, efficient Financial Management System built with **Laravel (PHP)**. It is designed to help users seamlessly track their daily income and expenses. By leveraging **AJAX and JSON**, the platform provides a smooth, modern, and no-reload user experience backed by a clean and powerful HTML/CSS admin dashboard.

---

## 🚀 Key Features

* **No-Reload Experience:** Powered by AJAX and JSON for instant data fetching, entries, and UI updates without refreshing the page.
* **Income & Expense Tracking:** Easy-to-use logging system to categorize cash flows (e.g., Salary, Food, Rent, Entertainment).
* **Interactive Dashboard:** Visual breakdown of total balance, monthly income, and expense logs.
* **Category Management:** Custom categories for better financial classification.
* **History & Filters:** Track and filter past transactions by date, type, or category.

---

## 🛠️ Tech Stack Used

* **Backend:** Laravel PHP Framework (MVC Architecture)
* **Frontend:** Blade Templates, JavaScript (AJAX / Fetch API), HTML5, CSS3 / SCSS
* **Database:** MySQL
* **Data Format:** JSON (for asynchronous communication)

---

## 💻 Getting Started / Installation

Follow these steps to set up this Laravel project locally:

### Prerequisites
* PHP (>= 8.x recommended)
* Composer
* MySQL Database (XAMPP / WampServer)

### 1. Clone the Repository
```bash
git clone [https://github.com/Vidhya-sagar01/apkabudget.git](https://github.com/Vidhya-sagar01/apkabudget.git)
cd apkabudget



2. Install Dependencies
Bash
composer install
npm install && npm run dev
3. Environment Configuration
Duplicate the .env.example file and rename it to .env:

Bash
cp .env.example .env
Open the .env file and set up your MySQL database credentials:

Code snippet
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=
4. Generate Application Key & Migrate Database
Bash
php artisan key:generate
php artisan migrate
5. Run the Project
Bash
php artisan serve
Now, open your browser and go to http://127.0.0.1:8000.
