<?php

declare(strict_types=1);

namespace App\Models;

class Collection
{
    public static function getAll(string $status = 'active'): array
    {
        return Database::fetchAll(
            "SELECT * FROM collections WHERE status = ? OR 'active' = ? ORDER BY sort_order ASC",
            [$status, $status === 'all' ? 'all' : $status]
        );
    }

    public static function getById(int $id): ?array
    {
        return Database::fetch("SELECT * FROM collections WHERE id = ?", [$id]);
    }

    public static function getBySlug(string $slug): ?array
    {
        return Database::fetch("SELECT * FROM collections WHERE slug = ?", [$slug]);
    }

    public static function getWithProducts(): array
    {
        return Database::fetchAll(
            "SELECT c.*, COUNT(cp.product_id) as product_count 
             FROM collections c 
             LEFT JOIN collection_products cp ON c.id = cp.collection_id 
             WHERE c.status = 'active' 
             GROUP BY c.id 
             ORDER BY c.sort_order ASC"
        );
    }

    public static function getFeatured(int $limit = 4): array
    {
        return Database::fetchAll(
            "SELECT * FROM collections WHERE status = 'active' ORDER BY sort_order ASC LIMIT ?",
            [$limit]
        );
    }

    public static function getActive(): array
    {
        $now = date('Y-m-d');
        return Database::fetchAll(
            "SELECT * FROM collections 
             WHERE status = 'active' 
             AND (start_date IS NULL OR start_date <= ?)
             AND (end_date IS NULL OR end_date >= ?)
             ORDER BY sort_order ASC",
            [$now, $now]
        );
    }

    public static function create(array $data): int
    {
        return Database::insert('collections', $data);
    }

    public static function update(int $id, array $data): int
    {
        return Database::update('collections', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): int
    {
        return Database::delete('collections', 'id = ?', [$id]);
    }

    public static function addProduct(int $collectionId, int $productId, int $sortOrder = 0): bool
    {
        $existing = Database::fetch(
            "SELECT * FROM collection_products WHERE collection_id = ? AND product_id = ?",
            [$collectionId, $productId]
        );
        
        if ($existing) {
            return true;
        }
        
        Database::insert('collection_products', [
            'collection_id' => $collectionId,
            'product_id' => $productId,
            'sort_order' => $sortOrder,
        ]);
        
        return true;
    }

    public static function removeProduct(int $collectionId, int $productId): int
    {
        return Database::delete(
            'collection_products',
            'collection_id = ? AND product_id = ?',
            [$collectionId, $productId]
        );
    }

    public static function reorderProducts(int $collectionId, array $productIds): void
    {
        foreach ($productIds as $index => $productId) {
            Database::update(
                'collection_products',
                ['sort_order' => $index],
                'collection_id = ? AND product_id = ?',
                [$collectionId, $productId]
            );
        }
    }
}
