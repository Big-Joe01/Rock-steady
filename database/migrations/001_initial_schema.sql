-- ROCK STEADY Database Schema
-- Version: 1.0.0
-- Run this migration to create all database tables

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS rocksteady CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rocksteady;

-- Drop existing tables
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `wishlists`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `product_images`;
DROP TABLE IF EXISTS `product_variants`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `collections`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `partners`;
DROP TABLE IF EXISTS `sponsorships`;
DROP TABLE IF EXISTS `contacts`;
DROP TABLE IF EXISTS `newsletter`;
DROP TABLE IF EXISTS `pages`;
DROP TABLE IF EXISTS `settings`;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `users`;

-- Users Table
CREATE TABLE `users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `avatar` VARCHAR(500) DEFAULT NULL,
    `role` ENUM('customer', 'admin') DEFAULT 'customer',
    `status` ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    `email_verified_at` TIMESTAMP NULL DEFAULT NULL,
    `remember_token` VARCHAR(100) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_email` (`email`),
    INDEX `idx_users_role` (`role`),
    INDEX `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- User Addresses Table
CREATE TABLE `user_addresses` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `type` ENUM('billing', 'shipping') DEFAULT 'shipping',
    `is_default` TINYINT(1) DEFAULT 0,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `company` VARCHAR(255) DEFAULT NULL,
    `address_line1` VARCHAR(255) NOT NULL,
    `address_line2` VARCHAR(255) DEFAULT NULL,
    `city` VARCHAR(100) NOT NULL,
    `state` VARCHAR(100) DEFAULT NULL,
    `postal_code` VARCHAR(20) NOT NULL,
    `country` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_addresses_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories Table
CREATE TABLE `categories` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `parent_id` INT UNSIGNED DEFAULT NULL,
    `image` VARCHAR(500) DEFAULT NULL,
    `sort_order` INT DEFAULT 0,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    INDEX `idx_categories_slug` (`slug`),
    INDEX `idx_categories_status` (`status`),
    INDEX `idx_categories_parent` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products Table
CREATE TABLE `products` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `sku` VARCHAR(100) NOT NULL UNIQUE,
    `name` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `specifications` TEXT DEFAULT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `sale_price` DECIMAL(10, 2) DEFAULT NULL,
    `cost` DECIMAL(10, 2) DEFAULT NULL,
    `weight` DECIMAL(8, 2) DEFAULT NULL,
    `stock` INT DEFAULT 0,
    `low_stock_threshold` INT DEFAULT 5,
    `category_id` INT UNSIGNED DEFAULT NULL,
    `collection_id` INT UNSIGNED DEFAULT NULL,
    `gender` ENUM('male', 'female', 'unisex') DEFAULT 'unisex',
    `featured` TINYINT(1) DEFAULT 0,
    `is_new` TINYINT(1) DEFAULT 0,
    `trending` TINYINT(1) DEFAULT 0,
    `visibility` ENUM('visible', 'hidden') DEFAULT 'visible',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    INDEX `idx_products_slug` (`slug`),
    INDEX `idx_products_sku` (`sku`),
    INDEX `idx_products_category` (`category_id`),
    INDEX `idx_products_collection` (`collection_id`),
    INDEX `idx_products_featured` (`featured`),
    INDEX `idx_products_is_new` (`is_new`),
    INDEX `idx_products_visibility` (`visibility`),
    INDEX `idx_products_price` (`price`),
    INDEX `idx_products_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product Images Table
