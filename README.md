# Zuriel Invoice & Receipt System

A professional PHP-based invoice and receipt management system with pixel-perfect design implementation, modular MVC architecture, and centralized configuration.

## 📋 Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Architecture](#architecture)
- [Folder Structure](#folder-structure)
- [Design Implementation](#design-implementation)
- [Security](#security)
- [Troubleshooting](#troubleshooting)
- [Contributing](#contributing)

---

## ✨ Features

### Core Features

- **Invoice Management**: Create, edit, view, print, and archive invoices
- **Receipt Management**: Generate payment receipts with multiple payment methods
- **Customer Management**: Store and manage customer information
- **Product Catalog**: Pre-define products/services for quick invoice creation
- **Centralized Configuration**: All company details and settings in one place
- **Pixel-Perfect Design**: Exact replication of provided invoice and receipt templates
- **Print-Optimized Views**: Professional PDF-ready print layouts

### Technical Features

- Modular MVC architecture
- CRUD operations for all entities
- Centralized configuration system
- SQL injection prevention with prepared statements
- CSRF protection
- XSS prevention
- Auto-generating invoice and receipt numbers
- Amount to words conversion
- Naira and Kobo currency handling
- Search and filter functionality

---

## 💻 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)
- PDO extension enabled

---

## 🚀 Installation

### Step 1: Clone/Download the Project

```bash
git clone https://github.com/yourusername/zuriel-invoice-system.git
cd zuriel-invoice-system
```

### Step 2: Configure Database Connection

Edit `config/database.php` and update the database credentials:

```php
private $host = 'localhost';
private $dbname = 'zuriel_invoice_system';
private $username = 'root';
private $password = '';
```

### Step 3: Create Database and Import Schema

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE zuriel_invoice_system"

# Import schema
mysql -u root -p zuriel_invoice_system < database/schema.sql
```

Or use phpMyAdmin to import the `schema.sql` file.

### Step 4: Configure Web Server

#### For Apache:

Ensure `.htaccess` is enabled and mod_rewrite is active:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Set your document root to the `public` folder or ensure `.htaccess` redirects work.

#### For Nginx:

Add this to your server block:

```nginx
location / {
    try_files $uri $uri/ /public/index.php?$query_string;
}
```

### Step 5: Set Permissions

```bash
# Make sure the web server can write to the images folder
chmod -R 755 public/images
chown -R www-data:www-data public/images
```

### Step 6: Upload Company Logo

1. Navigate to Settings (`/config`)
2. Upload your company logo (PNG format recommended)
3. The logo will be saved to `public/images/logo.png`

---

## ⚙️ Configuration

All system configuration is managed through the centralized config system.

### Accessing Configuration

Navigate to: **Settings** → `/config`

### Configurable Settings

- **Company Information**

  - Company Name
  - Tagline
  - Logo
  - Address
  - Phone Numbers
  - Email

- **Numbering**

  - Invoice Prefix (e.g., "INV")
  - Invoice Start Number
  - Receipt Prefix (e.g., "RCP")
  - Receipt Start Number

- **Appearance**

  - Primary Color
  - Header Background Color

- **Currency**
  - Major Currency Symbol (₦)
  - Minor Currency Symbol (K)
  - Currency Name (Naira)

### Configuration File Location

Configuration is stored in:

- **Database**: `config` table
- **PHP Config**: `config/config.php`

### How Configuration Works

All configuration values are loaded from the database on each request:

```php
// Getting configuration values
$companyName = Config::get('COMPANY_NAME');
$logo = Config::get('COMPANY_LOGO');

// Setting configuration values
Config::update($db, 'COMPANY_NAME', 'New Company Name');
```

**Never hardcode values in the code!** Always reference the config file.

---

## 📖 Usage

### Creating an Invoice

1. Navigate to **Invoices** → **Create New Invoice**
2. Fill in customer details (or select existing customer)
3. Add invoice items:
   - Enter quantity
   - Type product description (autocomplete available)
   - Rate auto-fills if product exists
4. Click **Create Invoice**
5. Print or view the invoice

### Creating a Receipt

1. Navigate to **Receipts** → **Create New Receipt**
2. Fill in:
   - Date
   - Received from (customer name)
   - Total amount
   - Payment purpose
   - Payment method (Cash/Transfer/POS/Other)
3. Click **Create Receipt**
4. Print or view the receipt

### Managing Customers

1. Navigate to **Customers**
2. Add customer details:
   - Name
   - Address
   - Phone
   - Email
3. View customer invoice history
4. Edit or delete customers

### Managing Products

1. Navigate to **Products**
2. Add products/services:
   - Description
   - Rate (price)
3. Products will appear in autocomplete when creating invoices

---

## 🏗️ Architecture

### MVC Pattern

The application follows a strict Model-View-Controller pattern:

```
Request → Router → Controller → Model → Database
                      ↓
                    View → Response
```

### Components

#### Models

- Handle all database operations
- Business logic
- Data validation
- Located in: `app/models/`

#### Views

- Display layer
- HTML templates with PHP
- Located in: `app/views/`

#### Controllers

- Request handling
- Input validation
- Coordinate between models and views
- Located in: `app/controllers/`

#### Router

- URL routing
- Request dispatching
- Located in: `public/index.php`

---

## 📁 Folder Structure

```
zuriel-invoice-system/
│
├── app/
│   ├── controllers/
│   │   ├── Controller.php          # Base controller
│   │   ├── InvoiceController.php   # Invoice CRUD
│   │   ├── ReceiptController.php   # Receipt CRUD
│   │   ├── CustomerController.php  # Customer CRUD
│   │   ├── ProductController.php   # Product CRUD
│   │   └── ConfigController.php    # System settings
│   │
│   ├── models/
│   │   ├── Model.php               # Base model
│   │   ├── Invoice.php             # Invoice model
│   │   ├── Receipt.php             # Receipt model
│   │   ├── Customer.php            # Customer model
│   │   └── Product.php             # Product model
│   │
│   └── views/
│       ├── layouts/
│       │   ├── header.php          # Common header
│       │   └── footer.php          # Common footer
│       ├── invoices/
│       │   ├── index.php           # Invoice list
│       │   ├── create.php          # Create invoice form
│       │   ├── edit.php            # Edit invoice form
│       │   ├── show.php            # Invoice details
│       │   └── print.php           # Print template
│       ├── receipts/
│       │   ├── index.php
│       │   ├── create.php
│       │   ├── edit.php
│       │   ├── show.php
│       │   └── print.php
│       ├── customers/
│       ├── products/
│       └── config/
│
├── config/
│   ├── config.php                  # Central configuration
│   └── database.php                # Database connection
│
├── database/
│   └── schema.sql                  # Database schema
│
├── public/
│   ├── index.php                   # Front controller/router
│   ├── css/                        # Custom CSS
│   ├── js/                         # Custom JavaScript
│   └── images/                     # Uploaded images
│
├── .htaccess                       # Apache rewrite rules
└── README.md                       # This file
```

---

## 🎨 Design Implementation

### Invoice Design

The invoice print view (`app/views/invoices/print.php`) implements:

- ✅ Exact company header with logo and contact box
- ✅ Blue color scheme matching design
- ✅ Cash/Credit invoice badge
- ✅ Customer details section
- ✅ Invoice items table with QTY, Description, Rate, Amount (₦ and K)
- ✅ Rounded corners on invoice table
- ✅ Total calculation with separate Naira and Kobo columns
- ✅ Amount in words
- ✅ Customer and Manager signature lines
- ✅ "Received the above goods in good condition" footer note

### Receipt Design

The receipt print view (`app/views/receipts/print.php`) implements:

- ✅ Company header with blue contact box
- ✅ "Payment Receipt" badge with receipt number
- ✅ Form-style fields (Received from, Sum of, Payment for)
- ✅ Payment method checkboxes (Cash, Transfer, POS, Other)
- ✅ Large amount display box with ₦ : K format
- ✅ Customer and Manager signature lines
- ✅ Blue footer bar

### Print Styling

Both print views include:

- A4 paper size optimization
- Print button (hidden when printing)
- No navigation or admin interface in print view
- Professional layout matching designs exactly

---

## 🔒 Security

### Implemented Security Measures

#### 1. SQL Injection Prevention

```php
// All database queries use prepared statements
$stmt = $db->prepare("SELECT * FROM invoices WHERE id = :id");
$stmt->execute(['id' => $id]);
```

#### 2. CSRF Protection

```php
// All forms include CSRF token
<input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

// Validated in controller
$this->validateCsrfToken();
```

#### 3. XSS Prevention

```php
// All output is escaped
echo htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
```

#### 4. Input Sanitization

```php
// All input is sanitized
$data = $this->sanitize($this->input('field_name'));
```

#### 5. File Upload Security

- Only image files allowed for logo upload
- Files stored in designated public folder
- File type validation

#### 6. Access Control

- Session-based authentication ready
- Archive instead of delete for audit trail

---

## 🐛 Troubleshooting

### Database Connection Failed

**Error**: "Database connection failed"

**Solution**:

1. Check database credentials in `config/database.php`
2. Ensure MySQL is running: `sudo systemctl status mysql`
3. Verify database exists: `mysql -u root -p -e "SHOW DATABASES;"`

### 404 Page Not Found

**Error**: All pages show 404

**Solution**:

1. Check if `.htaccess` exists in root folder
2. Enable mod_rewrite: `sudo a2enmod rewrite`
3. Check Apache config allows `.htaccess` overrides:
   ```apache
   <Directory /var/www/html>
       AllowOverride All
   </Directory>
   ```
4. Restart Apache: `sudo systemctl restart apache2`

### Images Not Uploading

**Error**: Logo upload fails

**Solution**:

1. Check folder permissions: `chmod 755 public/images`
2. Check ownership: `chown www-data:www-data public/images`
3. Check PHP upload settings in `php.ini`:
   ```ini
   upload_max_filesize = 10M
   post_max_size = 10M
   ```

### Print View Not Styled Correctly

**Issue**: Print view doesn't match design

**Solution**:

1. Clear browser cache
2. Check if accessing correct URL: `/invoices/print/{id}`
3. Ensure print CSS is loading (check browser console)
4. Try different browser

### Configuration Not Saving

**Issue**: Settings changes don't persist

**Solution**:

1. Check database connection
2. Verify `config` table exists
3. Check web server can write to database
4. Check for database errors in browser console

---

## 📝 Database Schema

### Main Tables

#### invoices

```sql
- id (Primary Key)
- invoice_number (Unique)
- customer_id (Foreign Key)
- customer_name
- customer_address
- invoice_date
- lpo_number
- subtotal
- total
- amount_in_words
- invoice_type (cash/credit)
- status (draft/issued/paid/archived)
- created_at
- updated_at
```

#### invoice_items

```sql
- id (Primary Key)
- invoice_id (Foreign Key)
- qty
- description
- rate
- amount
```

#### receipts

```sql
- id (Primary Key)
- receipt_number (Unique)
- receipt_date
- received_from
- amount_naira
- amount_kobo
- total_amount
- payment_for
- payment_method (cash/transfer/pos/other)
- status
- created_at
- updated_at
```

#### customers

```sql
- id (Primary Key)
- name
- address
- phone
- email
- created_at
- updated_at
```

#### products

```sql
- id (Primary Key)
- description
- rate
- created_at
- updated_at
```

#### config

```sql
- config_key (Primary Key)
- config_value
- updated_at
```

---

## 🔄 Extending the System

### Adding New Features

#### 1. Add a New Model

```php
// app/models/NewModel.php
class NewModel extends Model {
    protected $table = 'new_table';

    // Add custom methods
}
```

#### 2. Add a New Controller

```php
// app/controllers/NewController.php
class NewController extends Controller {
    public function index() {
        // Your code
    }
}
```

#### 3. Add Routes

Edit `public/index.php` and add routes:

```php
'GET /new-route' => ['controller' => 'NewController', 'action' => 'index']
```

#### 4. Add Views

Create views in `app/views/new-feature/`

---

## 🎯 Best Practices

### 1. Always Use Config

❌ **Wrong**:

```php
echo "ZURIEL TECH VENTURES";
```

✅ **Correct**:

```php
echo Config::get('COMPANY_NAME');
```

### 2. Use Prepared Statements

❌ **Wrong**:

```php
$sql = "SELECT * FROM users WHERE id = " . $_GET['id'];
```

✅ **Correct**:

```php
$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $_GET['id']]);
```

### 3. Sanitize Output

❌ **Wrong**:

```php
echo $user['name'];
```

✅ **Correct**:

```php
echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8');
```

### 4. Validate Input

❌ **Wrong**:

```php
$email = $_POST['email'];
```

✅ **Correct**:

```php
$email = $this->sanitize($this->input('email'));
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Handle error
}
```

---

## 📊 Performance Tips

1. **Enable OpCache** in `php.ini`:

   ```ini
   opcache.enable=1
   opcache.memory_consumption=128
   ```

2. **Optimize Database**:

   ```sql
   -- Add indexes
   CREATE INDEX idx_invoice_number ON invoices(invoice_number);
   CREATE INDEX idx_receipt_number ON receipts(receipt_number);
   CREATE INDEX idx_customer_name ON customers(name);
   ```

3. **Enable Compression** in `.htaccess`:
   ```apache
   <IfModule mod_deflate.c>
       AddOutputFilterByType DEFLATE text/html text/css application/javascript
   </IfModule>
   ```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/new-feature`
3. Commit your changes: `git commit -am 'Add new feature'`
4. Push to the branch: `git push origin feature/new-feature`
5. Submit a pull request

### Code Style

- Follow PSR-12 coding standards
- Use meaningful variable names
- Add comments for complex logic
- Write clean, readable code

---

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

---

## 👥 Support

For support, please:

1. Check the [Troubleshooting](#troubleshooting) section
2. Open an issue on GitHub
3. Contact: support@example.com

---

## 🎉 Acknowledgments

- Bootstrap 5 for UI components
- Bootstrap Icons for iconography
- Zuriel Tech Ventures for the design template

---

## 📅 Version History

### Version 1.0.0 (Current)

- Initial release
- Invoice management
- Receipt management
- Customer management
- Product catalog
- Centralized configuration
- Print-optimized views

---

## 🚀 Future Enhancements

- [ ] Multi-user authentication and roles
- [ ] Email notifications
- [ ] PDF export (instead of print)
- [ ] Dashboard with statistics
- [ ] Payment tracking and reminders
- [ ] Multi-currency support
- [ ] Invoice templates
- [ ] Backup and restore functionality
- [ ] API for external integrations
- [ ] Mobile responsive admin panel

---

**Built with ❤️ for Zuriel Tech Ventures**
