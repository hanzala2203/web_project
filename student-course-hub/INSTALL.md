# Installation Guide

## Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server with mod_rewrite enabled
- Composer (for dependencies)

## Installation Steps

1. **Clone/Download the Project**
   ```bash
   git clone <repository-url>
   cd student-course-hub
   ```

2. **Set Up Database**
   - Create a new MySQL database
   - Import the database schema:
   ```bash
   mysql -u root -p < database.sql
   ```

3. **Configure Database Connection**
   - Open `src/config/database.php`
   - Update the database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'student_course_hub');
   ```

4. **Set Up Web Server**
   - Configure Apache virtual host:
   ```apache
   <VirtualHost *:80>
       ServerName student-course-hub.local
       DocumentRoot "path/to/student-course-hub"
       <Directory "path/to/student-course-hub">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```
   - Add to hosts file:
   ```
   127.0.0.1 student-course-hub.local
   ```

5. **Default Login Credentials**
   ```
   Admin:
   Email: admin@university.com
   Password: admin123

   Student:
   Email: student@university.com
   Password: student123
   ```

6. **Start the Application**
   - Visit: http://student-course-hub.local
   - Or using PHP's built-in server:
   ```bash
   php -S localhost:8000
   ```
