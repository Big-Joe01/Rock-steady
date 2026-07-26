<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use App\Models\Collection;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Settings;
use App\Models\User;
use App\Models\Contact;
use App\Models\Newsletter;
use App\Models\Partner;
use App\Models\Sponsorship;
use App\Services\MailService;
use App\Services\Logger;

class FrontendController extends Controller
{
    public function home(): \App\Views\Response
    {
        $featuredProducts = Product::getFeatured(8);
        $newArrivals = Product::getNewArrivals(8);
        $trendingProducts = Product::getTrending(8);
        $collections = Collection::getActive();
        $partners = Partner::getFeatured();
        
        $data = [
            'title' => SITE_TAGLINE . ' | ' . APP_NAME,
            'featuredProducts' => $featuredProducts,
            'newArrivals' => $newArrivals,
            'trendingProducts' => $trendingProducts,
            'collections' => $collections,
            'partners' => $partners,
            'heroVideo' => Settings::get('hero_video'),
        ];
        
        return $this->view('frontend/home', $data);
    }

    public function shop(): \App\Views\Response
    {
        $filters = [
            'category' => $_GET['category'] ?? null,
            'collection' => $_GET['collection'] ?? null,
            'gender' => $_GET['gender'] ?? null,
            'min_price' => $_GET['min_price'] ?? null,
            'max_price' => $_GET['max_price'] ?? null,
            'sort' => $_GET['sort'] ?? null,
            'search' => $_GET['search'] ?? null,
            'featured' => $_GET['featured'] ?? null,
            'is_new' => $_GET['is_new'] ?? null,
        ];
        
        $page = (int)($_GET['page'] ?? 1);
        $products = Product::getAll($filters, $page, 12);
        $categories = Category::getWithProducts();
        $collections = Collection::getActive();
        
        $data = [
            'title' => 'Shop | ' . APP_NAME,
            'products' => $products,
            'categories' => $categories,
            'collections' => $collections,
            'filters' => $filters,
        ];
        
        return $this->view('frontend/shop', $data);
    }

    public function product(string $slug): \App\Views\Response
    {
        $product = Product::getBySlug($slug);
        
        if (!$product) {
            return $this->notFound();
        }
        
        $images = Product::getImages($product['id']);
        $variants = Product::getVariants($product['id']);
        $related = Product::getRelated($product['id'], 4);
        $reviews = Product::getReviews($product['id']);
        $reviewStats = Product::getReviewStats($product['id']);
        
        if (auth_check()) {
            User::addRecentlyViewed($_SESSION['user']['id'], $product['id']);
            $product['is_wishlisted'] = User::isInWishlist($_SESSION['user']['id'], $product['id']);
        }
        
        $groupedVariants = [];
        foreach ($variants as $variant) {
            if ($variant['size']) {
                $groupedVariants['sizes'][$variant['size']] = $variant;
            }
            if ($variant['color']) {
                $groupedVariants['colors'][$variant['color']] = $variant;
            }
        }
        
        $data = [
            'title' => $product['name'] . ' | ' . APP_NAME,
            'product' => $product,
            'images' => $images,
            'variants' => $variants,
            'groupedVariants' => $groupedVariants,
            'related' => $related,
            'reviews' => $reviews,
            'reviewStats' => $reviewStats,
        ];
        
        return $this->view('frontend/product', $data);
    }

    public function collections(): \App\Views\Response
    {
        $collections = Collection::getWithProducts();
        
        $data = [
            'title' => 'Collections | ' . APP_NAME,
            'collections' => $collections,
        ];
        
        return $this->view('frontend/collections', $data);
    }

    public function collection(string $slug): \App\Views\Response
    {
        $collection = Collection::getBySlug($slug);
        
        if (!$collection) {
            return $this->notFound();
        }
        
        $products = Product::getByCollection($collection['id']);
        
        $data = [
            'title' => $collection['name'] . ' | ' . APP_NAME,
            'collection' => $collection,
            'products' => $products,
        ];
        
        return $this->view('frontend/collection', $data);
    }

    public function cart(): \App\Views\Response
    {
        $cart = Cart::get();
        $coupon = Cart::getCoupon();
        
        $data = [
            'title' => 'Cart | ' . APP_NAME,
            'cart' => $cart,
            'coupon' => $coupon,
            'subtotal' => Cart::getSubtotal(),
            'discount' => Cart::getDiscount(),
            'shipping' => Cart::getShippingCost(),
            'tax' => Cart::getTax(),
            'total' => Cart::getTotal(),
        ];
        
        return $this->view('frontend/cart', $data);
    }

    public function checkout(): \App\Views\Response
    {
        if (Cart::isEmpty()) {
            return $this->redirect('/cart');
        }
        
        $cart = Cart::get();
        $coupon = Cart::getCoupon();
        
        $data = [
            'title' => 'Checkout | ' . APP_NAME,
            'cart' => $cart,
            'coupon' => $coupon,
            'subtotal' => Cart::getSubtotal(),
            'discount' => Cart::getDiscount(),
            'shipping' => Cart::getShippingCost(),
            'tax' => Cart::getTax(),
            'total' => Cart::getTotal(),
            'stripeKey' => \App\Services\StripeService::getPublishableKey(),
        ];
        
        return $this->view('frontend/checkout', $data);
    }

