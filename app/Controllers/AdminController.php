<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Order;
use App\Models\User;
use App\Models\Partner;
use App\Models\Sponsorship;
use App\Models\Contact;
use App\Models\Review;
use App\Models\Newsletter;
use App\Models\Settings;
use App\Models\Coupon;
use App\Services\CloudinaryService;
use App\Services\Logger;

class AdminController extends Controller
{
    private function checkAuth(): void
    {
        if (!admin_check()) {
            if ($this->isAjax()) {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }
            header('Location: ' . url('/'));
            exit;
        }
    }

    public function login(): \App\Views\Response
    {
        if (admin_check()) {
            return $this->redirect('/admin');
        }
        
        $data = [
            'title' => 'Admin Login | ' . APP_NAME,
        ];
        
        return $this->view('admin/login', $data);
    }

    public function authenticate(): \App\Views\Response
    {
        $password = $_POST['password'] ?? '';
        
        if (hash_equals(ADMIN_PASSWORD, $password)) {
            $_SESSION[ADMIN_SESSION_KEY] = true;
            $_SESSION['admin_login_time'] = time();
            
            if ($this->isAjax()) {
                return $this->json(['success' => true, 'redirect' => url('/admin')]);
            }
            
            return $this->redirect('/admin');
        }
        
        if ($this->isAjax()) {
            return $this->json(['success' => false, 'message' => 'Invalid password']);
        }
        
        $this->withError('Invalid password');
        return $this->redirect('/admin/login');
    }

    public function logout(): \App\Views\Response
    {
        unset($_SESSION[ADMIN_SESSION_KEY]);
        unset($_SESSION['admin_login_time']);
        
        return $this->redirect('/');
    }

    public function dashboard(): \App\Views\Response
    {
        $this->checkAuth();
        
        $orderStats = Order::getStats();
        $productCount = \App\Models\Database::count('products');
        $customerCount = \App\Models\Database::count('users', 'role = ?', ['customer']);
        $partnerStats = Partner::getStats();
        $sponsorshipStats = Sponsorship::getStats();
        $contactStats = Contact::getStats();
        $recentOrders = Order::getRecent(10);
        $lowStockProducts = Product::getLowStock();
        $pendingReviews = \App\Models\Database::count('reviews', 'status = ?', ['pending']);
        
        $data = [
            'title' => 'Dashboard | Admin',
            'orderStats' => $orderStats,
            'productCount' => $productCount,
            'customerCount' => $customerCount,
            'partnerStats' => $partnerStats,
            'sponsorshipStats' => $sponsorshipStats,
            'contactStats' => $contactStats,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => $lowStockProducts,
            'pendingReviews' => $pendingReviews,
        ];
        
        return $this->view('admin/dashboard', $data);
    }

    public function products(): \App\Views\Response
    {
        $this->checkAuth();
        
        $page = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        
        $filters = [];
        if ($search) {
            $filters['search'] = $search;
        }
        
        $products = Product::getAll($filters, $page, 20);
        
        $data = [
            'title' => 'Products | Admin',
            'products' => $products,
            'search' => $search,
        ];
        
        return $this->view('admin/products/index', $data);
    }

    public function productCreate(): \App\Views\Response
    {
        $this->checkAuth();
        
        $categories = Category::getAll();
        $collections = Collection::getAll();
        
        $data = [
            'title' => 'Create Product | Admin',
            'categories' => $categories,
            'collections' => $collections,
            'product' => null,
        ];
        
        return $this->view('admin/products/create', $data);
    }

    public function productStore(): \App\Views\Response
    {
        $this->checkAuth();
        
        $data = $this->sanitizeInput($_POST);
        
        $errors = $this->validateRequired($data, ['name', 'sku', 'price']);
        
        if (!empty($errors)) {
            $this->withError(implode(', ', $errors));
            return $this->back();
        }
        
        $data['slug'] = generate_slug($data['name']);
        
        if (isset($data['featured'])) {
            $data['featured'] = 1;
        } else {
            $data['featured'] = 0;
        }
        
        if (isset($data['is_new'])) {
            $data['is_new'] = 1;
        } else {
            $data['is_new'] = 0;
        }
        
        if (isset($data['trending'])) {
            $data['trending'] = 1;
        } else {
            $data['trending'] = 0;
        }
        
        $productId = Product::create($data);
        
        if (!empty($_FILES['images']['name'][0])) {
            $this->uploadProductImages($productId, $_FILES['images']);
        }
        
        $this->withSuccess('Product created successfully');
        
        return $this->redirect('/admin/products');
    }

