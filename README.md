# SecureView - Document Management & Response System

SecureView is a lightweight, secure PHP-based web application designed for managing document viewing sessions and collecting user responses. It features a robust admin panel for access control, file management, and user administration.

## Features

### Admin Panel
- **Dashboard**: Centralized hub for all administrative actions.
- **Access Control**: Toggle the public form availability on/off globally.
- **File Management**: Upload PDF documents securely.
- **Document Settings**: define which document is active and set start/expiration times for viewing.
- **User Management**: Create authorized users (Name & UID) for the system.
- **Response Monitoring**: View submissions from the public form.

### User/Public Portal
- **Response Form**: Public uploads interface for users to submit their Name, UID, Email, and Google Drive links.
- **Validation**: Enforces required fields and verifies active status before allowing submissions.
- **Feedback**: Instant success/error messages for submissions.

## Technology Stack

- **Backend**: PHP (Vanilla)
- **Frontend**: HTML5, CSS3
- **Database**: MySQL / MariaDB
- **Server**: Apache (via XAMPP/WAMP or similar)

## Installation Guide

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache Web Server (XAMPP Recommended)

### Setup Steps

1. **Clone/Download**
   - Download the source code and place it in your web server's root directory (e.g., `C:\xampp\htdocs\secureview`).

2. **Database Setup**
   - Open phpMyAdmin (usually `http://localhost/phpmyadmin`).
   - Create a new database named `u688119104_secureview` (or update `config/db.php` if you choose a different name).
   - Import the `database.sql` file located in the project root.

3. **Configuration**
   - Open `config/db.php`.
   - Update the database credentials if necessary:
     ```php
     $host = 'localhost';
     $dbname = 'u688119104_secureview';
     $username = 'root'; // Your DB Username
     $password = '';     // Your DB Password
     ```

4. **Directory Permissions**
   - Ensure the `uploads/` and `docs/` directories are writable by the web server.

### Usage

1. **Accessing the System**
   - **Public Form**: `http://localhost/secureview/uploads/forms.php`
   - **Admin Panel**: `http://localhost/secureview/admin_login.php`

2. **Default Admin Credentials**
   - **Username**: `admin`
   - **Password**: `admin123`
   - *Note: Please change these credentials immediately after deployment for security.*

## License

This project is licensed under the **MIT License**.

```text
MIT License

Copyright (c) 2026 Avanish Kumar

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
