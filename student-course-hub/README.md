# student-course-hub/README.md

# Student Course Hub

A modern web application designed to help prospective students explore undergraduate and postgraduate programmes, view detailed course information, and learn about teaching faculty.

## Features

### Student Interface
- Browse and filter academic programmes by level (undergraduate/postgraduate)
- View detailed programme descriptions and requirements
- Explore programme modules and curriculum details
- Access teaching faculty information and expertise
- Modern, responsive interface with dark mode support
- User-friendly programme filtering and search

### Admin Interface
- Comprehensive course and programme management
- Faculty information management
- Programme publication controls
- Student registration oversight
- Analytics dashboard
- Mailing list management

## Technologies Used

- **Backend**
  - PHP 7.4+
  - MySQL Database
  - MVC Architecture

- **Frontend**
  - HTML5
  - Tailwind CSS 3.0
  - JavaScript
  - Responsive Design
  - Dark Mode Support

- **Development Tools**
  - XAMPP
  - Git
  - VS Code (recommended)

## Installation

1. **Prerequisites**
   - XAMPP (or similar PHP development environment)
   - PHP 7.4 or higher
   - MySQL 5.7 or higher
   - Git

2. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd student-course-hub
   ```

3. **Database Setup**
   - Create a new MySQL database
   - Import the database schema from `database/schema.sql`
   - Update database credentials in `src/config/database.php`

4. **Application Setup**
   ```bash
   # Navigate to XAMPP htdocs directory
   cd /path/to/xampp/htdocs
   
   # Create symlink or copy project files
   ln -s /path/to/student-course-hub .
   
   # If using npm for frontend dependencies
   npm install
   ```

5. **Configure Virtual Host (Optional)**
   Add to Apache's `httpd-vhosts.conf`:
   ```apache
   <VirtualHost *:80>
       DocumentRoot "/path/to/xampp/htdocs/student-course-hub/public"
       ServerName student-course-hub.local
       <Directory "/path/to/xampp/htdocs/student-course-hub/public">
           Options Indexes FollowSymLinks MultiViews
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

## Project Structure

```
student-course-hub/
├── public/             # Public-facing files
│   ├── assets/        # CSS, JS, images
│   └── index.php      # Entry point
├── src/               # Application source
│   ├── controllers/   # MVC Controllers
│   ├── models/        # Database models
│   ├── views/         # View templates
│   └── config/        # Configuration files
└── database/          # Database schemas and migrations
```

## Usage

### Student Portal
- Access the student interface at `/student`
- Browse programmes using the sidebar navigation
- Use filters to find specific programmes
- View detailed programme and faculty information

### Admin Portal
- Access the admin interface at `/admin`
- Login with administrator credentials
- Manage programmes, faculty, and student registrations
- Control programme visibility and publication status

## Development

1. **Coding Standards**
   - Follow PSR-12 coding standards for PHP
   - Use Tailwind CSS utility classes for styling
   - Maintain consistent file and class naming conventions

2. **Testing**
   - Test new features thoroughly
   - Ensure responsive design works across devices
   - Validate database operations

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/your-feature`)
3. Commit changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin feature/your-feature`)
5. Submit a Pull Request

## License

This project is licensed under the MIT License. See LICENSE file for details.

## Support

For support and questions, please open an issue in the GitHub repository or contact the development team.