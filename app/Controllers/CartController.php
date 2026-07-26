<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function add(): \App\Views\Response
    {
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);
        $variant = [
            'id' => !empty($_POST['variant_id']) ? (int)$_POST['variant_id'] : null,
            'size' => $_POST['size'] ?? null,
            'color' => $_POST['color'] ?? null,
            'price_modifier' => $_POST['price_modifier'] ?? 0,
        ];
        
        if ($productId <= 0) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Invalid product']);
            }
            $this->withError('Invalid product');
            return $this->back();
        }
        
        $product = Product::getById($productId);
        if (!$product) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Product not found']);
            }
            $this->withError('Product not found');
            return $this->back();
        }
        
        if ($quantity <= 0) {
            $quantity = 1;
        }
        
        Cart::add($productId, $quantity, $variant);
        
        if ($this->isAjax()) {
            return $this->json([
                'success' => true,
                'message' => 'Product added to cart',
                'cart_count' => Cart::count(),
                'cart_total' => Cart::getSubtotal(),
            ]);
        }
        
        $this->withSuccess('Product added to cart');
        
        return $this->back();
    }

    public function update(): \App\Views\Response
    {
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);
        $variant = [
            'id' => !empty($_POST['variant_id']) ? (int)$_POST['variant_id'] : null,
            'size' => $_POST['size'] ?? null,
            'color' => $_POST['color'] ?? null,
        ];
        
        if ($productId <= 0) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Invalid product']);
            }
            return $this->back();
        }
        
        Cart::update($productId, $quantity, $variant);
        
        if ($this->isAjax()) {
            return $this->json([
                'success' => true,
                'cart_count' => Cart::count(),
                'cart_subtotal' => Cart::getSubtotal(),
                'cart_total' => Cart::getTotal(),
            ]);
        }
        
        return $this->redirect('/cart');
    }

    public function remove(): \App\Views\Response
    {
        $productId = (int)($_POST['product_id'] ?? 0);
        $variant = [
            'id' => !empty($_POST['variant_id']) ? (int)$_POST['variant_id'] : null,
            'size' => $_POST['size'] ?? null,
            'color' => $_POST['color'] ?? null,
        ];
        
        if ($productId <= 0) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Invalid product']);
            }
            return $this->back();
        }
        
        Cart::remove($productId, $variant);
        
        if ($this->isAjax()) {
            return $this->json([
                'success' => true,
                'message' => 'Product removed from cart',
                'cart_count' => Cart::count(),
                'cart_subtotal' => Cart::getSubtotal(),
                'cart_total' => Cart::getTotal(),
            ]);
        }
        
        return $this->redirect('/cart');
    }

    public function clear(): \App\Views\Response
    {
        Cart::clear();
        
        if ($this->isAjax()) {
            return $this->json(['success' => true]);
        }
        
        return $this->redirect('/cart');
    }

    public function applyCoupon(): \App\Views\Response
    {
        $code = $_POST['coupon_code'] ?? '';
        
        if (empty($code)) {
            if ($this->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Please enter a coupon code']);
            }
            $this->withError('Please enter a coupon code');
            return $this->back();
        }
        
        $result = Cart::applyCoupon($code);
        
        if ($this->isAjax()) {
            return $this->json($result);
        }
        
        if (!$result['success']) {
            $this->withError($result['message']);
        } else {
            $this->withSuccess($result['message']);
        }
        
        return $this->back();
    }

    public function removeCoupon(): \App\Views\Response
    {
        Cart::removeCoupon();
        
        if ($this->isAjax()) {
            return $this->json(['success' => true]);
        }
        
        return $this->redirect('/cart');
    }

    public function get(): \App\Views\Response
    {
        $cart = Cart::get();
        
        return $this->json([
            'items' => array_values($cart),
            'count' => Cart::count(),
            'subtotal' => Cart::getSubtotal(),
            'discount' => Cart::getDiscount(),
            'shipping' => Cart::getShippingCost(),
            'tax' => Cart::getTax(),
            'total' => Cart::getTotal(),
        ]);
    }
}
