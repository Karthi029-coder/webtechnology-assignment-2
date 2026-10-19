COLLEGE STUDENT ADMIN PANEL - PHP & MYSQL

REQUIREMENTS
- XAMPP (Apache, PHP, MySQL/MariaDB)
- A web browser

INSTALLATION
1. Extract this folder into C:\xampp\htdocs\college_admin
2. Start Apache and MySQL in XAMPP Control Panel.
3. Open http://localhost/phpmyadmin/
4. Select Import and import database.sql.
5. Open http://localhost/college_admin/index.php

FEATURES
- Add, view, edit and delete student records
- Upload one optional JPG, PNG or PDF file when adding a student
- Store file path, MIME type and upload date in MySQL
- View uploaded images and open documents
- Prepared statements and output escaping
- Maximum upload size: 2 MB

DATABASE CONNECTION
Default XAMPP credentials in db.php are username=root and blank password.
If your MySQL credentials differ, edit db.php.

NOTES
This is a classroom demonstration, not a production-ready public admin system.
Add login/authentication, authorization, CSRF protection, and private file serving
before deployment. Configure the uploads directory so scripts cannot execute.
