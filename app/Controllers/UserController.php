<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;

class UserController extends Controller
{
    public function dashboard(): \App\Views\Response
    {
        if (!auth_check()) {
            return $this->redirect('/login');
        }
        
        $user = auth();
        $orders = Order::getByUser($user['id'], 1, 5);
        $wishlist = User::getWishlist($user['id']);
        $recentlyViewed = User::getRecentlyViewed($user['id'], 5);
        
        $data = [
            'title' => 'My Account | ' . APP_NAME,
            'user' => $user,
            'orders' => $orders,
            'wishlist' => $wishlist,
            'recentlyViewed' => $recentlyViewed,
        ];
        
        return $this->view('frontend/user/dashboard', $data);
    }

    public function orders(): \App\Views\Response
    {
        if (!auth_check()) {
            return $this->redirect('/login');
        }
        
        $page = (int)($_GET['page'] ?? 1);
        $orders = Order::getByUser(auth()['id'], $page, 10);
        
        $data = [
            'title' => 'My Orders | ' . APP_NAME,
            'orders' => $orders,
        ];
        
        return $this->view('frontend/user/orders', $data);
    }

    public function order(string $orderNumber): \App\Views\Response
    {
        if (!auth_check()) {
            return $this->redirect('/login');
        }
        
        $order = Order::getByOrderNumber($orderNumber);
        
        if (!$order || $order['user_id'] != auth()['id']) {
            return $this->notFound();
        }
        
        $items = Order::getItems($order['id']);
        
        $data = [
            'title' => 'Order ' . $orderNumber . ' | ' . APP_NAME,
            'order' => $order,
            'items' => $items,
        ];
        
        return $this->view('frontend/user/order', $data);
    }

    public function wishlist(): \App\Views\Response
    {
        if (!auth_check()) {
            return $this->redirect('/login');
        }
        
        $wishlist = User::getWishlist(auth()['id']);
        
        $data = [
            'title' => 'My Wishlist | ' . APP_NAME,
            'wishlist' => $wishlist,
        ];
        
        return $this->view('frontend/user/wishlist', $data);
    }

    public function addresses(): \App\Views\Response
    {
        if (!auth_check()) {
            return $this->redirect('/login');
        }
        
        $addresses = User::getAddresses(auth()['id']);
        
        $data = [
            'title' => 'My Addresses | ' . APP_NAME,
            'addresses' => $addresses,
        ];
        
        return $this->view('frontend/user/addresses', $data);
    }

    public function profile(): \App\Views\Response
    {
        if (!auth_check()) {
            return $this->redirect('/login');
        }
        
        $data = [
            'title' => 'My Profile | ' . APP_NAME,
            'user' => auth(),
        ];
        
        return $this->view('frontend/user/profile', $data);
    }

    public function updateProfile(): \App\Views\Response
    {
        if (!auth_check()) {
            return $this->redirect('/login');
        }
        
        $data = $this->sanitizeInput($_POST);
        $errors = $this->validateRequired($data, ['first_name', 'last_name', 'email']);
        
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
        
        $existingEmail = User::getByEmail($data['email']);
        if ($existingEmail && $existingEmail['id'] != auth()['id']) {
            $this->withInput($_POST);
            $this->withError('This email is already in use');
            return $this->back();
        }
        
        User::update(auth()['id'], [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);
        
        $_SESSION['user'] = User::getById(auth()['id']);
        
        $this->withSuccess('Profile updated successfully');
        
        return $this->redirect('/user/profile');
    }

    public function updatePassword(): \App\Views\Response
    {
        if (!auth_check()) {
            return $this->redirect('/login');
        }
        
        $data = $_POST;
        
        if (empty($data['current_password']) || empty($data['password']) || empty($data['password_confirmation'])) {
            $this->withError('Please fill in all password fields');
            return $this->back();
        }
        
        if ($data['password'] !== $data['password_confirmation']) {
            $this->withError('Passwords do not match');
            return $this->back();
        }
        
        if (strlen($data['password']) < 8) {
            $this->withError('Password must be at least 8 characters');
            return $this->back();
        }
        
        $user = User::verifyPassword(auth()['email'], $data['current_password']);
        if (!$user) {
            $this->withError('Current password is incorrect');
            return $this->back();
        }
        
        User::updatePassword(auth()['id'], $data['password']);
        
        $this->withSuccess('Password updated successfully');
        
        return $this->redirect('/user/profile');
    }

    public function trackOrder(string $orderNumber): \App\Views\Response
    {
        $order = Order::getByOrderNumber($orderNumber);
        
        if (!$order) {
            return $this->notFound();
        }
        
        $data = [
            'title' => 'Track Order ' . $orderNumber . ' | ' . APP_NAME,
            'order' => $order,
            'items' => Order::getItems($order['id']),
        ];
        
        return $this->view('frontend/user/track-order', $data);
    }
}
