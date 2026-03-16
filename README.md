# Truck Management System

A PHP-based truck management dashboard for tracking trips, drivers, trucks, and points (goods). Built to run on a local development stack (e.g., XAMPP) and intended to be deployed behind a web server with a MySQL database.

---

## ✅ Key Features

- User authentication (login, reset password, email verification)
- Trip creation, tracking, and completion
- Driver and truck management
- Points (goods) management
- PDF export for trips (using FPDF)
- API endpoints for AJAX operations

---

## 🛠️ Requirements

- PHP 7.4+ (PHP 8 recommended)
- MySQL / MariaDB
- Apache (XAMPP / WAMP / MAMP)
- Composer (for dependency management)

---

## 🚀 Local Setup (XAMPP)

1. **Copy the project into `htdocs`**
   - `c:\xampp\htdocs\truck_mg`

2. **Create the database**
   - Open `http://localhost/phpmyadmin`
   - Create database: `trucking_system`

3. **Import schema (if available)**
   - If you have a `database.sql` or dump file, import it in phpMyAdmin.
   - If not, manually create needed tables based on the application requirements.

4. **Configure database credentials**
   - Update `config/db.php` and `config/app.php` with your MySQL credentials.

5. **Run the app**
   - Open in browser: `http://localhost/truck_mg/admin/login.php`

---

## ⚙️ Composer

If you need to install PHP dependencies (e.g., `phpmailer`), run:

```bash
cd c:\xampp\htdocs\truck_mg
composer install
```

> Note: `vendor/` is excluded from the repo via `.gitignore`; running Composer will recreate it.

---

## 🧩 Recommended `.env` strategy (optional)

For security, you can keep credentials out of the repo by using an environment file and updating `config/db.php` to read from it.

---

## 📌 Useful links

- Admin login: `http://localhost/truck_mg/admin/login.php`

---

## 🧑‍💻 Contributing

1. Fork the repo
2. Create a new branch (`feature/xxx`)
3. Submit a pull request

---

## 📄 License

This project is licensed under [MIT](LICENSE) (if you add one).