    public function partners(): \App\Views\Response
    {
        $partners = Partner::getApproved();
        
        $data = [
            'title' => 'Partners | ' . APP_NAME,
            'partners' => $partners,
        ];
        
        return $this->view('frontend/partners', $data);
    }

    public function sponsorship(): \App\Views\Response
    {
        $data = [
            'title' => 'Sponsorship | ' . APP_NAME,
        ];
        
        return $this->view('frontend/sponsorship', $data);
    }

    public function about(): \App\Views\Response
    {
        $data = [
            'title' => 'About Us | ' . APP_NAME,
        ];
        
        return $this->view('frontend/about', $data);
    }

    public function contact(): \App\Views\Response
    {
        $data = [
            'title' => 'Contact | ' . APP_NAME,
            'contactEmail' => Settings::get('contact_email'),
            'contactPhone' => Settings::get('contact_phone'),
            'address' => Settings::get('address'),
            'social' => [
                'facebook' => Settings::get('social_facebook'),
                'instagram' => Settings::get('social_instagram'),
                'twitter' => Settings::get('social_twitter'),
                'tiktok' => Settings::get('social_tiktok'),
            ],
        ];
        
        return $this->view('frontend/contact', $data);
    }

    public function search(): \App\Views\Response
    {
        $query = $_GET['q'] ?? '';
        
        if (strlen($query) < 2) {
            return $this->json(['results' => []]);
        }
        
        $results = Product::search($query, 10);
        
        return $this->json(['results' => $results]);
    }

    public function subscribe(): \App\Views\Response
    {
        if ($this->isAjax()) {
            $email = $_POST['email'] ?? '';
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->json(['success' => false, 'message' => 'Invalid email address']);
            }
            
            $result = Newsletter::subscribe($email);
            return $this->json($result);
        }
        
        $email = $_POST['email'] ?? '';
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->withError('Please enter a valid email address');
            return $this->back();
        }
        
        Newsletter::subscribe($email);
        $this->withSuccess('Thank you for subscribing!');
        
        return $this->back();
    }

    public function contactSubmit(): \App\Views\Response
    {
        $data = $this->sanitizeInput($_POST);
        $errors = $this->validateRequired($data, ['name', 'email', 'message']);
        
        if (!empty($errors)) {
            $this->withInput($_POST);
            $this->withError(implode(', ', $errors));
            return $this->back();
        }
        
        if (!$this->validateEmail($data['email'])) {
            $this->withInput($_POST);
            $this->withError('Please enter a valid email address');
            return $this->back();
        }
        
        Contact::create($data);
        
        MailService::sendTemplate(
            SMTP_REPLY_TO ?: SMTP_FROM_EMAIL,
            'New Contact Message: ' . ($data['subject'] ?? 'No Subject'),
            'contact_admin',
            ['contact' => $data]
        );
        
        $this->withSuccess('Thank you for your message! We will get back to you soon.');
        
        return $this->redirect('/contact');
    }

    public function partnerSubmit(): \App\Views\Response
    {
        $data = $this->sanitizeInput($_POST);
        $errors = $this->validateRequired($data, ['company_name', 'email']);
        
        if (!empty($errors)) {
            $this->withInput($_POST);
            $this->withError(implode(', ', $errors));
            return $this->back();
        }
        
        Partner::create($data);
        
        MailService::sendTemplate(
            SMTP_REPLY_TO ?: SMTP_FROM_EMAIL,
            'New Partner Application: ' . $data['company_name'],
            'partner_admin',
            ['partner' => $data]
        );
        
        $this->withSuccess('Thank you for your partnership application! We will review and get back to you.');
        
        return $this->redirect('/partners');
    }

    public function sponsorshipSubmit(): \App\Views\Response
    {
        $data = $this->sanitizeInput($_POST);
        $errors = $this->validateRequired($data, ['name', 'email']);
        
        if (!empty($errors)) {
            $this->withInput($_POST);
            $this->withError(implode(', ', $errors));
            return $this->back();
        }
        
        Sponsorship::create($data);
        
        MailService::sendTemplate(
            SMTP_REPLY_TO ?: SMTP_FROM_EMAIL,
            'New Sponsorship Application: ' . $data['name'],
            'sponsorship_admin',
            ['sponsorship' => $data]
        );
        
        $this->withSuccess('Thank you for your sponsorship application! We will review and get back to you.');
        
        return $this->redirect('/sponsorship');
    }

    public function notFound(): \App\Views\Response
    {
        http_response_code(404);
        
        $data = [
            'title' => 'Page Not Found | ' . APP_NAME,
        ];
        
        return $this->view('frontend/404', $data, 404);
    }
}
