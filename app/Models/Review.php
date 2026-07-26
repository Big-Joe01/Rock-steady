<?php

declare(strict_types=1);

namespace App\Models;

class Review
{
    public static function create(array $data): int
    {
        return Database::insert('reviews', $data);
    }

    public static function update(int $id, array $data): int
    {
        return Database::update('reviews', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): int
    {
        return Database::delete('reviews', 'id = ?', [$id]);
    }

    public static function getById(int $id): ?array
    {
        return Database::fetch(
            "SELECT r.*, u.first_name, u.last_name, u.avatar 
             FROM reviews r 
             LEFT JOIN users u ON r.user_id = u.id 
             WHERE r.id = ?",
            [$id]
        );
    }

    public static function getByProduct(int $productId, string $status = 'approved'): array
    {
        return Database::fetchAll(
            "SELECT r.*, u.first_name, u.last_name, u.avatar 
             FROM reviews r 
             LEFT JOIN users u ON r.user_id = u.id 
             WHERE r.product_id = ? AND r.status = ? 
             ORDER BY r.created_at DESC",
            [$productId, $status]
        );
    }

    public static function getByUser(int $userId): array
    {
        return Database::fetchAll(
            "SELECT r.*, p.name as product_name, p.slug as product_slug, pi.url as product_image
             FROM reviews r 
             LEFT JOIN products p ON r.product_id = p.id 
             LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
             WHERE r.user_id = ? 
             ORDER BY r.created_at DESC",
            [$userId]
        );
    }

    public static function updateStatus(int $id, string $status): int
    {
        return Database::update('reviews', ['status' => $status], 'id = ?', [$id]);
    }

    public static function incrementHelpful(int $id): int
    {
        return Database::query(
            "UPDATE reviews SET helpful_count = helpful_count + 1 WHERE id = ?",
            [$id]
        )->rowCount();
    }

    public static function hasUserReviewed(int $userId, int $productId): bool
    {
        $result = Database::fetch(
            "SELECT id FROM reviews WHERE user_id = ? AND product_id = ?",
            [$userId, $productId]
        );
        return $result !== null;
    }

    public static function getStats(): array
    {
        return [
            'total' => Database::count('reviews'),
            'pending' => Database::count('reviews', 'status = ?', ['pending']),
            'approved' => Database::count('reviews', 'status = ?', ['approved']),
            'rejected' => Database::count('reviews', 'status = ?', ['rejected']),
        ];
    }
}
