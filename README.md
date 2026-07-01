# 🎓 Student Management System (PHP + MySQL)

A simple, beginner-friendly **Student Management System** built with core PHP and MySQL.
It covers user authentication, full CRUD, image uploads, search, and pagination.

## ✨ Features

- 🔐 **User Authentication** — Register & Login with hashed passwords (`password_hash`)
- 🛡️ **Session-based protection** — student pages are accessible only when logged in
- 👨‍🎓 **Student CRUD** — Create, Read, Update, Delete students
- 🖼️ **Profile Picture Upload** — with type & size validation
- 🔎 **Search** — search students by name, email, or course
- 📄 **Pagination** — clean page navigation for large lists
- ✅ **Form Validation** — server-side validation on every form
- 🔒 **Secure** — prepared statements (SQL-injection safe) + `htmlspecialchars` (XSS safe)

## 🛠️ Tech Stack

- **PHP** (core, no framework)
- **MySQL** (via MySQLi with prepared statements)
- **HTML / CSS** (custom, responsive UI)

## 📁 Project Structure

```
php/
├── config/
│   └── db.php            # Database connection
├── includes/
│   ├── header.php        # Navbar + <head>
│   ├── footer.php        # Footer
│   └── auth_check.php    # Login guard for protected pages
├── auth/
│   ├── register.php      # Sign up
│   ├── login.php         # Login (sessions)
│   └── logout.php        # Logout
├── students/
│   ├── index.php         # List + search + pagination
│   ├── create.php        # Add student + photo upload
│   ├── edit.php          # Edit student
│   └── delete.php        # Delete student + photo
├── uploads/              # Uploaded profile pictures
├── assets/
│   └── style.css         # Styles
└── index.php             # Home page
```

## 🚀 Getting Started (Local Setup)

1. **Install [XAMPP](https://www.apachefriends.org/)** and start **MySQL** (and Apache for phpMyAdmin).

2. **Create the database.** Open `http://localhost/phpmyadmin` → SQL tab → run:

   ```sql
   CREATE DATABASE student_db;
   USE student_db;

   CREATE TABLE users (
       id INT AUTO_INCREMENT PRIMARY KEY,
       name VARCHAR(100) NOT NULL,
       email VARCHAR(100) NOT NULL UNIQUE,
       password VARCHAR(255) NOT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );

   CREATE TABLE students (
       id INT AUTO_INCREMENT PRIMARY KEY,
       name VARCHAR(100) NOT NULL,
       email VARCHAR(100) NOT NULL,
       phone VARCHAR(20),
       course VARCHAR(100),
       photo VARCHAR(255) DEFAULT NULL,
       created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
   );
   ```

3. **Run the app** from the project folder:

   ```bash
   php -S localhost:8000
   ```

4. Open **http://localhost:8000** → Register → Login → manage students. 🎉

## 📸 Screenshots

_Add your screenshots here._

---

Built with ❤️ while learning PHP.
