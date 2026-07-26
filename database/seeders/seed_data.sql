-- ROCK STEADY Seed Data
-- Run after migrations

USE rocksteady;

-- Insert sample products
INSERT INTO `products` (`sku`, `name`, `slug`, `description`, `specifications`, `price`, `sale_price`, `weight`, `stock`, `category_id`, `gender`, `featured`, `is_new`, `trending`, `visibility`, `created_at`) VALUES

-- Shirts
('RST-SHT-001', 'Classic Rock Tee', 'classic-rock-tee', 'Premium cotton t-shirt with distressed rock graphic. Features a relaxed fit and superior comfort.', 'Material: 100% Cotton\nFit: Regular\nCare: Machine wash cold', 49.99, NULL, 0.25, 150, 1, 'unisex', 1, 0, 0, 'visible', NOW() - INTERVAL 10 DAY),
('RST-SHT-002', 'Vintage Band Tee', 'vintage-band-tee', 'Vintage-wash t-shirt featuring iconic band imagery. Each piece is uniquely distressed.', 'Material: 100% Cotton\nFit: Relaxed\nCare: Hand wash only', 59.99, 44.99, 0.28, 75, 1, 'unisex', 1, 1, 1, 'visible', NOW() - INTERVAL 5 DAY),
('RST-SHT-003', 'Logo Print Tee', 'logo-print-tee', 'Clean minimalist tee with embossed ROCK STEADY logo. Essential everyday wear.', 'Material: Premium Cotton Blend\nFit: Regular\nCare: Machine wash cold', 39.99, NULL, 0.22, 200, 1, 'unisex', 0, 0, 0, 'visible', NOW() - INTERVAL 30 DAY),

-- Oversized Shirts
('RST-OVS-001', 'Heavy Metal Oversized', 'heavy-metal-oversized', 'Bold oversized t-shirt with heavy metal inspired artwork. Drop shoulder fit.', 'Material: 100% Organic Cotton\nFit: Oversized\nCare: Machine wash cold', 69.99, NULL, 0.35, 100, 2, 'unisex', 1, 1, 1, 'visible', NOW() - INTERVAL 3 DAY),
('RST-OVS-002', 'Tour Statement Oversized', 'tour-statement-oversized', 'Oversized tee inspired by legendary tour graphics. Perfect layering piece.', 'Material: Cotton/Polyester Blend\nFit: Oversized\nCare: Machine wash cold', 64.99, 54.99, 0.32, 80, 2, 'unisex', 0, 0, 0, 'visible', NOW() - INTERVAL 20 DAY),

-- Jackets
('RST-JKT-001', 'Biker Leather Jacket', 'biker-leather-jacket', 'Premium faux leather biker jacket with metal hardware. Asymmetric zip closure.', 'Material: Faux Leather\nLining: Polyester\nFit: Regular\nCare: Wipe clean only', 199.99, 179.99, 1.2, 45, 3, 'unisex', 1, 0, 1, 'visible', NOW() - INTERVAL 15 DAY),
('RST-JKT-002', 'Denim Trucker Jacket', 'denim-trucker-jacket', 'Classic denim trucker with modern rock detailing. Multiple pocket design.', 'Material: 100% Denim\nFit: Relaxed\nCare: Machine wash cold', 129.99, NULL, 0.8, 60, 3, 'unisex', 0, 0, 0, 'visible', NOW() - INTERVAL 45 DAY),
('RST-JKT-003', 'Varsity Bomber', 'varsity-bomber', 'Rock-inspired varsity jacket with embroidered patches. Wool blend body.', 'Material: Wool/Polyester\nSleeves: Faux Leather\nFit: Relaxed', 249.99, NULL, 1.0, 35, 3, 'unisex', 1, 1, 0, 'visible', NOW() - INTERVAL 8 DAY),

