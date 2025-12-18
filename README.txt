UniBot - README
===============

Project Overview
----------------
UniBot is a web-based application built using PHP and MySQL. It is designed to [briefly describe what your project does, e.g., "assist university students with course management and queries"]. This README will guide you through the setup and usage of the project using XAMPP.

Requirements
------------
1. XAMPP installed (https://www.apachefriends.org/)
   - Apache server
   - MySQL (MariaDB)
2. A web browser (Chrome, Firefox, Edge, etc.)
3. PHP 7.4 or higher (comes with XAMPP)
4. Basic knowledge of running XAMPP

Installation & Setup
--------------------
1. **Start XAMPP**:
   - Open XAMPP Control Panel.
   - Start **Apache** and **MySQL** modules.

2. **Copy Project Files**:
   - Copy the `unibot` project folder into `C:\xampp\htdocs\`.
     Example: `C:\xampp\htdocs\unibot\`

3. **Create Database**:
   - Open your browser and go to `http://localhost/phpmyadmin/`.
   - Click **New** to create a new database. Name it `unibot_db` (or any name you prefer).
   - Import the database:
     - Click **Import** in phpMyAdmin.
     - Choose the provided `unibot.sql` file (found in the project folder) and click **Go**.

4. **Configure Database Connection**:
   - Open `config.php` (or your database configuration file) in the project folder.
   - Update the following lines with your database details if necessary:
     ```php
     $servername = "localhost";
     $username = "root";        // default XAMPP username
     $password = "";            // default XAMPP password is empty
     $dbname = "unibot_db";     // database name you created
     ```

5. **Access the Project**:
   - Open a browser and go to: `http://localhost/unibot/`
   - You should now see the UniBot homepage.

Usage
-----
- [Briefly explain main features and how the user can interact with the project, e.g., "Log in as a student, access course materials, and chat with UniBot for guidance."]

Troubleshooting
---------------
- **Database Connection Errors**:
  - Ensure MySQL is running in XAMPP.
  - Verify database name, username, and password in `config.php`.
- **Page Not Loading**:
  - Ensure Apache is running in XAMPP.
  - Check that the project folder is correctly placed in `htdocs`.

Contact
-------
For any issues, contact [Your Name] at [Your Email] (optional).

---

Thank you for trying out UniBot!