    public function productEdit(int $id): \App\Views\Response
    {
        $this->checkAuth();
        
        $product = Product::getById($id);
        if (!$product) {
            return $this->notFound();
        }
        
        $images = Product::getImages($id);
        $variants = Product::getVariants($id);
        $categories = Category::getAll();
        $collections = Collection::getAll();
        
        $data = [
            'title' => 'Edit Product | Admin',
            'product' => $product,
            'images' => $images,
            'variants' => $variants,
            'categories' => $categories,
            'collections' => $collections,
        ];
        
        return $this->view('admin/products/edit', $data);
    }

    public function productUpdate(int $id): \App\Views\Response
    {
        $this->checkAuth();
        
        $data = $this->sanitizeInput($_POST);
        
        $errors = $this->validateRequired($data, ['name', 'sku', 'price']);
        
        if (!empty($errors)) {
            $this->withError(implode(', ', $errors));
            return $this->back();
        }
        
        $data['slug'] = generate_slug($data['name']);
        $data['featured'] = isset($data['featured']) ? 1 : 0;
        $data['is_new'] = isset($data['is_new']) ? 1 : 0;
        $data['trending'] = isset($data['trending']) ? 1 : 0;
        
        if (empty($data['sale_price'])) {
            $data['sale_price'] = null;
        }
        
        Product::update($id, $data);
        
        if (!empty($_FILES['images']['name'][0])) {
            $this->uploadProductImages($id, $_FILES['images']);
        }
        
        $this->withSuccess('Product updated successfully');
        
        return $this->redirect('/admin/products/' . $id . '/edit');
    }

    public function productDelete(int $id): \App\Views\Response
    {
        $this->checkAuth();
        
        Product::delete($id);
        
        $this->withSuccess('Product deleted successfully');
        
        return $this->redirect('/admin/products');
    }

    private function uploadProductImages(int $productId, array $files): void
    {
        $cloudinary = new CloudinaryService();
        
        $count = count($files['name']);
        $existingImages = Product::getImages($productId);
        $isPrimary = empty($existingImages);
        
        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }
            
            $tmpName = $files['tmp_name'][$i];
            $result = $cloudinary->upload($tmpName);
            