-- Hoodies
('RST-HOD-001', 'Essential Pullover Hoodie', 'essential-pullover-hoodie', 'Premium fleece hoodie with kangaroo pocket. Soft inside for maximum comfort.', 'Material: 80% Cotton, 20% Polyester\nFit: Relaxed\nCare: Machine wash cold', 89.99, 79.99, 0.5, 120, 4, 'unisex', 1, 1, 1, 'visible', NOW() - INTERVAL 7 DAY),
('RST-HOD-002', 'Graphic Print Hoodie', 'graphic-print-hoodie', 'All-over print hoodie with rock-inspired graphics. Drawstring hood.', 'Material: 100% Cotton\nFit: Oversized\nCare: Machine wash cold inside out', 99.99, NULL, 0.55, 85, 4, 'unisex', 0, 0, 0, 'visible', NOW() - INTERVAL 25 DAY),
('RST-HOD-003', 'Zip Through Hoodie', 'zip-through-hoodie', 'Full zip hoodie with tonal branding. Perfect for layering.', 'Material: Cotton/Polyester Blend\nFit: Regular\nCare: Machine wash cold', 94.99, NULL, 0.48, 90, 4, 'unisex', 0, 0, 0, 'visible', NOW() - INTERVAL 40 DAY),

-- Sweatshirts
('RST-SWT-001', 'Crewneck Sweatshirt', 'crewneck-sweatshirt', 'Classic crewneck with embroidered logo. Premium heavyweight cotton.', 'Material: 100% Cotton\nFit: Relaxed\nCare: Machine wash cold', 79.99, NULL, 0.45, 110, 5, 'unisex', 0, 0, 0, 'visible', NOW() - INTERVAL 35 DAY),
('RST-SWT-002', 'Reaper Print Sweatshirt', 'reaper-print-sweatshirt', 'Striking reaper graphic on heavyweight sweatshirt. Limited edition.', 'Material: 80% Cotton, 20% Polyester\nFit: Relaxed\nCare: Machine wash cold inside out', 89.99, 74.99, 0.48, 50, 5, 'unisex', 1, 1, 0, 'visible', NOW() - INTERVAL 4 DAY),

-- Joggers
('RST-JOG-001', 'Tech Joggers', 'tech-joggers', 'Performance joggers with zip pockets. Moisture-wicking fabric.', 'Material: Polyester/Elastane\nFit: Slim\nCare: Machine wash cold', 84.99, NULL, 0.35, 95, 6, 'unisex', 0, 0, 1, 'visible', NOW() - INTERVAL 50 DAY),
('RST-JOG-002', 'Classic Sweatpants', 'classic-sweatpants', 'Comfortable cotton sweatpants with elastic waistband. Perfect for off-duty.', 'Material: 100% Cotton French Terry\nFit: Relaxed\nCare: Machine wash cold', 69.99, 59.99, 0.4, 130, 6, 'unisex', 0, 0, 0, 'visible', NOW() - INTERVAL 60 DAY),

-- Cargo Pants
('RST-CRG-001', 'Tactical Cargo Pants', 'tactical-cargo-pants', 'Multi-pocket cargo pants with adjustable waist. Durable ripstop fabric.', 'Material: 65% Polyester, 35% Cotton\nFit: Relaxed\nCare: Machine wash cold', 109.99, NULL, 0.55, 70, 7, 'unisex', 1, 0, 0, 'visible', NOW() - INTERVAL 22 DAY),
('RST-CRG-002', 'Utility Cargo Joggers', 'utility-cargo-joggers', 'Hybrid cargo and jogger design. Side cargo pockets with velcro closure.', 'Material: Nylon/Spandex\nFit: Slim\nCare: Machine wash cold', 94.99, 84.99, 0.4, 85, 7, 'unisex', 0, 0, 1, 'visible', NOW() - INTERVAL 12 DAY),

