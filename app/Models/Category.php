<?php

declare(strict_types=1);

namespace App\Models;

class Category
{
    public static function getAll(string $status = 'active'): array
    {
        return Database::fetchAll(
            "SELECT * FROM categories WHERE status = ? OR 'active' = ? ORDER BY sort_order ASC",
            [$status, $status === 'all' ? 'all' : $status]
        );
    }

    public static function getById(int $id): ?array
    {
        return Database::fetch("SELECT * FROM categories WHERE id = ?", [$id]);
    }

    public static function getBySlug(string $slug): ?array
    {
        return Database::fetch("SELECT * FROM categories WHERE slug = ?", [$slug]);
    }

    public static function getWithProducts(): array
    {
        return Database::fetchAll(
            "SELECT c.*, COUNT(p.id) as product_count 
             FROM categories c 
             LEFT JOIN products p ON c.id = p.category_id AND p.visibility = 'visible' 
             WHERE c.status = 'active' 
             GROUP BY c.id 
             ORDER BY c.sort_order ASC"
        );
    }

    public static function getTree(): array
    {
        $categories = self::getAll();
        $tree = [];
        
        foreach ($categories as $category) {
            if ($category['parent_id'] === null) {
                $tree[$category['id']] = $category;
                $tree[$category['id']]['children'] = [];
            }
        }
        
        foreach ($categories as $category) {
            if ($category['parent_id'] !== null && isset($tree[$category['parent_id']])) {
                $tree[$category['parent_id']]['children'][] = $category;
            }
        }
        
        return $tree;
    }

    public static function create(array $data): int
    {
        return Database::insert('categories', $data);
    }

    public static function update(int $id, array $data): int
    {
        return Database::update('categories', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): int
    {
        return Database::delete('categories', 'id = ?', [$id]);
    }
}
