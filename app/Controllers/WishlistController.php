<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\User;
use App\Models\Product;

class WishlistController extends Controller
{
    public function add(): \App\Views\Response
    {
        if (!auth_check()) {
            if ($this->isAjax()) {
                return $this->json([
                    'success' => false,
                    'message' => 'Please login to add items to wishlist',
                    'requires_login' => true,
                ]);
            }
            return $this->redirect('/login');
        }
        
        $productId = (int)($_POST['product_id'] ?? 0);
        
        if ($productId <= 0) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Invalid product']);
            }
            return $this->back();
        }
        
        $product = Product::getById($productId);
        if (!$product) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Product not found']);
            }
            return $this->back();
        }
        
        User::addToWishlist(auth()['id'], $productId);
        
        if ($this->isAjax()) {
            return $this->json([
                'success' => true,
                'message' => 'Added to wishlist',
            ]);
        }
        
        $this->withSuccess('Added to wishlist');
        
        return $this->back();
    }

    public function remove(): \App\Views\Response
    {
        if (!auth_check()) {
            return $this->redirect('/login');
        }
        
        $productId = (int)($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
        
        if ($productId <= 0) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Invalid product']);
            }
            return $this->back();
        }
        
        User::removeFromWishlist(auth()['id'], $productId);
        
        if ($this->isAjax()) {
            return $this->json([
                'success' => true,
                'message' => 'Removed from wishlist',
            ]);
        }
        
        $this->withSuccess('Removed from wishlist');
        
        return $this->redirect('/user/wishlist');
    }

    public function toggle(): \App\Views\Response
    {
        $productId = (int)($_POST['product_id'] ?? 0);
        
        if (!auth_check()) {
            if ($this->isAjax()) {
                return $this->json([
                    'success' => false,
                    'message' => 'Please login',
                    'requires_login' => true,
                ]);
            }
            return $this->redirect('/login');
        }
        
        if ($productId <= 0) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Invalid product']);
            }
            return $this->back();
        }
        
        $isInWishlist = User::isInWishlist(auth()['id'], $productId);
        
        if ($isInWishlist) {
            User::removeFromWishlist(auth()['id'], $productId);
            $message = 'Removed from wishlist';
        } else {
            User::addToWishlist(auth()['id'], $productId);
            $message = 'Added to wishlist';
        }
        
        if ($this->isAjax()) {
            return $this->json([
                'success' => true,
                'message' => $message,
                'is_in_wishlist' => !$isInWishlist,
            ]);
        }
        
        return $this->redirect('/user/wishlist');
    }
}