            if ($result['success']) {
                Product::addImage($productId, [
                    'cloudinary_id' => $result['public_id'],
                    'url' => $result['secure_url'],
                    'is_primary' => $isPrimary && $i === 0 ? 1 : 0,
                    'sort_order' => count($existingImages) + $i,
                ]);
            }
        }
    }

    public function categories(): \App\Views\Response
    {
        $this->checkAuth();
        
        $categories = Category::getAll('all');
        
        $data = [
            'title' => 'Categories | Admin',
            'categories' => $categories,
        ];
        
        return $this->view('admin/categories/index', $data);
    }

    public function categoryStore(): \App\Views\Response
    {
        $this->checkAuth();
        
        $data = $this->sanitizeInput($_POST);
        $data['slug'] = generate_slug($data['name']);
        
        Category::create($data);
        
        $this->withSuccess('Category created successfully');
        
        return $this->redirect('/admin/categories');
    }

    public function categoryUpdate(int $id): \App\Views\Response
    {
        $this->checkAuth();
        
        $data = $this->sanitizeInput($_POST);
        $data['slug'] = generate_slug($data['name']);
        
        Category::update($id, $data);
        
        $this->withSuccess('Category updated successfully');
        
        return $this->redirect('/admin/categories');
    }

    public function categoryDelete(int $id): \App\Views\Response
    {
        $this->checkAuth();
        
        Category::delete($id);
        
        $this->withSuccess('Category deleted successfully');
        
        return $this->redirect('/admin/categories');
    }

    public function collections(): \App\Views\Response
    {
        $this->checkAuth();
        
        $collections = Collection::getAll('all');
        
        $data = [
            'title' => 'Collections | Admin',
            'collections' => $collections,
        ];
        
        return $this->view('admin/collections/index', $data);
    }

    public function collectionStore(): \App\Views\Response
    {
        $this->checkAuth();
        
        $data = $this->sanitizeInput($_POST);
        $data['slug'] = generate_slug($data['name']);
        
        Collection::create($data);
        
        $this->withSuccess('Collection created successfully');
        
        return $this->redirect('/admin/collections');
    }

    public function collectionUpdate(int $id): \App\Views\Response
    {
        $this->checkAuth();
        
        $data = $this->sanitizeInput($_POST);
        $data['slug'] = generate_slug($data['name']);
        
        Collection::update($id, $data);
        
        $this->withSuccess('Collection updated successfully');
        
        return $this->redirect('/admin/collections');
    }

    public function orders(): \App\Views\Response
    {
        $this->checkAuth();
        
        $filters = [
            'status' => $_GET['status'] ?? null,
            'search' => $_GET['search'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
        ];
        
        $page = (int)($_GET['page'] ?? 1);
        $orders = Order::getAll($filters, $page, 20);
        
        $data = [
            'title' => 'Orders | Admin',
            'orders' => $orders,
            'filters' => $filters,
        ];
        
        return $this->view('admin/orders/index', $data);
    }

    public function orderView(int $id): \App\Views\Response
    {
        $this->checkAuth();
        
        $order = Order::getById($id);
        if (!$order) {
            return $this->notFound();
        }
        
        $items = Order::getItems($id);
        
        $data = [
            'title' => 'Order ' . $order['order_number'] . ' | Admin',
            'order' => $order,
            'items' => $items,
        ];
        
        return $this->view('admin/orders/view', $data);
    }

    public function orderUpdateStatus(int $id): \App\Views\Response
    {
        $this->checkAuth();
        
        $status = $_POST['status'] ?? '';
        
        Order::updateStatus($id, $status);
        
        $this->withSuccess('Order status updated');
        
        return $this->redirect('/admin/orders/' . $id);
    }

    public function orderUpdateTracking(int $id): \App\Views\Response
    {
        $this->checkAuth();
        
        $trackingNumber = $_POST['tracking_number'] ?? '';
        $trackingUrl = $_POST['tracking_url'] ?? '';
        
        Order::updateTracking($id, $trackingNumber, $trackingUrl);
        
        $this->withSuccess('Tracking information updated');
        
        return $this->redirect('/admin/orders/' . $id);
    }

    public function partners(): \App\Views\Response
    {
        $this->checkAuth();
        
        $filters = [
            'status' => $_GET['status'] ?? null,
            'search' => $_GET['search'] ?? null,
        ];
        
        $page = (int)($_GET['page'] ?? 1);
        $partners = Partner::getAll($filters, $page, 20);
        
        $data = [
            'title' => 'Partners | Admin',
            'partners' => $partners,
            'filters' => $filters,
        ];
        
        return $this->view('admin/partners/index', $data);
    }

    public function partnerUpdateStatus(int $id): \App\Views\Response
    {
        $this->checkAuth();
        
        $status = $_POST['status'] ?? '';
        
        Partner::updateStatus($id, $status);
        
        $this->withSuccess('Partner status updated');
        
        return $this->redirect('/admin/partners');
    }

    public function sponsorships(): \App\Views\Response
    {
        $this->checkAuth();
        
        $filters = [
            'status' => $_GET['status'] ?? null,
            'search' => $_GET['search'] ?? null,
        ];
        
        $page = (int)($_GET['page'] ?? 1);
        $sponsorships = Sponsorship::getAll($filters, $page, 20);
        
        $data = [
            'title' => 'Sponsorships | Admin',
            'sponsorships' => $sponsorships,
            'filters' => $filters,
        ];
        
        return $this->view('admin/sponsorships/index', $data);
    }

    public function sponsorshipUpdateStatus(int $id): \App\Views\Response
    {
        $this->checkAuth();
        
        $status = $_POST['status'] ?? '';
        $notes = $_POST['notes'] ?? '';
        
        Sponsorship::updateStatus($id, $status, $notes);
        
        $this->withSuccess('Sponsorship status updated');
        
        return $this->redirect('/admin/sponsorships');
    }

    public function contacts(): \App\Views\Response
    {
        $this->checkAuth();
        
        $filters = [
            'status' => $_GET['status'] ?? null,
            'search' => $_GET['search'] ?? null,
        ];
        
        $page = (int)($_GET['page'] ?? 1);
        $contacts = Contact::getAll($filters, $page, 20);
        
        $data = [
            'title' => 'Contacts | Admin',
            'contacts' => $contacts,
            'filters' => $filters,
        ];
        
        return $this->view('admin/contacts/index', $data);
    }

    public function contactView(int $id): \App\Views\Response
    {
        $this->checkAuth();
        
        $contact = Contact::getById($id);
        if (!$contact) {
            return $this->notFound();
        }
        
        Contact::markAsRead($id);
        
        $data = [
            'title' => 'Contact | Admin',
            'contact' => $contact,
        ];
        
        return $this->view('admin/contacts/view', $data);
    }

    public function reviews(): \App\Views\Response
    {
        $this->checkAuth();
        
        $status = $_GET['status'] ?? null;
        
        if ($status) {
            $reviews = \App\Models\Database::fetchAll(
                "SELECT r.*, p.name as product_name, u.first_name, u.last_name 
                 FROM reviews r 
                 LEFT JOIN products p ON r.product_id = p.id 
                 LEFT JOIN users u ON r.user_id = u.id 
                 WHERE r.status = ? 
                 ORDER BY r.created_at DESC",
                [$status]
            );
        } else {
            $reviews = \App\Models\Database::fetchAll(
                "SELECT r.*, p.name as product_name, u.first_name, u.last_name 
                 FROM reviews r 
                 LEFT JOIN products p ON r.product_id = p.id 
                 LEFT JOIN users u ON r.user_id = u.id 
                 ORDER BY r.created_at DESC 
                 LIMIT 50"
            );
        }
        
        $stats = Review::getStats();
        
        $data = [
            'title' => 'Reviews | Admin',
            'reviews' => $reviews,
            'stats' => $stats,
            'status' => $status,
        ];
        
        return $this->view('admin/reviews/index', $data);
    }

    public function reviewUpdateStatus(int $id): \App\Views\Response
    {
        $this->checkAuth();
        
        $status = $_POST['status'] ?? '';
        
        Review::updateStatus($id, $status);
        
        $this->withSuccess('Review status updated');
        
        return $this->redirect('/admin/reviews');
    }

    public function customers(): \App\Views\Response
    {
        $this->checkAuth();
        
        $page = (int)($_GET['page'] ?? 1);
        $search = $_GET['search'] ?? '';
        
        $offset = ($page - 1) * 20;
        
        if ($search) {
            $customers = \App\Models\Database::fetchAll(
                "SELECT * FROM users WHERE role = 'customer' AND (email LIKE ? OR first_name LIKE ? OR last_name LIKE ?) ORDER BY created_at DESC LIMIT 20 OFFSET ?",
                ['%' . $search . '%', '%' . $search . '%', '%' . $search . '%', $offset]
            );
            $total = \App\Models\Database::count('users', "role = 'customer' AND (email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)", ['%' . $search . '%', '%' . $search . '%', '%' . $search . '%']);
        } else {
            $customers = \App\Models\Database::fetchAll(
                "SELECT * FROM users WHERE role = 'customer' ORDER BY created_at DESC LIMIT 20 OFFSET ?",
                [$offset]
            );
            $total = \App\Models\Database::count('users', "role = 'customer'");
        }
        
        $data = [
            'title' => 'Customers | Admin',
            'customers' => $customers,
            'search' => $search,
            'total' => $total,
            'per_page' => 20,
            'current_page' => $page,
        ];
        
        return $this->view('admin/customers/index', $data);
    }

    public function settings(): \App\Views\Response
    {
        $this->checkAuth();
        
        $settings = Settings::getAll();
        
        $data = [
            'title' => 'Settings | Admin',
            'settings' => $settings,
        ];
        
        return $this->view('admin/settings/index', $data);
    }

    public function settingsUpdate(): \App\Views\Response
    {
        $this->checkAuth();
        
        foreach ($_POST as $key => $value) {
            if (str_starts_with($key, 'setting_')) {
                $settingKey = substr($key, 8);
                Settings::set($settingKey, $value);
            }
        }
        
        $this->withSuccess('Settings saved successfully');
        
        return $this->redirect('/admin/settings');
    }

    public function coupons(): \App\Views\Response
    {
        $this->checkAuth();
        
        $coupons = \App\Models\Database::fetchAll("SELECT * FROM coupons ORDER BY created_at DESC");
        
        $data = [
            'title' => 'Coupons | Admin',
            'coupons' => $coupons,
        ];
        
        return $this->view('admin/coupons/index', $data);
    }

    public function couponStore(): \App\Views\Response
    {
        $this->checkAuth();
        
        $data = $this->sanitizeInput($_POST);
        
        \App\Models\Database::insert('coupons', $data);
        
        $this->withSuccess('Coupon created successfully');
        
        return $this->redirect('/admin/coupons');
    }

    public function couponDelete(int $id): \App\Views\Response
    {
        $this->checkAuth();
        
        \App\Models\Database::delete('coupons', 'id = ?', [$id]);
        
        $this->withSuccess('Coupon deleted successfully');
        
        return $this->redirect('/admin/coupons');
    }

    public function newsletter(): \App\Views\Response
    {
        $this->checkAuth();
        
        $page = (int)($_GET['page'] ?? 1);
        $subscribers = Newsletter::getAll($page, 50);
        $stats = Newsletter::getStats();
        
        $data = [
            'title' => 'Newsletter | Admin',
            'subscribers' => $subscribers,
            'stats' => $stats,
        ];
        
        return $this->view('admin/newsletter/index', $data);
    }
}
