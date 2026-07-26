<?php

declare(strict_types=1);

use App\Routes\Router;
use App\Controllers\FrontendController;
use App\Controllers\AuthController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\UserController;
use App\Controllers\WishlistController;
use App\Controllers\ReviewController;
use App\Controllers\AdminController;
use App\Middleware\CSRFMiddleware;
use App\Middleware\RateLimitMiddleware;

$router = new Router();

$router->group([CSRFMiddleware::class, RateLimitMiddleware::class], function($router) {
    // Frontend Routes
    $router->get('/', [FrontendController::class, 'home']);
    $router->get('/shop', [FrontendController::class, 'shop']);
    $router->get('/product/{slug}', [FrontendController::class, 'product']);
    $router->get('/collections', [FrontendController::class, 'collections']);
    $router->get('/collection/{slug}', [FrontendController::class, 'collection']);
    $router->get('/cart', [FrontendController::class, 'cart']);
    $router->get('/partners', [FrontendController::class, 'partners']);
    $router->get('/sponsorship', [FrontendController::class, 'sponsorship']);
    $router->get('/about', [FrontendController::class, 'about']);
    $router->get('/contact', [FrontendController::class, 'contact']);
    $router->get('/search', [FrontendController::class, 'search']);
    
    // Contact & Newsletter
    $router->post('/contact', [FrontendController::class, 'contactSubmit']);
    $router->post('/subscribe', [FrontendController::class, 'subscribe']);
    $router->post('/partner/submit', [FrontendController::class, 'partnerSubmit']);
    $router->post('/sponsorship/submit', [FrontendController::class, 'sponsorshipSubmit']);
    
    // Auth Routes
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register']);
    $router->post('/logout', [AuthController::class, 'logout']);
    $router->get('/forgot-password', [AuthController::class, 'showForgotPassword']);
    $router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
    
    // Cart Routes
    $router->post('/cart/add', [CartController::class, 'add']);
    $router->post('/cart/update', [CartController::class, 'update']);
    $router->post('/cart/remove', [CartController::class, 'remove']);
    $router->post('/cart/clear', [CartController::class, 'clear']);
    $router->post('/cart/apply-coupon', [CartController::class, 'applyCoupon']);
    $router->post('/cart/remove-coupon', [CartController::class, 'removeCoupon']);
    $router->get('/cart/data', [CartController::class, 'get']);
    
    // Checkout Routes
    $router->get('/checkout', [FrontendController::class, 'checkout']);
    $router->post('/checkout/initiate', [CheckoutController::class, 'initiate']);
    $router->get('/checkout/success', [CheckoutController::class, 'success']);
    $router->get('/checkout/cancel', [CheckoutController::class, 'cancel']);
    
    // User Routes
    $router->get('/user/dashboard', [UserController::class, 'dashboard']);
    $router->get('/user/orders', [UserController::class, 'orders']);
    $router->get('/user/order/{orderNumber}', [UserController::class, 'order']);
    $router->get('/user/wishlist', [UserController::class, 'wishlist']);
    $router->get('/user/addresses', [UserController::class, 'addresses']);
    $router->get('/user/profile', [UserController::class, 'profile']);
    $router->post('/user/profile/update', [UserController::class, 'updateProfile']);
    $router->post('/user/password/update', [UserController::class, 'updatePassword']);
    $router->get('/track-order/{orderNumber}', [UserController::class, 'trackOrder']);
    
    // Wishlist Routes
    $router->post('/wishlist/add', [WishlistController::class, 'add']);
    $router->post('/wishlist/remove', [WishlistController::class, 'remove']);
    $router->post('/wishlist/toggle', [WishlistController::class, 'toggle']);
    
    // Review Routes
    $router->post('/review/store', [ReviewController::class, 'store']);
    $router->post('/review/helpful', [ReviewController::class, 'helpful']);
    
    // Admin Routes
    $router->get('/admin/login', [AdminController::class, 'login']);
    $router->post('/admin/authenticate', [AdminController::class, 'authenticate']);
    $router->post('/admin/logout', [AdminController::class, 'logout']);
    
    // Admin Protected Routes
    $router->get('/admin', [AdminController::class, 'dashboard']);
    $router->get('/admin/products', [AdminController::class, 'products']);
    $router->get('/admin/products/create', [AdminController::class, 'productCreate']);
    $router->post('/admin/products/store', [AdminController::class, 'productStore']);
    $router->get('/admin/products/{id}/edit', [AdminController::class, 'productEdit']);
    $router->post('/admin/products/{id}/update', [AdminController::class, 'productUpdate']);
    $router->post('/admin/products/{id}/delete', [AdminController::class, 'productDelete']);
    
    $router->get('/admin/categories', [AdminController::class, 'categories']);
    $router->post('/admin/categories/store', [AdminController::class, 'categoryStore']);
    $router->post('/admin/categories/{id}/update', [AdminController::class, 'categoryUpdate']);
    $router->post('/admin/categories/{id}/delete', [AdminController::class, 'categoryDelete']);
    
    $router->get('/admin/collections', [AdminController::class, 'collections']);
    $router->post('/admin/collections/store', [AdminController::class, 'collectionStore']);
    $router->post('/admin/collections/{id}/update', [AdminController::class, 'collectionUpdate']);
    
    $router->get('/admin/orders', [AdminController::class, 'orders']);
    $router->get('/admin/orders/{id}', [AdminController::class, 'orderView']);
    $router->post('/admin/orders/{id}/status', [AdminController::class, 'orderUpdateStatus']);
    $router->post('/admin/orders/{id}/tracking', [AdminController::class, 'orderUpdateTracking']);
    
    $router->get('/admin/partners', [AdminController::class, 'partners']);
    $router->post('/admin/partners/{id}/status', [AdminController::class, 'partnerUpdateStatus']);
    
    $router->get('/admin/sponsorships', [AdminController::class, 'sponsorships']);
    $router->post('/admin/sponsorships/{id}/status', [AdminController::class, 'sponsorshipUpdateStatus']);
    
    $router->get('/admin/contacts', [AdminController::class, 'contacts']);
    $router->get('/admin/contacts/{id}', [AdminController::class, 'contactView']);
    
    $router->get('/admin/reviews', [AdminController::class, 'reviews']);
    $router->post('/admin/reviews/{id}/status', [AdminController::class, 'reviewUpdateStatus']);
    
    $router->get('/admin/customers', [AdminController::class, 'customers']);
    
    $router->get('/admin/settings', [AdminController::class, 'settings']);
    $router->post('/admin/settings/update', [AdminController::class, 'settingsUpdate']);
    
    $router->get('/admin/coupons', [AdminController::class, 'coupons']);
    $router->post('/admin/coupons/store', [AdminController::class, 'couponStore']);
    $router->post('/admin/coupons/{id}/delete', [AdminController::class, 'couponDelete']);
    
    $router->get('/admin/newsletter', [AdminController::class, 'newsletter']);
});

// Webhook routes (no CSRF)
$router->post('/api/webhook/stripe', [CheckoutController::class, 'webhook']);

return $router;
