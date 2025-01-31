# Event Management System

A web-based event management system built with PHP and MySQL that allows users to create, manage events and handle attendee registrations.

## 🔴 Live Demo

**URL**: [Event Management App](http://4.145.104.31/)

**Test Credentials**:
- Admin Account (admin can manage all events):
  - Email: admin@example.com
  - Password: admin123
- User Account (user can manage events created by him):
  - Email: user@example.com
  - Password: user123

## 🌟 Features

### Core Features
- User Authentication (Login/Registration)
- Event Management (CRUD Operations)
- Attendee Registration System
- Event Dashboard with Pagination
- CSV Export for Attendee Lists
- Search Functionality
- AJAX-based Registration
- JSON API Endpoints

### Technical Features
- Pure PHP (No Framework)
- MySQL Database
- Client & Server-side Validation
- Prepared Statements
- Responsive Bootstrap UI
- Security Implementation

## 🛠️ Requirements

- PHP >= 8.0
- MySQL >= 5.7
- Apache/Nginx Server

## ⚙️ Installation
** First start your xampp or apache server and mysql.

1. **Clone Repository**
```bash
git clone https://github.com/sharif7761/php-event-management.git
cd php-event-management
```

2. **Configuration env**
- Copy `.env.example` to `.env`  (although not recommended, the .env file has already been created and pushed to Git for your convenience)
- Update database credentials:
```php
DB_HOST=localhost
DB_NAME=event_management
DB_USER=root
DB_PASS=
```

3. **Database Setup**
   You have three options to setup the database:

Option 1: Using Migration Commands
```bash
# Create, migrate and seed database
php database/migrate.php fresh

# Or, only run migrations
php database/migrate.php migrate

# Or, only run seeders
php database/migrate.php seed
```

Option 2: Using SQL File
```bash
 Run the sql commands from the schema.sql file into your MySQL database
```

Option 3: Download SQL File
- Download the database file from [Google Drive Link](https://drive.google.com/file/d/15Mh5Ct8RShdZtNWppBLf2rlI7myJzsUH/view?usp=sharing)
- Import the downloaded SQL file to your MySQL server

4. **Run Project**
```bash
# Start development server on port 8002
php -S localhost:8002 -t public
```
The application will be available at http://localhost:8002

## 🌟Test Credentials
- Admin Account (admin can manage all events):
  - Email: admin@example.com
  - Password: admin123
- User Account (user can manage events created by him):
  - Email: user@example.com
  - Password: user123

## 🌟 API Endpoints

Available endpoints for programmatic access:
```bash
# Get all events
GET /api/events
```