# ROCK STEADY - Official Website

**JUST KEEP ROCKING**

A premium e-commerce website for the ROCK STEADY clothing brand. Built with modern web technologies featuring a sleek, dark aesthetic that conveys luxury and rock-inspired streetwear.

## Features

### Core Features
- **Full E-Commerce Platform**: Complete shopping experience with product catalog, cart, checkout, and order management
- **User Authentication**: Registration, login, password reset, wishlists, order history
- **Product Management**: Multiple images, variants (size/color), stock tracking, categories, collections
- **Admin Dashboard**: Comprehensive admin panel for managing products, orders, customers, partners, and more
- **Stripe Payments**: Secure checkout with Stripe Checkout, supporting Apple Pay and Google Pay
- **Cloudinary Integration**: All images uploaded directly to Cloudinary for optimized delivery
- **Email Notifications**: PHPMailer integration for order confirmations, contact form, etc.

### Design
- **Premium Dark Theme**: Minimal, luxury aesthetic with gold accents (#C9A227)
- **Responsive Design**: Perfect on desktop, tablet, and mobile
- **Smooth Animations**: GSAP animations, AOS scroll effects
- **Modern Typography**: Bebas Neue for headings, Montserrat/Inter for body

## Tech Stack

### Backend
- PHP 8.3+
- MySQL with PDO
- MVC Architecture
- REST-style routing
- Composer dependencies
- Dotenv configuration

### Frontend
- HTML5 / CSS3
- Bootstrap 5 (grid only)
- Vanilla JavaScript
- GSAP Animations
- Swiper.js (sliders)
- AOS (scroll animations)
- Three.js (3D effects where needed)

### Services
- **Stripe**: Payments (Checkout, Webhooks)
- **Cloudinary**: Image storage and optimization
- **PHPMailer**: Email delivery via SMTP

## Installation

### Prerequisites
- PHP 8.3 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Apache/Nginx web server
- SSL certificate (for Stripe webhooks)

### Steps

1. **Clone the repository**
```bash
git clone https://github.com/your-repo/rocksteady.git
cd rocksteady
```

2. **Install dependencies**
```bash
composer install
```

3. **Create environment file**
```bash
cp .env.example .env
```

4. **Configure your .env file**
```env
APP_NAME="ROCK STEADY"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=rocksteady
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Cloudinary
CLOUDINARY_CLOUD_NAME=your_cloud_name
CLOUDINARY_API_KEY=your_api_key
CLOUDINARY_API_SECRET=your_api_secret
CLOUDINARY_FOLDER=rocksteady

# Stripe
STRIPE_PUBLISHABLE_KEY=pk_live_xxx
STRIPE_SECRET_KEY=sk_live_xxx
STRIPE_WEBHOOK_SECRET=whsec_xxx

# SMTP
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your@email.com
SMTP_PASSWORD=your_app_password
SMTP_FROM_EMAIL=rocksteady@gmail.com
SMTP_FROM_NAME="ROCK STEADY"
SMTP_REPLY_TO=rocksteady@gmail.com

# Admin (change this!)
ADMIN_PASSWORD=YourSecurePassword123!
```

5. **Create the database**
```bash
mysql -u root -p
CREATE DATABASE rocksteady CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

6. **Run migrations**
```bash
mysql -u root -p rocksteady < database/migrations/001_initial_schema.sql
mysql -u root -p rocksteady < database/seeders/seed_data.sql
```

7. **Set permissions**
```bash
chmod -R 755 storage
chmod -R 755 public/uploads
```

8. **Configure web server**

For Apache (`.htaccess`):
```apache
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ public/index.php [L]
```

For Nginx:
```nginx
location / {
    try_files $uri $uri/ /public/index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

## Deployment

### cPanel / Shared Hosting
1. Upload all files via FTP/SFTP
2. Set PHP version to 8.3 in cPanel
3. Create MySQL database and import SQL files
4. Update `.env` with database credentials
5. Point domain to `/public` directory

### VPS / Dedicated Server
1. Clone repository
2. Follow installation steps above
3. Setup Nginx/Apache virtual host
4. Enable SSL with Let's Encrypt:
```bash
certbot --nginx -d yourdomain.com
```

### Railway / Platform-as-a-Service
1. Connect GitHub repository
2. Add environment variables in dashboard
3. Deploy - Railway auto-detects PHP

## Admin Access

The admin panel is hidden from the main navigation. To access:

1. Look for the small **settings gear icon** in the bottom-left corner of the site
2. Click it and enter the admin password (set in `.env`)
3. Default password: `Cybertr0n#$` (change this!)

**Important**: For production, replace the fixed password check with a proper admin user authentication system.

## Stripe Webhook Setup

1. Install Stripe CLI
```bash
stripe listen --forward-to localhost:8000/api/webhook/stripe
```

2. Or configure in Stripe Dashboard:
- Webhooks → Add endpoint
- URL: `https://yourdomain.com/api/webhook/stripe`
- Events: `checkout.session.completed`, `payment_intent.succeeded`, `payment_intent.payment_failed`

## API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | / | Homepage |
| GET | /shop | Product listing |
| GET | /product/{slug} | Product detail |
| GET | /collections | All collections |
| GET | /collection/{slug} | Collection products |
| GET | /cart | Shopping cart |
| POST | /cart/add | Add to cart |
| POST | /checkout/initiate | Start Stripe checkout |
| GET | /checkout/success | Order success |
| POST | /api/webhook/stripe | Stripe webhook |
| POST | /admin/authenticate | Admin login |
| GET | /admin | Admin dashboard |
| GET | /admin/products | Manage products |
| GET | /admin/orders | Manage orders |

## Database Schema

### Main Tables
- `users` - Customer and admin accounts
- `products` - Product catalog
- `product_images` - Multiple product images
- `product_variants` - Size/color variants
- `categories` - Product categories
- `collections` - Curated collections
- `orders` - Customer orders
- `order_items` - Order line items
- `partners` - Partner applications
- `sponsorships` - Sponsorship applications
- `contacts` - Contact form messages
- `newsletter` - Email subscribers
- `coupons` - Discount codes

## Security Features

- Prepared statements (SQL injection prevention)
- CSRF token validation
- XSS protection (output escaping)
- Password hashing (bcrypt)
- Rate limiting on forms
- File upload validation
- Secure session handling

## Performance Optimizations

- Lazy loading images
- Cloudinary image optimization
- CSS/JS minification ready
- GZIP compression
- Database query optimization with indexes
- Caching support for settings

## Customization

### Colors
Edit CSS variables in `public/assets/css/main.css`:
```css
:root {
    --color-primary-black: #111111;
    --color-gold: #C9A227;
    /* ... */
}
```

### Fonts
Replace Google Fonts links in `resources/views/layouts/main.php`

### Logo
Add your logo to `public/assets/images/` and update the navbar

## Support

For issues or questions:
- Email: rocksteady@gmail.com

## License

Proprietary - All rights reserved. ROCK STEADY brand.

---

**JUST KEEP ROCKING**