-- Shoes
('RST-SHO-001', 'Rock Combat Boots', 'rock-combat-boots', 'Chunky sole combat boots with metal hardware. Steel toe option available.', 'Material: Faux Leather\nSole: Rubber\nFit: True to size', 159.99, NULL, 1.0, 40, 9, 'unisex', 1, 0, 1, 'visible', NOW() - INTERVAL 18 DAY),
('RST-SHO-002', 'Platform Sneakers', 'platform-sneakers', 'High-top platform sneakers with cushioned sole. Perfect street style.', 'Material: Canvas/Faux Leather\nSole: EVA\nFit: True to size', 119.99, 99.99, 0.6, 65, 10, 'unisex', 0, 1, 0, 'visible', NOW() - INTERVAL 6 DAY),

-- Accessories
('RST-ACC-001', 'Classic Snapback Cap', 'classic-snapback-cap', 'Premium snapback cap with embroidered logo. Adjustable fit.', 'Material: Cotton Twill\nClosure: Snapback\nOne size fits most', 34.99, NULL, 0.15, 200, 11, 'unisex', 0, 0, 0, 'visible', NOW() - INTERVAL 55 DAY),
('RST-ACC-002', 'Skull Beanie', 'skull-beanie', 'Knit beanie with skull embroidery. Soft acrylic blend.', 'Material: Acrylic Blend\nFit: Stretch\nOne size fits most', 29.99, 24.99, 0.1, 150, 12, 'unisex', 0, 0, 0, 'visible', NOW() - INTERVAL 70 DAY),
('RST-ACC-003', 'Aviator Sunglasses', 'aviator-sunglasses', 'Classic aviator style with UV protection. Metal frame.', 'Frame: Metal\nLenses: Polycarbonate\nUV Protection: 100%', 89.99, NULL, 0.05, 75, 13, 'unisex', 1, 0, 0, 'visible', NOW() - INTERVAL 28 DAY),
('RST-ACC-004', 'Rock Backpack', 'rock-backpack', 'Durable backpack with padded laptop sleeve. Multiple compartments.', 'Material: Polyester\nCapacity: 25L\nDimensions: 18x12x6 inches', 79.99, NULL, 0.7, 60, 14, 'unisex', 0, 0, 0, 'visible', NOW() - INTERVAL 42 DAY),

-- Limited Edition
('RST-LTD-001', 'Anniversary Limited Tee', 'anniversary-limited-tee', 'Special 10th anniversary limited edition tee. Numbered and authenticated.', 'Material: 100% Cotton\nEdition: 1/500\nIncludes Certificate of Authenticity', 129.99, NULL, 0.25, 50, 16, 'unisex', 1, 1, 1, 'visible', NOW() - INTERVAL 2 DAY),
('RST-LTD-002', 'Artist Collaboration Hoodie', 'artist-collab-hoodie', 'Exclusive collaboration with renowned artist. Hand-numbered edition.', 'Material: Premium Cotton Blend\nEdition: 1/250\nIncludes Art Print', 199.99, NULL, 0.55, 25, 16, 'unisex', 1, 1, 1, 'visible', NOW() - INTERVAL 1 DAY);

