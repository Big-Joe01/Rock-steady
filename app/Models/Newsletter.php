<?php

declare(strict_types=1);

namespace App\Models;

class Newsletter
{
    public static function subscribe(string $email, string $name = null): array
    {
        $existing = Database::fetch(
            "SELECT * FROM newsletter WHERE email = ?",
            [$email]
        );
        
        if ($existing) {
            if ($existing['status'] === 'unsubscribed') {
                Database::update('newsletter', [
                    'status' => 'active',
                    'unsubscribed_at' => null,
                ], 'id = ?', [$existing['id']]);
                return ['success' => true, 'message' => 'Successfully re-subscribed!'];
            }
            
            return ['success' => false, 'message' => 'Email is already subscribed'];
        }
        
        Database::insert('newsletter', [
            'email' => $email,
            'name' => $name,
            'status' => 'active',
            'ip_address' => get_client_ip(),
        ]);
        
        return ['success' => true, 'message' => 'Successfully subscribed!'];
    }

    public static function unsubscribe(string $email): bool
    {
        return Database::update('newsletter', [
            'status' => 'unsubscribed',
            'unsubscribed_at' => date('Y-m-d H:i:s'),
        ], 'email = ?', [$email]) > 0;
    }

    public static function isSubscribed(string $email): bool
    {
        $result = Database::fetch(
            "SELECT id FROM newsletter WHERE email = ? AND status = 'active'",
            [$email]
        );
        return $result !== null;
    }

    public static function getActive(): array
    {
        return Database::fetchAll(
            "SELECT * FROM newsletter WHERE status = 'active' ORDER BY subscribed_at DESC"
        );
    }

    public static function getAll(int $page = 1, int $perPage = 50): array
    {
        $offset = ($page - 1) * $perPage;
        
        $subscribers = Database::fetchAll(
            "SELECT * FROM newsletter ORDER BY subscribed_at DESC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );
        
        $total = Database::count('newsletter');
        
        return [
            'items' => $subscribers,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    public static function getStats(): array
    {
        return [
            'total' => Database::count('newsletter'),
            'active' => Database::count('newsletter', 'status = ?', ['active']),
            'unsubscribed' => Database::count('newsletter', 'status = ?', ['unsubscribed']),
        ];
    }
}
