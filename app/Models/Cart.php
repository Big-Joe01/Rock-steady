<?php

declare(strict_types=1);

namespace App\Models;

class Cart
{
    private const CART_KEY = 'cart';
    private const COUPON_KEY = 'coupon';

    public static function get(): array
    {
        return $_SESSION[self::CART_KEY] ?? [];
    }

    public static function add(int $productId, int $quantity = 1, array $variant = []): void
    {
        $cart = self::get();
        
        $key = self::generateKey($productId, $variant);
        
        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $quantity;
        } else {
            $product = Product::getById($productId);
            if (!$product) {
                return;
            }
            
            $price = $product['sale_price'] ?? $product['price'];
            if (!empty($variant) && isset($variant['price_modifier'])) {
                $price += (float)$variant['price_modifier'];
            }
            
            $cart[$key] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'variant_id' => $variant['id'] ?? null,
                'size' => $variant['size'] ?? null,
                'color' => $variant['color'] ?? null,
                'price' => $price,
                'name' => $product['name'],
                'sku' => $product['sku'],
                'image_url' => self::getProductImage($productId),
            ];
        }
        
        $_SESSION[self::CART_KEY] = $cart;
    }

    public static function update(int $productId, int $quantity, array $variant = []): void
    {
        $cart = self::get();
        $key = self::generateKey($productId, $variant);
        
        if (isset($cart[$key])) {
            if ($quantity <= 0) {
                unset($cart[$key]);
            } else {
                $cart[$key]['quantity'] = $quantity;
            }
        }
        
        $_SESSION[self::CART_KEY] = $cart;
    }

    public static function remove(int $productId, array $variant = []): void
    {
        $cart = self::get();
        $key = self::generateKey($productId, $variant);
        
        if (isset($cart[$key])) {
            unset($cart[$key]);
        }
        
        $_SESSION[self::CART_KEY] = $cart;
    }

    public static function clear(): void
    {
        $_SESSION[self::CART_KEY] = [];
        unset($_SESSION[self::COUPON_KEY]);
    }

    public static function count(): int
    {
        $cart = self::get();
        return array_sum(array_column($cart, 'quantity'));
    }

    public static function isEmpty(): bool
    {
        return empty(self::get());
    }

    public static function getSubtotal(): float
    {
        $cart = self::get();
        $subtotal = 0;
        
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }
        
        return (float)$subtotal;
    }

    public static function getTotal(): float
    {
        $subtotal = self::getSubtotal();
        $discount = self::getDiscount();
        $shipping = self::getShippingCost();
        $tax = self::getTax();
        
        return $subtotal - $discount + $shipping + $tax;
    }

    public static function getDiscount(): float
    {
        $coupon = self::getCoupon();
        if (!$coupon) {
            return 0;
        }
        
        $subtotal = self::getSubtotal();
        
        if ($subtotal < $coupon['min_order_amount']) {
            return 0;
        }
        
        if ($coupon['type'] === 'percentage') {
            $discount = $subtotal * ($coupon['value'] / 100);
            if ($coupon['max_discount']) {
                $discount = min($discount, $coupon['max_discount']);
            }
            return (float)$discount;
        }
        
        return (float)$coupon['value'];
    }

    public static function getShippingCost(): float
    {
        $subtotal = self::getSubtotal();
        $freeThreshold = (float)Settings::get('shipping_free_threshold', 150);
        
        if ($subtotal >= $freeThreshold) {
            return 0;
        }
        
        return (float)Settings::get('shipping_flat_rate', 9.99);
    }

    public static function getTax(): float
    {
        $taxRate = (float)Settings::get('tax_rate', 0);
        $subtotal = self::getSubtotal() - self::getDiscount();
        
        return $subtotal * ($taxRate / 100);
    }

    public static function applyCoupon(string $code): array
    {
        $coupon = Database::fetch(
            "SELECT * FROM coupons WHERE code = ? AND status = 'active' 
             AND (expires_at IS NULL OR expires_at >= NOW()) 
             AND (starts_at IS NULL OR starts_at <= NOW())",
            [$code]
        );
        
        if (!$coupon) {
            return ['success' => false, 'message' => 'Invalid or expired coupon code'];
        }
        
        if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) {
            return ['success' => false, 'message' => 'Coupon usage limit reached'];
        }
        
        $subtotal = self::getSubtotal();
        if ($subtotal < $coupon['min_order_amount']) {
            return [
                'success' => false, 
                'message' => 'Minimum order amount of ' . format_price($coupon['min_order_amount']) . ' required'
            ];
        }
        
        $_SESSION[self::COUPON_KEY] = $coupon;
        
        return [
            'success' => true, 
            'message' => 'Coupon applied successfully',
            'coupon' => $coupon
        ];
    }

    public static function getCoupon(): ?array
    {
        return $_SESSION[self::COUPON_KEY] ?? null;
    }

    public static function removeCoupon(): void
    {
        unset($_SESSION[self::COUPON_KEY]);
    }

    public static function validateStock(): array
    {
        $cart = self::get();
        $errors = [];
        
        foreach ($cart as $key => $item) {
            $product = Product::getById($item['product_id']);
            
            if (!$product) {
                $errors[$key] = 'Product no longer available';
                continue;
            }
            
            if ($item['variant_id']) {
                $variant = Database::fetch(
                    "SELECT * FROM product_variants WHERE id = ?",
                    [$item['variant_id']]
                );
                
                if ($variant && $variant['stock'] < $item['quantity']) {
                    $errors[$key] = 'Requested quantity not available. Only ' . $variant['stock'] . ' in stock.';
                }
            } else {
                if ($product['stock'] < $item['quantity']) {
                    $errors[$key] = 'Requested quantity not available. Only ' . $product['stock'] . ' in stock.';
                }
            }
        }
        
        return $errors;
    }

    private static function generateKey(int $productId, array $variant): string
    {
        $parts = [$productId];
        
        if (!empty($variant['size'])) {
            $parts[] = 'size:' . $variant['size'];
        }
        if (!empty($variant['color'])) {
            $parts[] = 'color:' . $variant['color'];
        }
        if (!empty($variant['id'])) {
            $parts[] = 'variant:' . $variant['id'];
        }
        
        return implode('-', $parts);
    }

    private static function getProductImage(int $productId): ?string
    {
        $image = Database::fetch(
            "SELECT url FROM product_images WHERE product_id = ? AND is_primary = 1 LIMIT 1",
            [$productId]
        );
        return $image ? $image['url'] : null;
    }
}