-- Insert product images (sample Cloudinary URLs - replace with actual images)
INSERT INTO `product_images` (`product_id`, `cloudinary_id`, `url`, `alt_text`, `sort_order`, `is_primary`) VALUES
-- Classic Rock Tee images
(1, NULL, 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800', 'Classic Rock Tee Front', 1, 1),
(1, NULL, 'https://images.unsplash.com/photo-1622445275576-721325763afe?w=800', 'Classic Rock Tee Back', 2, 0),

-- Vintage Band Tee images
(2, NULL, 'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=800', 'Vintage Band Tee Front', 1, 1),
(2, NULL, 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=800', 'Vintage Band Tee Detail', 2, 0),

-- Biker Leather Jacket images
(7, NULL, 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800', 'Biker Leather Jacket Front', 1, 1),
(7, NULL, 'https://images.unsplash.com/photo-1521223890158-f9f7c3d5d504?w=800', 'Biker Leather Jacket Side', 2, 0),

-- Essential Pullover Hoodie images
(9, NULL, 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=800', 'Essential Hoodie Front', 1, 1),
(9, NULL, 'https://images.unsplash.com/photo-1578681994506-b8f463449011?w=800', 'Essential Hoodie Back', 2, 0),

-- Anniversary Limited Tee images
(29, NULL, 'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=800', 'Limited Anniversary Tee', 1, 1),

-- Artist Collaboration Hoodie images
(30, NULL, 'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?w=800', 'Artist Collaboration Hoodie', 1, 1);

-- Insert product variants
INSERT INTO `product_variants` (`product_id`, `sku_variant`, `size`, `color`, `color_code`, `price_modifier`, `stock`) VALUES
-- Classic Rock Tee variants
(1, 'RST-SHT-001-S-BLK', 'S', 'Black', '#000000', 0, 50),
(1, 'RST-SHT-001-M-BLK', 'M', 'Black', '#000000', 0, 40),
(1, 'RST-SHT-001-L-BLK', 'L', 'Black', '#000000', 0, 35),
(1, 'RST-SHT-001-XL-BLK', 'XL', 'Black', '#000000', 0, 25),
(1, 'RST-SHT-001-S-WHT', 'S', 'White', '#FFFFFF', 0, 30),
(1, 'RST-SHT-001-M-WHT', 'M', 'White', '#FFFFFF', 0, 25),
(1, 'RST-SHT-001-L-WHT', 'L', 'White', '#FFFFFF', 0, 20),

-- Vintage Band Tee variants
(2, 'RST-SHT-002-S-BLK', 'S', 'Black', '#000000', 0, 25),
(2, 'RST-SHT-002-M-BLK', 'M', 'Black', '#000000', 0, 20),
(2, 'RST-SHT-002-L-BLK', 'L', 'Black', '#000000', 0, 20),
(2, 'RST-SHT-002-XL-BLK', 'XL', 'Black', '#000000', 0, 10),

-- Heavy Metal Oversized variants
(4, 'RST-OVS-001-S-BLK', 'S', 'Black', '#000000', 0, 30),
(4, 'RST-OVS-001-M-BLK', 'M', 'Black', '#000000', 0, 35),
(4, 'RST-OVS-001-L-BLK', 'L', 'Black', '#000000', 0, 25),
(4, 'RST-OVS-001-XL-BLK', 'XL', 'Black', '#000000', 0, 10),

-- Essential Pullover Hoodie variants
(9, 'RST-HOD-001-S-BLK', 'S', 'Black', '#000000', 0, 30),
(9, 'RST-HOD-001-M-BLK', 'M', 'Black', '#000000', 0, 35),
(9, 'RST-HOD-001-L-BLK', 'L', 'Black', '#000000', 0, 30),
(9, 'RST-HOD-001-XL-BLK', 'XL', 'Black', '#000000', 0, 25),
(9, 'RST-HOD-001-S-GRY', 'S', 'Gray', '#808080', 0, 20),
(9, 'RST-HOD-001-M-GRY', 'M', 'Gray', '#808080', 0, 25),
(9, 'RST-HOD-001-L-GRY', 'L', 'Gray', '#808080', 0, 20),

-- Classic Snapback Cap variants
(21, 'RST-ACC-001-OS-BLK', 'One Size', 'Black', '#000000', 0, 100),
(21, 'RST-ACC-001-OS-WHT', 'One Size', 'White', '#FFFFFF', 0, 50),
(21, 'RST-ACC-001-OS-RED', 'One Size', 'Red', '#FF0000', 0, 50),

-- Platform Sneakers variants
(28, 'RST-SHO-002-7-BLK', '7', 'Black', '#000000', 0, 15),
(28, 'RST-SHO-002-8-BLK', '8', 'Black', '#000000', 0, 20),
(28, 'RST-SHO-002-9-BLK', '9', 'Black', '#000000', 0, 15),
(28, 'RST-SHO-002-10-BLK', '10', 'Black', '#000000', 0, 10),
(28, 'RST-SHO-002-11-BLK', '11', 'Black', '#000000', 0, 5),

-- Limited Edition variants (only one of each)
(29, 'RST-LTD-001-M-BLK', 'M', 'Black', '#000000', 0, 15),
(29, 'RST-LTD-001-L-BLK', 'L', 'Black', '#000000', 0, 15),
(29, 'RST-LTD-001-XL-BLK', 'XL', 'Black', '#000000', 0, 10),
(30, 'RST-LTD-002-S-BLK', 'S', 'Black', '#000000', 0, 8),
(30, 'RST-LTD-002-M-BLK', 'M', 'Black', '#000000', 0, 10),
(30, 'RST-LTD-002-L-BLK', 'L', 'Black', '#000000', 0, 7);

-- Insert sample partners
INSERT INTO `partners` (`company_name`, `brand_name`, `website`, `industry`, `country`, `email`, `phone`, `status`, `featured`, `created_at`) VALUES
('Global Music Festival', 'GMF', 'https://globalmusicfestival.com', 'Entertainment', 'United States', 'partnerships@gmf.com', '+1 555-0100', 'approved', 1, NOW() - INTERVAL 30 DAY),
('Rock & Roll Gym', 'RRGym', 'https://rockandrollgym.com', 'Fitness', 'United Kingdom', 'hello@rrgym.co.uk', '+44 20 1234 5678', 'approved', 1, NOW() - INTERVAL 25 DAY),
('Street Culture Magazine', 'SCMag', 'https://streetculturemag.com', 'Media', 'Germany', 'editorial@streetculturemag.de', '+49 30 1234567', 'approved', 1, NOW() - INTERVAL 20 DAY),
('Underground Records', 'URecords', 'https://undergroundrecords.com', 'Music', 'Australia', 'info@undergroundrecords.au', '+61 2 9876 5432', 'approved', 0, NOW() - INTERVAL 15 DAY),
('Urban Skate Co', 'USkate', 'https://urbanskateco.com', 'Sports', 'Canada', 'hello@urbanskateco.ca', '+1 555-0200', 'approved', 0, NOW() - INTERVAL 10 DAY);

-- Insert sample coupons
INSERT INTO `coupons` (`code`, `type`, `value`, `min_order_amount`, `max_discount`, `usage_limit`, `status`, `starts_at`, `expires_at`) VALUES
('WELCOME20', 'percentage', 20.00, 50.00, 50.00, 1000, 'active', NULL, NOW() + INTERVAL 90 DAY),
('ROCK10', 'percentage', 10.00, 0.00, NULL, NULL, 'active', NULL, NOW() + INTERVAL 180 DAY),
('FREESHIP', 'fixed', 9.99, 100.00, NULL, 500, 'active', NULL, NOW() + INTERVAL 60 DAY),
('SUMMER25', 'percentage', 25.00, 75.00, 75.00, 200, 'active', NOW(), NOW() + INTERVAL 30 DAY);

-- Add products to collections
INSERT INTO `collection_products` (`collection_id`, `product_id`, `sort_order`) VALUES
-- Summer Collection
(1, 1, 1), (1, 4, 2), (1, 11, 3), (1, 28, 4),
-- Street Collection
(2, 1, 1), (2, 2, 2), (2, 4, 3), (2, 7, 4), (2, 9, 5), (2, 17, 6), (2, 18, 7), (2, 28, 8),
-- Performance Collection
(3, 9, 1), (3, 15, 2), (3, 16, 3), (3, 17, 4),
-- Mountain Collection
(4, 7, 1), (4, 8, 2), (4, 15, 3), (4, 16, 4), (4, 19, 5),
-- Limited Drop
(5, 29, 1), (5, 30, 2),
-- Winter Drop
(6, 7, 1), (6, 9, 2), (6, 10, 3), (6, 12, 4), (6, 13, 5), (6, 22, 6);
