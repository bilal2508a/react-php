# Mehmaan Hub - Property Booking Platform

Pakistan's premier property booking platform with AI-powered recommendations, smart travel checklists, and seamless booking experience.

## Features

### For Guests (Tenants)
- **Browse Properties**: Search and filter properties across 6 Pakistani cities
- **AI Recommendations**: Smart property matching based on preferences
- **Smart Travel Checklist**: Interactive checklist with progress tracking
- **Wishlist**: Save favorite properties for later
- **Booking Management**: View and manage upcoming, completed, and cancelled trips
- **Travel Analytics**: Track spending, savings, and travel patterns
- **Coupon System**: Apply discount codes (EARLY20, STAY7, FAMILY4, WELCOME10)
- **Reviews & Ratings**: Read and leave property reviews
- **Compatibility Score**: AI-powered property match scoring

### For Property Owners
- **Property Management**: Add and manage property listings
- **Booking Requests**: Approve or reject booking requests
- **Earnings Dashboard**: Track gross/net earnings with 5% commission breakdown
- **AI Description Generator**: Auto-generate property descriptions

### For Admins
- **Platform Overview**: User, property, and booking statistics
- **User Management**: View all registered users
- **Property Management**: View and delete properties
- **Booking Management**: Monitor all platform bookings
- **Revenue Tracking**: 5% platform commission tracking

## Tech Stack

- **Backend**: PHP 8.0+ with PDO MySQL
- **Database**: MySQL 8.0+ / MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Styling**: Custom CSS with CSS variables + Bootstrap 5
- **Icons**: Bootstrap Icons
- **Fonts**: Plus Jakarta Sans (Google Fonts)
- **Maps**: OpenStreetMap

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/mehmaan-hub.git
   cd mehmaan-hub
   ```

2. **Set up the database**
   - Open phpMyAdmin or MySQL CLI
   - Import `database/schema.sql` to create the database and seed data
   - Or run: `mysql -u root -p < database/schema.sql`

3. **Configure database connection**
   - Edit `includes/config.php` if your database credentials differ
   - Default: DB_HOST=localhost, DB_NAME=mehmaan_hub, DB_USER=root, DB_PASS=''

4. **Start your PHP server**
   ```bash
   php -S localhost:8000
   ```
   Or use XAMPP/WAMP/MAMP

5. **Access the application**
   - Open http://localhost:8000 in your browser

## Admin Login

- **Email**: admin@mehmaanhub.pk
- **Password**: admin123

## Demo Users

- **Owner**: owner@mehmaanhub.pk / admin123
- **Tenant**: Register a new account as a tenant

## File Structure

```
php-project/
├── includes/
│   ├── config.php          # Database config & helper functions
│   ├── auth.php            # Authentication functions
│   ├── header.php          # Navbar & HTML head
│   ├── footer.php          # Footer & scripts
│   └── property_card.php   # Reusable property card component
├── assets/
│   ├── css/
│   │   └── style.css       # Custom styles
│   └── js/
│       └── main.js         # JavaScript functionality
├── api/
│   ├── toggle-wishlist.php # Add/remove wishlist items
│   ├── create-booking.php  # Create new booking
│   ├── process-payment.php # Process payment
│   ├── booking-action.php  # Approve/reject bookings (owner)
│   └── delete-property.php # Delete property (admin)
├── database/
│   └── schema.sql          # Database schema & seed data
├── uploads/                # User uploads directory
├── index.php               # Home page
├── properties.php          # Property listing page
├── property-details.php    # Single property page
├── booking.php             # Booking form
├── payment.php             # Payment page
├── login.php               # Login page
├── register.php            # Registration page
├── logout.php              # Logout handler
├── dashboard.php           # Tenant dashboard
├── owner-dashboard.php     # Owner dashboard
├── admin.php               # Admin dashboard
├── profile.php             # User profile
├── wishlist.php            # User wishlist
├── bookings.php            # User bookings list
├── add-property.php        # Add property form (owner)
├── about.php               # About page
├── contact.php             # Contact page
├── faq.php                 # FAQ page
├── .htaccess               # Apache config
└── README.md               # This file
```

## Database Schema

### Tables
- **users**: User accounts (tenant, owner, admin roles)
- **properties**: Property listings with amenities, images, ratings
- **bookings**: Booking records with status tracking
- **reviews**: Property reviews and ratings
- **wishlist**: User saved properties
- **contact_messages**: Contact form submissions

## Features in Detail

### AI Recommendation System
Properties are scored based on:
- City match (+30 points)
- Budget match (+25 points)
- Guest capacity match (+20 points)
- High rating ≥4.5 (+15 points)
- Featured status (+10 points)

### Coupon Codes
- `EARLY20`: 20% off (book 30 days in advance)
- `STAY7`: 15% off (stay 7+ nights)
- `FAMILY4`: 10% off (family booking 4+ guests)
- `WELCOME10`: 10% off (new user welcome)

### Service Fee
A 5% service fee is applied to all bookings. For owners, the platform takes a 5% commission from earnings.

## Security Features
- PDO prepared statements (SQL injection prevention)
- Password hashing with bcrypt
- Session-based authentication
- Role-based access control
- HTML output escaping
- .htaccess security headers

## License
MIT License - feel free to use this project for learning or commercial purposes.
