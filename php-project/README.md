# Mehmaan Hub - Property Booking Platform

Pakistan's premier property booking platform with AI-powered recommendations, smart travel checklists, and seamless booking experience.

## XAMPP Setup (Complete Guide)

This project uses **XAMPP Control Panel** — the same Apache + MySQL you already use. Here's how to run it:

### Step 1: Start XAMPP
1. Open **XAMPP Control Panel**
2. Click **Start** next to **Apache**
3. Click **Start** next to **MySQL**

### Step 2: Copy Project Files
1. Copy the entire `php-project` folder into your XAMPP htdocs directory:
   - Windows: `C:\xampp\htdocs\php-project`
   - Mac: `/Applications/XAMPP/htdocs/php-project`
   - Linux: `/opt/lampp/htdocs/php-project`

### Step 3: Import Database (one-time only)
1. Open browser and go to `http://localhost/phpmyadmin`
2. Click the **Import** tab at the top
3. Click **Choose File** and select `php-project/database/schema.sql`
4. Click **Go** (bottom right)
5. This will automatically create:
   - The `mehmaan_hub` database
   - All 6 tables (users, properties, bookings, reviews, wishlist, contact_messages)
   - 10 seed properties across Pakistani cities
   - Admin and owner user accounts
   - 5 sample reviews

**That's it!** The database is now in MySQL and all data lives there. You don't need schema.sql again unless you want to reset the database.

### Step 4: Open the App
- Go to `http://localhost/php-project/` in your browser
- The app is now running with MySQL database

### Database Config (already set for XAMPP defaults)
File: `php-project/includes/config.php`
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mehmaan_hub');
define('DB_USER', 'root');     // XAMPP default
define('DB_PASS', '');          // XAMPP default (empty password)
```
If your MySQL has a different password, change `DB_PASS` in this file.

## Login Credentials

| Role   | Email                  | Username | Password   |
|--------|------------------------|----------|------------|
| Admin  | admin@mehmaanhub.pk    | admin    | admin123   |
| Owner  | owner@mehmaanhub.pk    | owner    | admin123   |
| Tenant | tenant@mehmaanhub.pk   | tenant   | admin123   |

You can log in using either your **email** or **username**.

## Features

### For Guests (Tenants)
- Browse and filter properties across 6 Pakistani cities
- AI-powered property recommendations
- Smart travel checklist with progress tracking
- Save properties to wishlist
- Booking management (upcoming, completed, cancelled)
- Travel analytics (spending, savings, patterns)
- Coupon system (EARLY20, STAY7, FAMILY4, WELCOME10)
- Compatibility score for each property

### For Property Owners
- Add and manage property listings
- AI description generator
- Approve or reject booking requests
- Earnings dashboard with 5% commission breakdown

### For Admins
- Platform-wide statistics
- User, property, and booking management
- Revenue tracking (5% platform commission)

## Tech Stack

- **Backend**: PHP 8.0+ with PDO MySQL
- **Database**: MySQL / MariaDB (via XAMPP)
- **Frontend**: Bootstrap 5 + custom CSS
- **Icons**: Bootstrap Icons
- **Fonts**: Plus Jakarta Sans (Google Fonts)
- **Maps**: OpenStreetMap

## File Structure

```
php-project/
├── includes/
│   ├── config.php          # Database config & helper functions
│   ├── auth.php            # Authentication functions
│   ├── header.php          # Navbar
│   ├── footer.php          # Footer
│   └── property_card.php   # Reusable property card
├── assets/
│   ├── css/style.css       # Custom styles
│   └── js/main.js          # JavaScript
├── api/
│   ├── toggle-wishlist.php
│   ├── create-booking.php
│   ├── process-payment.php
│   ├── booking-action.php
│   └── delete-property.php
├── database/
│   └── schema.sql          # Import this in phpMyAdmin (one-time)
├── index.php               # Home page
├── properties.php          # Property listings
├── property-details.php    # Property detail page
├── booking.php             # Booking form
├── payment.php             # Payment page
├── login.php               # Login
├── register.php            # Registration
├── logout.php              # Logout
├── dashboard.php           # Tenant dashboard
├── owner-dashboard.php     # Owner dashboard
├── admin.php               # Admin panel
├── profile.php             # User profile
├── wishlist.php            # Wishlist
├── bookings.php            # My bookings
├── add-property.php        # Add property (owner)
├── about.php               # About page
├── contact.php             # Contact page
└── faq.php                 # FAQ page
```

## Database Tables

| Table              | Purpose                                    |
|--------------------|--------------------------------------------|
| users              | User accounts (tenant, owner, admin)       |
| properties         | Property listings with amenities & images  |
| bookings           | Booking records with status tracking       |
| reviews            | Property reviews and ratings               |
| wishlist           | User saved properties                      |
| contact_messages   | Contact form submissions                   |

## Coupon Codes

| Code       | Discount | Description                    |
|------------|----------|--------------------------------|
| EARLY20    | 20% off  | Book 30 days in advance        |
| STAY7      | 15% off  | Stay 7+ nights                 |
| FAMILY4    | 10% off  | Family booking (4+ guests)     |
| WELCOME10  | 10% off  | New user welcome               |

## License

MIT License - feel free to use this project for learning or commercial purposes.
