# AI Text Summarizer 🤖

A modern web application that uses artificial intelligence to generate concise summaries of long texts. Built with PHP, MySQL, and Bootstrap 5.

![Project Banner](assets/images/banner.png)

## 🌟 Features

- **Text Summarization**
  - AI-powered text analysis
  - Customizable summary length
  - Real-time processing
  - Statistics visualization

- **User Management**
  - Secure authentication
  - Personal dashboards
  - Summary history
  - Download summaries

- **Admin Panel**
  - User management
  - Usage statistics
  - System monitoring
  - Activity logs

## 🚀 Getting Started

### Prerequisites

- PHP 7.4+
- MySQL 5.7+
- Apache/Nginx
- Composer

### Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/ai-text-summarizer.git
cd ai-text-summarizer
```

2. Create the database:
```sql
CREATE DATABASE summarizer;
```

3. Import the database schema:
```bash
mysql -u root -p summarizer < database.sql
```

4. Configure your database connection:
```php
// config/db.php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'summarizer');
```

5. Create an admin user:
```sql
INSERT INTO users (username, email, password, role) 
VALUES (
    'admin',
    'admin@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'admin'
);
```

### Default Admin Credentials
- Email: admin@example.com
- Password: password

## 🏗 Project Structure

```
ai-text-summarizer/
├── admin/
│   ├── dashboard.php
│   ├── users.php
│   └── ...
├── config/
│   └── db.php
├── assets/
│   ├── css/
│   └── js/
├── includes/
│   ├── navbar.php
│   └── footer.php
├── index.php
├── login.php
├── signup.php
└── user.php
```

## 💡 Key Features Explained

### Text Summarization
- Implements natural language processing
- Maintains context and meaning
- Provides reduction statistics
- Supports multiple languages

### User Dashboard
- Summary history tracking
- Download summaries as TXT
- Personal settings management
- Usage statistics

### Admin Features
- Complete user management
- System statistics
- Activity monitoring
- Performance metrics

## 🔐 Admin Panel

### Accessing Admin Panel
- Direct access via `/admin/login.php`
- Separate secure login for administrators
- Not visible to regular users

### Default Admin Credentials
```plaintext
Email: admin@summarizer.com
Password: Admin@123
```

### Security Features
- Role-based authentication
- Separate admin login portal
- Session-based security
- Password hashing with bcrypt
- PDO prepared statements
- SQL injection protection

### Admin Capabilities
- User Management
  - View all users
  - Add new users
  - Delete user accounts
  - Monitor user activity
- Statistics Dashboard
  - Total users count
  - Summary statistics
  - System usage metrics
- System Management
  - User role management
  - Activity logging
  - Error monitoring

## 🔒 Security Features

- Password hashing using `password_hash()`
- PDO prepared statements
- Session management
- Role-based access control
- XSS protection
- CSRF protection

## 📊 Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Summaries Table
```sql
CREATE TABLE summaries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    original_text TEXT NOT NULL,
    summary_text TEXT NOT NULL,
    characters_count INT NOT NULL,
    reduction_percentage DECIMAL(5,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## 📦 Installation

### 1. Database Setup
```sql
CREATE DATABASE summarizer;
USE summarizer;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 2. Admin Account Setup
```powershell
# Navigate to project directory
cd c:\Users\arkba\Desktop\ps

# Run admin setup script
php admin/setup_admin.php
```

### 3. Security Recommendations
- Change default admin password after first login
- Remove or rename setup_admin.php after use
- Configure secure session settings
- Enable HTTPS
- Set up proper file permissions

## 🔒 Authentication Flow

### User Authentication
```php
POST /login.php
{
    "email": "user@example.com",
    "password": "userpassword"
}
```

### Admin Authentication
```php
POST /admin/login.php
{
    "email": "admin@summarizer.com",
    "password": "Admin@123"
}
```

## 🏗 Project Structure Update
```
ai-text-summarizer/
├── admin/
│   ├── dashboard.php
│   ├── login.php
│   ├── users.php
│   └── setup_admin.php
├── config/
│   └── db.php
├── assets/
│   ├── css/
│   └── js/
├── includes/
│   ├── navbar.php
│   └── footer.php
├── index.php
├── login.php
├── signup.php
└── user.php
```

## 🛠 Technologies Used

- **Frontend**
  - HTML5
  - CSS3/Bootstrap 5
  - JavaScript/AJAX
  - Font Awesome
  - Animate.css

- **Backend**
  - PHP 7.4+
  - MySQL
  - PDO
  - Sessions

## ⚡ AJAX Implementations

### Text Summarization
```javascript
const xhr = new XMLHttpRequest();
xhr.open('POST', 'index.php', true);
xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

xhr.onload = function() {
    // Handle response
    const response = JSON.parse(xhr.responseText);
    updateUI(response);
};

xhr.send('text=' + encodeURIComponent(text));
```

### Settings Update
```javascript
fetch('update_settings.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => handleResponse(data));
```

### Admin User Management
```javascript
// Add User
fetch('add_user.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => updateUsersList(data));

// Delete User
fetch('delete_user.php', {
    method: 'POST',
    body: JSON.stringify({ id: userId }),
    headers: { 'Content-Type': 'application/json' }
})
.then(response => response.json())
.then(data => removeUserFromList(data));
```

### Summary History Management
```javascript
// View Summary
fetch(`get_summary.php?id=${summaryId}`)
    .then(response => response.json())
    .then(data => showSummaryModal(data));

// Delete Summary
fetch('delete_summary.php', {
    method: 'POST',
    body: JSON.stringify({ id: summaryId })
})
.then(response => response.json())
.then(data => removeSummaryCard(data));
```

## 🔄 AJAX Features

### Modern Implementation
- Uses Fetch API for modern browsers
- XMLHttpRequest for legacy support
- Promise-based async operations
- JSON data handling
- FormData for form submissions

### Error Handling
```javascript
try {
    const response = await fetch(url);
    const data = await response.json();
    if (!data.success) throw new Error(data.message);
    // Handle success
} catch (error) {
    showToast(error.message, 'error');
}
```

### Loading States
```javascript
function showLoader() {
    loader.style.display = 'block';
    submitBtn.disabled = true;
}

function hideLoader() {
    loader.style.display = 'none';
    submitBtn.disabled = false;
}
```

### Response Handling
```javascript
function handleResponse(response) {
    if (response.success) {
        showToast(response.message, 'success');
        updateUI(response.data);
    } else {
        showToast(response.message, 'error');
    }
}
```

## 📝 Development Setup

### IDE Configuration
- **Editor**: Visual Studio Code
- **Extensions Required**:
  - PHP IntelliSense
  - JavaScript Debugger
  - Live Server
  - MySQL

### Windows-Specific Setup
```powershell
# Install dependencies
composer install

# Set up database
mysql -u root -p < database.sql

# Start local server (using PHP built-in server)
php -S localhost:8000

# Check PHP version
php -v

# Verify MySQL connection
mysql -u root -p -e "SELECT VERSION();"
```

### Testing
```powershell
# Run PHP unit tests
.\vendor\bin\phpunit

# Check PHP syntax
php -l index.php

# Validate JavaScript
npx eslint ./assets/js/
```

## 📝 Usage Examples

### Generating a Summary
```php
$summary = summarizeText($longText);
echo "Reduction: " . calculateReduction($longText, $summary) . "%";
```

### Admin API
```php
// Add new user
POST /admin/add_user.php
{
    "username": "john_doe",
    "email": "john@example.com",
    "password": "secure_password"
}
```

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📜 License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## 👥 Authors

- **Your Name** - *Initial work* - [YourGithub](https://github.com/yourusername)

## 🙏 Acknowledgments

- Bootstrap team for the amazing UI framework
- Font Awesome for the icons
- All contributors who helped with the project
