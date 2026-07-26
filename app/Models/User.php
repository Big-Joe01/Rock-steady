<?php

declare(strict_types=1);

namespace App\Models;

class User
{
    public static function getById(int $id): ?array
    {
        $user = Database::fetch("SELECT * FROM users WHERE id = ?", [$id]);
        if ($user) {
            unset($user['password'], $user['remember_token']);
        }
        return $user;
    }

    public static function getByEmail(string $email): ?array
    {
        return Database::fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }

    public static function create(array $data): int
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return Database::insert('users', $data);
    }

    public static function update(int $id, array $data): int
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return Database::update('users', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): int
    {
        return Database::delete('users', 'id = ?', [$id]);
    }

    public static function verifyPassword(string $email, string $password): ?array
    {
        $user = self::getByEmail($email);
        
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password'], $user['remember_token']);
            return $user;
        }
        
        return null;
    }

    public static function updatePassword(int $id, string $password): bool
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        return Database::update('users', ['password' => $hashed], 'id = ?', [$id]) > 0;
    }

    public static function generateRememberToken(int $id): string
    {
        $token = bin2hex(random_bytes(32));
        Database::update('users', ['remember_token' => hash('sha256', $token)], 'id = ?', [$id]);
        return $token;
    }

    public static function getAddresses(int $userId): array
    {
        return Database::fetchAll(
            "SELECT * FROM user_addresses WHERE user_id = ? ORDER BY is_default DESC, created_at DESC",
            [$userId]
        );
    }

    public static function addAddress(int $userId, array $data): int
    {
        $data['user_id'] = $userId;
        
        if (!empty($data['is_default'])) {
            Database::query("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?", [$userId]);
        }
        
        return Database::insert('user_addresses', $data);
    }

    public static function updateAddress(int $addressId, int $userId, array $data): int
    {
        if (!empty($data['is_default'])) {
            Database::query("UPDATE user_addresses SET is_default = 0 WHERE user_id = ?", [$userId]);
        }
        
        return Database::update('user_addresses', $data, 'id = ? AND user_id = ?', [$addressId, $userId]);
    }

    public static function deleteAddress(int $addressId, int $userId): int
    {
        return Database::delete('user_addresses', 'id = ? AND user_id = ?', [$addressId, $userId]);
    }

    public static function getWishlist(int $userId): array
    {
        return Database::fetchAll(
            "SELECT p.*, pi.url as image_url, w.created_at as added_at
             FROM wishlists w
             INNER JOIN products p ON w.product_id = p.id
             LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
             WHERE w.user_id = ?
             ORDER BY w.created_at DESC",
            [$userId]
        );
    }

    public static function addToWishlist(int $userId, int $productId): bool
    {
        $existing = Database::fetch(
            "SELECT * FROM wishlists WHERE user_id = ? AND product_id = ?",
            [$userId, $productId]
        );
        
        if ($existing) {
            return true;
        }
        
        Database::insert('wishlists', [
            'user_id' => $userId,
            'product_id' => $productId,
        ]);
        
        return true;
    }

    public static function removeFromWishlist(int $userId, int $productId): int
    {
        return Database::delete(
            'wishlists',
            'user_id = ? AND product_id = ?',
            [$userId, $productId]
        );
    }

    public static function isInWishlist(int $userId, int $productId): bool
    {
        $result = Database::fetch(
            "SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?",
            [$userId, $productId]
        );
        return $result !== null;
    }

    public static function getRecentlyViewed(int $userId, int $limit = 10): array
    {
        $sessionId = session_id();
        
        return Database::fetchAll(
            "SELECT DISTINCT p.*, pi.url as image_url, rv.created_at as viewed_at
             FROM recently_viewed rv
             INNER JOIN products p ON rv.product_id = p.id
             LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
             WHERE rv.user_id = ? OR rv.session_id = ?
             ORDER BY rv.created_at DESC
             LIMIT ?",
            [$userId, $sessionId, $limit]
        );
    }

    public static function addRecentlyViewed(int $userId, int $productId): void
    {
        $sessionId = $userId ? null : session_id();
        
        $existing = Database::fetch(
            "SELECT id FROM recently_viewed WHERE (user_id = ? OR session_id = ?) AND product_id = ?",
            [$userId ?? 0, $sessionId ?? '', $productId]
        );
        
        if ($existing) {
            Database::query(
                "UPDATE recently_viewed SET created_at = NOW() WHERE id = ?",
                [$existing['id']]
            );
        } else {
            Database::insert('recently_viewed', [
                'user_id' => $userId ?: null,
                'session_id' => $userId ? null : $sessionId,
                'product_id' => $productId,
            ]);
            
            Database::query(
                "DELETE FROM recently_viewed 
                 WHERE (user_id = ? OR session_id = ?) 
                 AND id NOT IN (
                     SELECT id FROM (
                         SELECT id FROM recently_viewed 
                         WHERE user_id = ? OR session_id = ?
                         ORDER BY created_at DESC LIMIT 20
                     ) as t
                 )",
                [$userId ?? 0, $sessionId ?? '', $userId ?? 0, $sessionId ?? '']
            );
        }
    }
}
