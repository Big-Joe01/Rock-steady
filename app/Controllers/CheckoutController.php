<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\StripeService;
use App\Services\MailService;
use App\Services\Logger;

class CheckoutController extends Controller
{
    public function initiate(): \App\Views\Response
    {
        if (Cart::isEmpty()) {
            return $this->redirect('/cart');
        }
        
        $stockErrors = Cart::validateStock();
        if (!empty($stockErrors)) {
            foreach ($stockErrors as $error) {
                $this->withError($error);
            }
            return $this->redirect('/cart');
        }
        
        $cart = Cart::get();
        $items = [];
        
        foreach ($cart as $item) {
            $items[] = [
                'name' => $item['name'] . ($item['size'] ? ' - ' . $item['size'] : '') . ($item['color'] ? ' - ' . $item['color'] : ''),
                'description' => $item['sku'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'images' => $item['image_url'] ? [$item['image_url']] : [],
            ];
        }
        
        $coupon = Cart::getCoupon();
        $metadata = [
            'user_id' => auth_check() ? auth()['id'] : null,
            'coupon_code' => $coupon['code'] ?? '',
        ];
        
        $successUrl = url('/checkout/success?session_id={CHECKOUT_SESSION_ID}');
        $cancelUrl = url('/checkout/cancel');
        
        $stripe = new StripeService();
        $result = $stripe->createCheckoutSession($items, $successUrl, $cancelUrl, $metadata);
        
        if (!$result['success']) {
            Logger::error('Stripe checkout failed: ' . $result['error']);
            $this->withError('Payment initialization failed. Please try again.');
            return $this->redirect('/checkout');
        }
        
        return $this->redirect($result['url']);
    }

    public function success(): \App\Views\Response
    {
        $sessionId = $_GET['session_id'] ?? '';
        
        if (empty($sessionId)) {
            return $this->redirect('/');
        }
        
        $stripe = new StripeService();
        $session = $stripe->retrieveSession($sessionId);
        
        if (!$session || $session['payment_status'] !== 'paid') {
            $this->withError('Payment verification failed');
            return $this->redirect('/checkout');
        }
        
        $existingOrder = Order::getByOrderNumber($session['metadata']['order_number'] ?? '');
        if ($existingOrder) {
            return $this->view('frontend/checkout/success', [
                'title' => 'Order Confirmed | ' . APP_NAME,
                'order' => $existingOrder,
            ]);
        }
        
        $cart = Cart::get();
        
        $shipping = $session['shipping_details']['address'] ?? [];
        
        $subtotal = (float)$session['amount_subtotal'] / 100;
        $total = (float)$session['amount_total'] / 100;
        $shippingCost = $session['shipping_cost'] ?? 0;
        $tax = $session['total_tax'] ?? 0;
        
        $orderId = Order::create([
            'order_number' => 'RS-' . strtoupper(substr(md5(uniqid()), 0, 8)) . '-' . date('Ymd'),
            'user_id' => auth_check() ? auth()['id'] : null,
            'email' => $session['customer_email'] ?? $session['customer_details']['email'] ?? '',
            'first_name' => $session['customer_details']['name'] ?? '',
            'last_name' => '',
            'phone' => $session['customer_details']['phone'] ?? '',
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'tax' => $tax,
            'discount' => $subtotal + $shippingCost + $tax - $total,
            'coupon_code' => $session['metadata']['coupon_code'] ?? null,
            'total' => $total,
            'currency' => STRIPE_CURRENCY,
            'status' => 'paid',
            'payment_method' => 'stripe',
            'payment_id' => $session['payment_intent'] ?? '',
            'stripe_session_id' => $sessionId,
            'shipping_first_name' => $shipping['recipient'] ?? '',
            'shipping_address_line1' => $shipping['line1'] ?? '',
            'shipping_address_line2' => $shipping['line2'] ?? '',
            'shipping_city' => $shipping['city'] ?? '',
            'shipping_state' => $shipping['state'] ?? '',
            'shipping_postal_code' => $shipping['postal_code'] ?? '',
            'shipping_country' => $shipping['country'] ?? '',
            'shipping_phone' => $session['customer_details']['phone'] ?? '',
            'ip_address' => get_client_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        ]);
        
        foreach ($cart as $item) {
            Order::addItem($orderId, [
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'],
                'sku' => $item['sku'],
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'size' => $item['size'],
                'color' => $item['color'],
                'image_url' => $item['image_url'],
            ]);
            
            if (!empty($item['variant_id'])) {
                \App\Models\Database::query(
                    "UPDATE product_variants SET stock = stock - ? WHERE id = ?",
                    [$item['quantity'], $item['variant_id']]
                );
            } else {
                Product::updateStock($item['product_id'], $item['quantity']);
            }
        }
        
        $order = Order::getById($orderId);
        $orderItems = Order::getItems($orderId);
        
        Cart::clear();
        
        MailService::sendTemplate(
            $order['email'],
            'Order Confirmed - ' . $order['order_number'],
            'order_confirmation',
            [
                'order' => $order,
                'items' => $orderItems,
            ]
        );
        
        MailService::sendTemplate(
            SMTP_FROM_EMAIL,
            'New Order - ' . $order['order_number'],
            'order_admin',
            [
                'order' => $order,
                'items' => $orderItems,
            ]
        );
        
        $data = [
            'title' => 'Order Confirmed | ' . APP_NAME,
            'order' => $order,
            'items' => $orderItems,
        ];
        
        return $this->view('frontend/checkout/success', $data);
    }

    public function cancel(): \App\Views\Response
    {
        $this->withError('Payment was cancelled. Please try again.');
        return $this->redirect('/checkout');
    }

    public function webhook(): void
    {
        $payload = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
        
        $stripe = new StripeService();
        
        if (!$stripe->constructWebhookEvent($payload, $signature)) {
            http_response_code(400);
            exit;
        }
        
        $event = json_decode($payload, true);
        
        switch ($event['type']) {
            case 'checkout.session.completed':
                Logger::info('Checkout completed webhook received');
                break;
                
            case 'payment_intent.succeeded':
                Logger::info('Payment succeeded webhook received');
                break;
                
            case 'payment_intent.payment_failed':
                $paymentIntentId = $event['data']['object']['id'];
                Logger::warning('Payment failed: ' . $paymentIntentId);
                break;
        }
        
        http_response_code(200);
    }
}
