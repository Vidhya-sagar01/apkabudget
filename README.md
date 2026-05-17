# ✨ Apka budget - Door-to-Door Service Provider Platform

Aura is a modern and efficient **Door-to-Door Service Provider** web application. It connects users with local service professionals (like cleaning, repairs, delivery, or maintenance) right at their doorstep. Designed with a seamless user experience, it simplifies service booking, tracking, and management.

---

## 🚀 Key Features

### 👤 User Features
* **Service Discovery:** Browse through various home service categories with ease.
* **On-Demand Booking:** Book a service for immediate assistance or schedule it for later.
* **Doorstep Tracking:** Real-time updates on the status of your service request.
* **Booking History:** Keep a log of all past and upcoming service appointments.

### 💼 Provider & Admin Features
* **Service Management:** Admins can add, update, or remove service categories and pricing.
* **Order Assignment:** Smooth system to track new incoming requests and assign them to available professionals.
* **Status Control:** Update booking status from "Accepted" to "In-Progress" and "Completed".

---

## 🛠️ Tech Stack Used

*(Tip: You can update this section based on the exact languages/frameworks you used!)*
* **Frontend:** HTML5, CSS3, JavaScript (React / Blade Templates)
* **Backend:** PHP / Node.js
* **Database:** MySQL / MongoDB
* **Styling:** Bootstrap / Tailwind CSS

---

## 💻 Getting Started / Installation

Follow these steps to set up the project locally:

### 1. Clone the Repository
```bash
git clone [https://github.com/Vidhya-sagar01/Aura.git](https://github.com/Vidhya-sagar01/Aura.git)
cd Aura

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
