<?php

declare(strict_types=1);

namespace App\Services;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;
use App\Models\Database;

class StripeService
{
    public function __construct()
    {
        Stripe::setApiKey(STRIPE_SECRET_KEY);
        Stripe::setApiVersion('2023-10-16');
    }

    public function createCheckoutSession(array $items, string $successUrl, string $cancelUrl, array $metadata = []): array
    {
        try {
            $lineItems = [];
            
            foreach ($items as $item) {
                $lineItems[] = [
                    'price_data' => [
                        'currency' => STRIPE_CURRENCY,
                        'product_data' => [
                            'name' => $item['name'],
                            'description' => $item['description'] ?? '',
                            'images' => $item['images'] ?? [],
                        ],
                        'unit_amount' => (int)($item['price'] * 100),
                    ],
                    'quantity' => $item['quantity'],
                ];
            }

            $session = Session::create([
                'mode' => 'payment',
                'line_items' => $lineItems,
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
                'shipping_address_collection' => [
                    'allowed_countries' => ['US', 'CA', 'GB', 'AU', 'DE', 'FR', 'IT', 'ES', 'NL', 'BE'],
                ],
                'shipping_options' => [
                    [
                        'shipping_rate_data' => [
                            'type' => 'fixed_amount',
                            'fixed_amount' => [
                                'amount' => 0,
                                'currency' => STRIPE_CURRENCY,
                            ],
                            'display_name' => 'Free Shipping',
                            'delivery_estimate' => [
                                'minimum' => ['unit' => 'business_day', 'value' => 5],
                                'maximum' => ['unit' => 'business_day', 'value' => 10],
                            ],
                        ],
                    ],
                    [
                        'shipping_rate_data' => [
                            'type' => 'fixed_amount',
                            'fixed_amount' => [
                                'amount' => 1500,
                                'currency' => STRIPE_CURRENCY,
                            ],
                            'display_name' => 'Express Shipping',
                            'delivery_estimate' => [
                                'minimum' => ['unit' => 'business_day', 'value' => 2],
                                'maximum' => ['unit' => 'business_day', 'value' => 3],
                            ],
                        ],
                    ],
                ],
                'billing_address_collection' => 'required',
                'payment_method_types' => ['card', 'apple_pay', 'google_pay'],
                'allow_promotion_codes' => true,
                'metadata' => $metadata,
            ]);

            return [
                'success' => true,
                'session_id' => $session->id,
                'url' => $session->url,
            ];
        } catch (\Exception $e) {
            Logger::error('Stripe checkout session creation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function retrieveSession(string $sessionId): ?array
    {
        try {
            $session = Session::retrieve($sessionId);
            return $session->toArray();
        } catch (\Exception $e) {
            Logger::error('Stripe session retrieval failed: ' . $e->getMessage());
            return null;
        }
    }

    public function constructWebhookEvent(string $payload, string $signature): bool
    {
        try {
            Webhook::constructEvent($payload, $signature, STRIPE_WEBHOOK_SECRET);
            return true;
        } catch (SignatureVerificationException $e) {
            Logger::error('Stripe webhook signature verification failed: ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            Logger::error('Stripe webhook construction failed: ' . $e->getMessage());
            return false;
        }
    }

    public function createCustomer(string $email, string $name, array $metadata = []): ?array
    {
        try {
            $customer = Customer::create([
                'email' => $email,
                'name' => $name,
                'metadata' => $metadata,
            ]);
            
            return $customer->toArray();
        } catch (\Exception $e) {
            Logger::error('Stripe customer creation failed: ' . $e->getMessage());
            return null;
        }
    }

    public function refund(string $paymentIntentId, int $amount = null): bool
    {
        try {
            $params = ['payment_intent' => $paymentIntentId];
            
            if ($amount) {
                $params['amount'] = $amount;
            }
            
            \Stripe\Refund::create($params);
            return true;
        } catch (\Exception $e) {
            Logger::error('Stripe refund failed: ' . $e->getMessage());
            return false;
        }
    }

    public static function getPublishableKey(): string
    {
        return STRIPE_PUBLISHABLE_KEY;
    }
}
