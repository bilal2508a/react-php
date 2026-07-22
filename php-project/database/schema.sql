-- Mehmaan Hub - Database Schema
-- MySQL Database for Property Booking Platform

CREATE DATABASE IF NOT EXISTS mehmaan_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mehmaan_hub;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    avatar_url VARCHAR(500) DEFAULT NULL,
    travel_personality ENUM('adventurer','relaxer','explorer','foodie') DEFAULT 'explorer',
    role ENUM('tenant','owner','admin') NOT NULL DEFAULT 'tenant',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Properties Table
CREATE TABLE IF NOT EXISTS properties (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    city VARCHAR(100) NOT NULL,
    area VARCHAR(100) NOT NULL,
    property_type ENUM('guest_house','apartment','villa','hotel') NOT NULL,
    price_per_night DECIMAL(10,2) NOT NULL,
    bedrooms INT NOT NULL DEFAULT 1,
    bathrooms INT NOT NULL DEFAULT 1,
    max_guests INT NOT NULL DEFAULT 2,
    amenities TEXT,
    images TEXT,
    rating DECIMAL(2,1) DEFAULT 0.0,
    review_count INT DEFAULT 0,
    latitude DECIMAL(10,7) DEFAULT NULL,
    longitude DECIMAL(10,7) DEFAULT NULL,
    owner_id INT NOT NULL,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Bookings Table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    user_id INT NOT NULL,
    check_in DATE NOT NULL,
    check_out DATE NOT NULL,
    guests INT NOT NULL DEFAULT 1,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','completed','cancelled') DEFAULT 'pending',
    owner_status ENUM('pending','approved','rejected') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT NULL,
    payment_status ENUM('unpaid','paid','refunded') DEFAULT 'unpaid',
    guest_name VARCHAR(255) NOT NULL,
    guest_email VARCHAR(255) NOT NULL,
    guest_phone VARCHAR(50) DEFAULT NULL,
    special_requests TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    property_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Wishlist Table
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    property_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wishlist (user_id, property_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Seed Admin User
-- Password: admin123 (bcrypt hash)
INSERT INTO users (full_name, email, password, phone, travel_personality, role) VALUES
('Admin User', 'admin@mehmaanhub.pk', '$2y$10$N9qo8uLOickgx2ZMRZoMy.MQDq/1JoC9r0aFyLZ5pQXJmR5yLZ5pQ', '+92 300 1234567', 'explorer', 'admin');

-- Seed Owner User
INSERT INTO users (full_name, email, password, phone, travel_personality, role) VALUES
('Property Owner', 'owner@mehmaanhub.pk', '$2y$10$N9qo8uLOickgx2ZMRZoMy.MQDq/1JoC9r0aFyLZ5pQXJmR5yLZ5pQ', '+92 321 9876543', 'adventurer', 'owner');

-- Seed Properties (10 properties across Pakistani cities)
INSERT INTO properties (title, description, city, area, property_type, price_per_night, bedrooms, bathrooms, max_guests, amenities, images, rating, review_count, owner_id, is_featured) VALUES
('Luxury Sea View Apartment', 'Stunning apartment with panoramic views of the Arabian Sea. Modern amenities and prime location near Clifton Beach.', 'Karachi', 'Clifton', 'apartment', 15000, 2, 2, 4, 'WiFi,Air Conditioning,Kitchen,Free Parking,Sea View,Balcony,TV,Workspace', 'https://images.pexels.com/photos/1571460/pexels-photo-1571460.jpeg,https://images.pexels.com/photos/1457847/pexels-photo-1457847.jpeg,https://images.pexels.com/photos/1086328/pexels-photo-1086328.jpeg', 4.8, 24, 2, TRUE),
('Hunza Valley Guest House', 'Traditional guest house with breathtaking mountain views in the heart of Hunza Valley. Perfect for nature lovers.', 'Hunza', 'Karimabad', 'guest_house', 8000, 3, 2, 6, 'WiFi,Heating,Kitchen,Free Parking,Mountain View,Garden,Breakfast,Family Friendly', 'https://images.pexels.com/photos/2901209/pexels-photo-2901209.jpeg,https://images.pexels.com/photos/2422259/pexels-photo-2422259.jpeg,https://images.pexels.com/photos/247204/pexels-photo-247204.jpeg', 4.9, 31, 2, TRUE),
('Murree Hills Villa', 'Cozy villa nestled in the hills of Murree. Enjoy fresh mountain air and scenic views from your private garden.', 'Murree', 'Gulberg Gali', 'villa', 12000, 4, 3, 8, 'WiFi,Fireplace,Heating,Kitchen,Free Parking,Mountain View,Garden,BBQ Grill,Family Friendly', 'https://images.pexels.com/photos/247204/pexels-photo-247204.jpeg,https://images.pexels.com/photos/1396122/pexels-photo-1396122.jpeg,https://images.pexels.com/photos/1571460/pexels-photo-1571460.jpeg', 4.7, 18, 2, TRUE),
('Islamabad Modern Apartment', 'Contemporary apartment in the diplomatic sector of Islamabad. Close to F-7 Markaz and Margalla Hills.', 'Islamabad', 'F-7', 'apartment', 10000, 2, 2, 4, 'WiFi,Air Conditioning,Kitchen,Free Parking,Workspace,Gym,Balcony,City View,24/7 Security', 'https://images.pexels.com/photos/1457847/pexels-photo-1457847.jpeg,https://images.pexels.com/photos/1086328/pexels-photo-1086328.jpeg,https://images.pexels.com/photos/1571460/pexels-photo-1571460.jpeg', 4.6, 22, 2, FALSE),
('Lahore Heritage Hotel', 'Experience Mughal-era charm in this heritage hotel located in the heart of old Lahore near Badshahi Mosque.', 'Lahore', 'Walled City', 'hotel', 18000, 1, 1, 2, 'WiFi,Air Conditioning,Room Service,Breakfast,24/7 Security,TV,Cable TV,Workspace', 'https://images.pexels.com/photos/2699348/pexels-photo-2699348.jpeg,https://images.pexels.com/photos/2029722/pexels-photo-2029722.jpeg,https://images.pexels.com/photos/3225531/pexels-photo-3225531.jpeg', 4.5, 15, 2, TRUE),
('Skardu Lake View Villa', 'Luxurious villa with stunning views of Shangrila Lake. Perfect base for exploring Skardu and the Karakoram.', 'Skardu', 'Shangrila', 'villa', 20000, 5, 4, 10, 'WiFi,Heating,Kitchen,Free Parking,Mountain View,Sea View,Garden,BBQ Grill,Fireplace,Family Friendly,Pet Friendly', 'https://images.pexels.com/photos/2422259/pexels-photo-2422259.jpeg,https://images.pexels.com/photos/2901209/pexels-photo-2901209.jpeg,https://images.pexels.com/photos/247204/pexels-photo-247204.jpeg', 5.0, 12, 2, TRUE),
('Karachi Beachfront Guest House', 'Charming guest house steps away from Clifton Beach. Relax with ocean views and easy beach access.', 'Karachi', 'Clifton', 'guest_house', 6000, 2, 1, 4, 'WiFi,Air Conditioning,Kitchen,Sea View,Balcony,Breakfast,Family Friendly', 'https://images.pexels.com/photos/1086328/pexels-photo-1086328.jpeg,https://images.pexels.com/photos/1457847/pexels-photo-1457847.jpeg,https://images.pexels.com/photos/2699348/pexels-photo-2699348.jpeg', 4.3, 19, 2, FALSE),
('Lahore Garden Apartment', 'Spacious apartment with private garden in Gulberg III. Ideal for families visiting Lahore.', 'Lahore', 'Gulberg III', 'apartment', 11000, 3, 2, 6, 'WiFi,Air Conditioning,Kitchen,Free Parking,Garden,Washing Machine,TV,Family Friendly,Wheelchair Accessible', 'https://images.pexels.com/photos/2029722/pexels-photo-2029722.jpeg,https://images.pexels.com/photos/3225531/pexels-photo-3225531.jpeg,https://images.pexels.com/photos/1571460/pexels-photo-1571460.jpeg', 4.4, 16, 2, FALSE),
('Islamabad Hilltop Hotel', 'Premium hotel with panoramic views of Margalla Hills. Top-rated hospitality and modern facilities.', 'Islamabad', 'F-6', 'hotel', 22000, 1, 1, 2, 'WiFi,Air Conditioning,Room Service,Breakfast,Gym,Swimming Pool,24/7 Security,TV,Netflix,Workspace', 'https://images.pexels.com/photos/3225531/pexels-photo-3225531.jpeg,https://images.pexels.com/photos/2699348/pexels-photo-2699348.jpeg,https://images.pexels.com/photos/2029722/pexels-photo-2029722.jpeg', 4.9, 28, 2, TRUE),
('Hunza Riverside Guest House', 'Peaceful guest house by the Hunza River. Enjoy tranquility and stunning natural surroundings.', 'Hunza', 'Altit', 'guest_house', 5000, 2, 1, 4, 'WiFi,Heating,Kitchen,Mountain View,Garden,Breakfast,Family Friendly,Pet Friendly', 'https://images.pexels.com/photos/247204/pexels-photo-247204.jpeg,https://images.pexels.com/photos/2422259/pexels-photo-2422259.jpeg,https://images.pexels.com/photos/2901209/pexels-photo-2901209.jpeg', 4.6, 14, 2, FALSE);

-- Seed Reviews (5 reviews)
INSERT INTO reviews (property_id, user_id, rating, comment) VALUES
(1, 1, 5, 'Absolutely stunning views and the apartment was spotless. Will definitely book again!'),
(2, 1, 5, 'The mountain views from this guest house are unreal. Host was incredibly welcoming.'),
(3, 1, 4, 'Cozy villa, perfect for a family getaway. The fireplace was a lovely touch.'),
(5, 1, 5, 'Staying in a heritage hotel near Badshahi Mosque was a dream come true. Highly recommended!'),
(6, 1, 5, 'The villa exceeded all expectations. Lake views are breathtaking and the staff was amazing.');
