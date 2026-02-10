# Election System

A simple online voting system built with PHP, MySQL, HTML, CSS, and JavaScript.

## Features

- 👤 User Registration & Login
- 🗳️ One-time voting per user
- 👨‍💼 Admin dashboard to manage candidates
- 📊 Real-time election results
- 📸 Candidate photo upload
- 🔒 Secure password hashing

## Installation

### Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP/WAMP (for local development)

### Setup Instructions

1. **Clone the repository:**
```bash
   git clone https://github.com/YOUR-USERNAME/election-system.git
   cd election-system
```

2. **Import Database:**
   - Create a new database named `election_system`
   - Import the `database.sql` file from the project root

3. **Configure Database:**
   - Copy `config/database.example.php` to `config/database.php`
   - Update database credentials in `config/database.php`

4. **Set Permissions:**
```bash
   chmod 777 assets/uploads/candidates
```

5. **Access the application:**
   - Local: `http://localhost/election-system`

## Default Admin Login

- **Username:** admin
- **Password:** admin123

⚠️ **Important:** Change the admin password after first login!

## Project Structure
```
election-system/
├── admin/              # Admin panel files
├── assets/             # CSS, JS, and uploads
├── config/             # Database configuration
├── includes/           # Reusable components
├── index.php           # Home page
├── login.php           # Login page
├── register.php        # Registration page
├── vote.php            # Vote processing
└── logout.php          # Logout handler
```

## Technologies Used

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Database:** MySQL
- **Server:** Apache

## Screenshots

(Add screenshots here after deployment)

## License

This project is open-source and available under the MIT License.

## Author

Rudich Chhantel
