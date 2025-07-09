# PageantDBMS-Unfinished
This is a Database Management System (DBMS) for managing pageant events, contestants, judges, scores, and results. The system is built using PHP, MySQL, and runs on the XAMPP server.  Features

Features
Contestant registration and management

Judge accounts and authentication

Criteria and scoring system

Real-time results calculation

Admin dashboard for event management

Reporting and analytics

Technologies Used
PHP 7.4+

MySQL 8.0

HTML5, CSS3, JavaScript

Bootstrap 5 (for responsive design)

XAMPP (Apache, MySQL, PHP stack)

System Requirements
Windows/macOS/Linux

XAMPP 8.0+ installed

Visual Studio Code (with PHP extensions recommended)

Web browser (Chrome, Firefox, Edge)

Installation Instructions
Install XAMPP

Download from Apache Friends

Install with default settings

Start Apache and MySQL services from XAMPP Control Panel

Set Up the Project

Clone this repository or extract the project files

Place the project folder in htdocs directory (usually C:\xampp\htdocs\ on Windows)

Database Setup

Open phpMyAdmin (http://localhost/phpmyadmin)

Create a new database (e.g., pageant_db)

Import the SQL file from database/pageant_db.sql

Configure Database Connection

Edit config/database.php with your database credentials:

php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pageant_db');
Run the Application

Open browser and navigate to http://localhost/your-project-folder