CREATE TABLE `product_images` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT UNSIGNED NOT NULL,
    `cloudinary_id` VARCHAR(255) DEFAULT NULL,
    `url` VARCHAR(500) NOT NULL,
    `alt_text` VARCHAR(255) DEFAULT NULL,
    `sort_order` INT DEFAULT 0,
    `is_primary` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    INDEX `idx_images_product` (`product_id`),
    INDEX `idx_images_primary` (`is_primary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Product Variants Table
CREATE TABLE `product_variants` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT UNSIGNED NOT NULL,
    `sku` VARCHAR(100) DEFAULT NULL,
    `size` VARCHAR(50) DEFAULT NULL,
    `color` VARCHAR(50) DEFAULT NULL,
    `color_code` VARCHAR(7) DEFAULT NULL,
    `price_modifier` DECIMAL(10, 2) DEFAULT 0.00,
    `stock` INT DEFAULT 0,
    `image_url` VARCHAR(500) DEFAULT NULL,
    `cloudinary_id` VARCHAR(255) DEFAULT NULL,
    `sku_variant` VARCHAR(100) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    INDEX `idx_variants_product` (`product_id`),
    INDEX `idx_variants_sku` (`sku_variant`),
    INDEX `idx_variants_size` (`size`),
    INDEX `idx_variants_color` (`color`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Reviews Table
CREATE TABLE `reviews` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT UNSIGNED NOT NULL,
    `user_id` INT UNSIGNED NOT NULL,
    `rating` TINYINT NOT NULL CHECK (`rating` BETWEEN 1 AND 5),
    `title` VARCHAR(255) DEFAULT NULL,
    `comment` TEXT DEFAULT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `helpful_count` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_reviews_product` (`product_id`),
    INDEX `idx_reviews_user` (`user_id`),
    INDEX `idx_reviews_rating` (`rating`),
    INDEX `idx_reviews_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Collections Table
CREATE TABLE `collections` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `cover_image` VARCHAR(500) DEFAULT NULL,
    `cloudinary_id` VARCHAR(255) DEFAULT NULL,
    `sort_order` INT DEFAULT 0,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `start_date` DATE DEFAULT NULL,
    `end_date` DATE DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_collections_slug` (`slug`),
    INDEX `idx_collections_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Collection Products (Many-to-Many)
CREATE TABLE `collection_products` (
    `collection_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `sort_order` INT DEFAULT 0,
    PRIMARY KEY (`collection_id`, `product_id`),
    FOREIGN KEY (`collection_id`) REFERENCES `collections`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Wishlists Table
CREATE TABLE `wishlists` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_wishlist` (`user_id`, `product_id`),
    INDEX `idx_wishlists_user` (`user_id`),
    INDEX `idx_wishlists_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders Table
CREATE TABLE `orders` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `order_number` VARCHAR(50) NOT NULL UNIQUE,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `email` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(100) NOT NULL,
    `last_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `subtotal` DECIMAL(10, 2) NOT NULL,
    `shipping_cost` DECIMAL(10, 2) DEFAULT 0.00,
    `tax` DECIMAL(10, 2) DEFAULT 0.00,
    `discount` DECIMAL(10, 2) DEFAULT 0.00,
    `coupon_code` VARCHAR(50) DEFAULT NULL,
    `total` DECIMAL(10, 2) NOT NULL,
    `currency` VARCHAR(3) DEFAULT 'USD',
    `status` ENUM('pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
    `payment_method` VARCHAR(50) DEFAULT NULL,
    `payment_id` VARCHAR(255) DEFAULT NULL,
    `stripe_session_id` VARCHAR(255) DEFAULT NULL,
    `shipping_first_name` VARCHAR(100) DEFAULT NULL,
    `shipping_last_name` VARCHAR(100) DEFAULT NULL,
    `shipping_address_line1` VARCHAR(255) DEFAULT NULL,
    `shipping_address_line2` VARCHAR(255) DEFAULT NULL,
    `shipping_city` VARCHAR(100) DEFAULT NULL,
    `shipping_state` VARCHAR(100) DEFAULT NULL,
    `shipping_postal_code` VARCHAR(20) DEFAULT NULL,
    `shipping_country` VARCHAR(100) DEFAULT NULL,
    `shipping_phone` VARCHAR(20) DEFAULT NULL,
    `billing_first_name` VARCHAR(100) DEFAULT NULL,
    `billing_last_name` VARCHAR(100) DEFAULT NULL,
    `billing_address_line1` VARCHAR(255) DEFAULT NULL,
    `billing_address_line2` VARCHAR(255) DEFAULT NULL,
    `billing_city` VARCHAR(100) DEFAULT NULL,
    `billing_state` VARCHAR(100) DEFAULT NULL,
    `billing_postal_code` VARCHAR(20) DEFAULT NULL,
    `billing_country` VARCHAR(100) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `tracking_number` VARCHAR(255) DEFAULT NULL,
    `tracking_url` VARCHAR(500) DEFAULT NULL,
    `shipped_at` TIMESTAMP NULL DEFAULT NULL,
    `delivered_at` TIMESTAMP NULL DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_orders_number` (`order_number`),
    INDEX `idx_orders_user` (`user_id`),
    INDEX `idx_orders_email` (`email`),
    INDEX `idx_orders_status` (`status`),
    INDEX `idx_orders_created` (`created_at`),
    INDEX `idx_orders_payment_id` (`payment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order Items Table
CREATE TABLE `order_items` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT UNSIGNED NOT NULL,
    `product_id` INT UNSIGNED DEFAULT NULL,
    `variant_id` INT UNSIGNED DEFAULT NULL,
    `sku` VARCHAR(100) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `price` DECIMAL(10, 2) NOT NULL,
    `quantity` INT UNSIGNED NOT NULL,
    `size` VARCHAR(50) DEFAULT NULL,
    `color` VARCHAR(50) DEFAULT NULL,
    `image_url` VARCHAR(500) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`variant_id`) REFERENCES `product_variants`(`id`) ON DELETE SET NULL,
    INDEX `idx_items_order` (`order_id`),
    INDEX `idx_items_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Partners Table
CREATE TABLE `partners` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `company_name` VARCHAR(255) NOT NULL,
    `brand_name` VARCHAR(255) DEFAULT NULL,
    `website` VARCHAR(255) DEFAULT NULL,
    `industry` VARCHAR(100) DEFAULT NULL,
    `country` VARCHAR(100) DEFAULT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `message` TEXT DEFAULT NULL,
    `logo_url` VARCHAR(500) DEFAULT NULL,
    `cloudinary_id` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    `featured` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_partners_status` (`status`),
    INDEX `idx_partners_email` (`email`),
    INDEX `idx_partners_featured` (`featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sponsorships Table
CREATE TABLE `sponsorships` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `brand` VARCHAR(255) DEFAULT NULL,
    `followers` VARCHAR(50) DEFAULT NULL,
    `platforms` JSON DEFAULT NULL,
    `proposal` TEXT DEFAULT NULL,
    `budget` VARCHAR(100) DEFAULT NULL,
    `portfolio` VARCHAR(500) DEFAULT NULL,
    `social_links` JSON DEFAULT NULL,
    `attachments` JSON DEFAULT NULL,
    `email` VARCHAR(255) NOT NULL,
    `status` ENUM('pending', 'reviewed', 'approved', 'rejected') DEFAULT 'pending',
    `notes` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_sponsorships_status` (`status`),
    INDEX `idx_sponsorships_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contact Messages Table
CREATE TABLE `contacts` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `subject` VARCHAR(255) DEFAULT NULL,
    `message` TEXT NOT NULL,
    `status` ENUM('new', 'read', 'replied', 'archived') DEFAULT 'new',
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_contacts_status` (`status`),
    INDEX `idx_contacts_email` (`email`),
    INDEX `idx_contacts_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Newsletter Subscribers Table
CREATE TABLE `newsletter` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `name` VARCHAR(255) DEFAULT NULL,
    `status` ENUM('active', 'unsubscribed') DEFAULT 'active',
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `subscribed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `unsubscribed_at` TIMESTAMP NULL DEFAULT NULL,
    INDEX `idx_newsletter_email` (`email`),
    INDEX `idx_newsletter_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Coupons Table
CREATE TABLE `coupons` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `type` ENUM('percentage', 'fixed') DEFAULT 'percentage',
    `value` DECIMAL(10, 2) NOT NULL,
    `min_order_amount` DECIMAL(10, 2) DEFAULT 0.00,
    `max_discount` DECIMAL(10, 2) DEFAULT NULL,
    `usage_limit` INT DEFAULT NULL,
    `used_count` INT DEFAULT 0,
    `starts_at` TIMESTAMP NULL DEFAULT NULL,
    `expires_at` TIMESTAMP NULL DEFAULT NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_coupons_code` (`code`),
    INDEX `idx_coupons_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Site Settings Table
CREATE TABLE `settings` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT DEFAULT NULL,
    `type` VARCHAR(50) DEFAULT 'text',
    `group` VARCHAR(50) DEFAULT 'general',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_settings_key` (`key`),
    INDEX `idx_settings_group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Activity Logs Table
CREATE TABLE `activity_logs` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(50) DEFAULT NULL,
    `entity_id` INT UNSIGNED DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` TEXT DEFAULT NULL,
    `metadata` JSON DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    INDEX `idx_logs_user` (`user_id`),
    INDEX `idx_logs_action` (`action`),
    INDEX `idx_logs_entity` (`entity_type`, `entity_id`),
    INDEX `idx_logs_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Recently Viewed Products Table
CREATE TABLE `recently_viewed` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `session_id` VARCHAR(100) DEFAULT NULL,
    `product_id` INT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
    INDEX `idx_recently_user` (`user_id`),
    INDEX `idx_recently_session` (`session_id`),
    INDEX `idx_recently_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default settings
INSERT INTO `settings` (`key`, `value`, `type`, `group`) VALUES
('site_name', 'ROCK STEADY', 'text', 'general'),
('site_tagline', 'JUST KEEP ROCKING', 'text', 'general'),
('site_description', 'Premium Rock-Inspired Streetwear Brand', 'textarea', 'general'),
('contact_email', 'rocksteady@gmail.com', 'text', 'contact'),
('contact_phone', '+1 (555) 123-4567', 'text', 'contact'),
('address', '123 Rock Street, Los Angeles, CA 90001', 'textarea', 'contact'),
('social_facebook', 'https://facebook.com/rocksteady', 'text', 'social'),
('social_instagram', 'https://instagram.com/rocksteady', 'text', 'social'),
('social_twitter', 'https://twitter.com/rocksteady', 'text', 'social'),
('social_tiktok', 'https://tiktok.com/@rocksteady', 'text', 'social'),
('shipping_free_threshold', '150.00', 'text', 'shipping'),
('shipping_flat_rate', '9.99', 'text', 'shipping'),
('shipping_express_rate', '24.99', 'text', 'shipping'),
('tax_rate', '0.00', 'text', 'tax'),
('currency', 'USD', 'text', 'general'),
('logo', NULL, 'image', 'appearance'),
('favicon', NULL, 'image', 'appearance'),
('hero_video', NULL, 'text', 'homepage');

-- Insert default categories
INSERT INTO `categories` (`name`, `slug`, `description`, `sort_order`, `status`) VALUES
('Shirts', 'shirts', 'Premium rock-inspired shirts', 1, 'active'),
('Oversized Shirts', 'oversized-shirts', 'Oversized fit shirts', 2, 'active'),
('Jackets', 'jackets', 'Rock-inspired jackets', 3, 'active'),
('Hoodies', 'hoodies', 'Comfortable hoodies', 4, 'active'),
('Sweatshirts', 'sweatshirts', 'Cozy sweatshirts', 5, 'active'),
('Joggers', 'joggers', 'Comfortable joggers', 6, 'active'),
('Trousers', 'trousers', 'Stylish trousers', 7, 'active'),
('Cargo Pants', 'cargo-pants', 'Utility cargo pants', 8, 'active'),
('Shoes', 'shoes', 'Rock-inspired footwear', 9, 'active'),
('Sneakers', 'sneakers', 'Casual sneakers', 10, 'active'),
('Caps', 'caps', 'Stylish caps', 11, 'active'),
('Beanies', 'beanies', 'Warm beanies', 12, 'active'),
('Glasses', 'glasses', 'Sunglasses & Eyewear', 13, 'active'),
('Bags', 'bags', 'Backpacks & Bags', 14, 'active'),
('Accessories', 'accessories', 'Brand accessories', 15, 'active'),
('Limited Edition', 'limited-edition', 'Exclusive limited drops', 16, 'active');

-- Insert default collections
INSERT INTO `collections` (`name`, `slug`, `description`, `sort_order`, `status`) VALUES
('Summer Collection', 'summer-collection', 'Embrace the heat with our summer essentials', 1, 'active'),
('Street Collection', 'street-collection', 'Urban streetwear for the bold', 2, 'active'),
('Performance Collection', 'performance-collection', 'Athletic wear meets rock style', 3, 'active'),
('Mountain Collection', 'mountain-collection', 'Built for adventure, styled for the streets', 4, 'active'),
('Limited Drop', 'limited-drop', 'Exclusive limited edition pieces', 5, 'active'),
('Winter Drop', 'winter-drop', 'Warm up with our winter line', 6, 'active');

SET FOREIGN_KEY_CHECKS = 1;
