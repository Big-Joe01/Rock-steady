<?php

declare(strict_types=1);

namespace App\Models;

class Product
{
    public static function getAll(array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $where = ['p.visibility = ?' => 'visible'];
        $params = ['visible'];
        
        if (!empty($filters['category'])) {
            $where['p.category_id = ?'] = (int)$filters['category'];
        }
        
        if (!empty($filters['collection'])) {
            $where['p.collection_id = ?'] = (int)$filters['collection'];
        }
        
        if (!empty($filters['gender'])) {
            $where['p.gender = ?'] = $filters['gender'];
        }
        
        if (!empty($filters['min_price'])) {
            $where['p.price >= ?'] = (float)$filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $where['p.price <= ?'] = (float)$filters['max_price'];
        }
        
        if (!empty($filters['featured'])) {
            $where['p.featured = ?'] = 1;
        }
        
        if (!empty($filters['is_new'])) {
            $where['p.is_new = ?'] = 1;
        }
        
        if (!empty($filters['search'])) {
            $where['(p.name LIKE ? OR p.description LIKE ?)'] = '%' . $filters['search'] . '%';
        }
        
        $orderBy = 'p.created_at DESC';
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $orderBy = 'p.price ASC';
                    break;
                case 'price_desc':
                    $orderBy = 'p.price DESC';
                    break;
                case 'oldest':
                    $orderBy = 'p.created_at ASC';
                    break;
                case 'popular':
                    $orderBy = 'p.stock DESC';
                    break;
                case 'name':
                    $orderBy = 'p.name ASC';
                    break;
            }
        }
        
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id";
        
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', array_keys($where));
        }
        
        $sql .= " ORDER BY {$orderBy} LIMIT ? OFFSET ?";
        
        $allParams = array_values($params);
        $allParams[] = $perPage;
        $allParams[] = $offset;
        
        $products = Database::fetchAll($sql, $allParams);
        
        $countSql = "SELECT COUNT(*) as count FROM products p";
        if (!empty($where)) {
            $countSql .= ' WHERE ' . implode(' AND ', array_keys($where));
        }
        $total = (int)Database::fetch($countSql, array_values($params))['count'];
        
        return [
            'items' => $products,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    public static function getById(int $id): ?array
    {
        return Database::fetch(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.id = ?",
            [$id]
        );
    }

    public static function getBySlug(string $slug): ?array
    {
        return Database::fetch(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.slug = ? AND p.visibility = 'visible'",
            [$slug]
        );
    }

    public static function getImages(int $productId): array
    {
        return Database::fetchAll(
            "SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, is_primary DESC",
            [$productId]
        );
    }

    public static function getVariants(int $productId): array
    {
        return Database::fetchAll(
            "SELECT * FROM product_variants WHERE product_id = ? ORDER BY size, color",
            [$productId]
        );
    }

    public static function getRelated(int $productId, int $limit = 4): array
    {
        $product = self::getById($productId);
        if (!$product) {
            return [];
        }
        
        return Database::fetchAll(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.id != ? AND p.category_id = ? AND p.visibility = 'visible' 
             ORDER BY RAND() 
             LIMIT ?",
            [$productId, $product['category_id'] ?? 0, $limit]
        );
    }

    public static function getFeatured(int $limit = 8): array
    {
        return Database::fetchAll(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.featured = 1 AND p.visibility = 'visible' 
             ORDER BY p.created_at DESC 
             LIMIT ?",
            [$limit]
        );
    }

    public static function getNewArrivals(int $limit = 8): array
    {
        $days = NEW_ARRIVALS_DAYS;
        return Database::fetchAll(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.is_new = 1 AND p.visibility = 'visible' 
             AND p.created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
             ORDER BY p.created_at DESC 
             LIMIT ?",
            [$limit]
        );
    }

    public static function getTrending(int $limit = 8): array
    {
        return Database::fetchAll(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.trending = 1 AND p.visibility = 'visible' 
             ORDER BY p.created_at DESC 
             LIMIT ?",
            [$limit]
        );
    }

    public static function getBestSellers(int $limit = 8): array
    {
        return Database::fetchAll(
            "SELECT p.*, c.name as category_name, 
                    SUM(oi.quantity) as total_sold
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             LEFT JOIN order_items oi ON p.id = oi.product_id 
             WHERE p.visibility = 'visible' 
             GROUP BY p.id 
             ORDER BY total_sold DESC NULLS LAST 
             LIMIT ?",
            [$limit]
        );
    }

    public static function search(string $query, int $limit = 10): array
    {
        return Database::fetchAll(
            "SELECT p.id, p.name, p.slug, p.price, p.sale_price, 
                    pi.url as image_url,
                    c.name as category_name
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
             WHERE p.name LIKE ? AND p.visibility = 'visible' 
             ORDER BY p.name ASC 
             LIMIT ?",
            ['%' . $query . '%', $limit]
        );
    }

    public static function getByCollection(int $collectionId, int $limit = 50): array
    {
        return Database::fetchAll(
            "SELECT p.*, c.name as category_name, cp.sort_order
             FROM products p 
             INNER JOIN collection_products cp ON p.id = cp.product_id 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE cp.collection_id = ? AND p.visibility = 'visible' 
             ORDER BY cp.sort_order ASC 
             LIMIT ?",
            [$collectionId, $limit]
        );
    }

    public static function getLowStock(int $threshold = 10): array
    {
        return Database::fetchAll(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.stock <= ? AND p.visibility = 'visible' 
             ORDER BY p.stock ASC",
            [$threshold]
        );
    }

    public static function create(array $data): int
    {
        return Database::insert('products', $data);
    }

    public static function update(int $id, array $data): int
    {
        return Database::update('products', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): int
    {
        return Database::delete('products', 'id = ?', [$id]);
    }

    public static function addImage(int $productId, array $data): int
    {
        $data['product_id'] = $productId;
        return Database::insert('product_images', $data);
    }

    public static function deleteImage(int $imageId): bool
    {
        return Database::delete('product_images', 'id = ?', [$imageId]) > 0;
    }

    public static function addVariant(int $productId, array $data): int
    {
        $data['product_id'] = $productId;
        return Database::insert('product_variants', $data);
    }

    public static function updateVariant(int $variantId, array $data): int
    {
        return Database::update('product_variants', $data, 'id = ?', [$variantId]);
    }

    public static function deleteVariant(int $variantId): int
    {
        return Database::delete('product_variants', 'id = ?', [$variantId]);
    }

    public static function updateStock(int $id, int $quantity): int
    {
        return Database::query(
            "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?",
            [$quantity, $id, $quantity]
        )->rowCount();
    }

    public static function getReviews(int $productId, string $status = 'approved'): array
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

    public static function getAverageRating(int $productId): float
    {
        $result = Database::fetch(
            "SELECT AVG(rating) as avg_rating 
             FROM reviews 
             WHERE product_id = ? AND status = 'approved'",
            [$productId]
        );
        return $result ? round((float)$result['avg_rating'], 1) : 0;
    }

    public static function getReviewStats(int $productId): array
    {
        $stats = Database::fetchAll(
            "SELECT rating, COUNT(*) as count 
             FROM reviews 
             WHERE product_id = ? AND status = 'approved' 
             GROUP BY rating 
             ORDER BY rating DESC",
            [$productId]
        );
        
        $total = 0;
        $result = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        
        foreach ($stats as $stat) {
            $result[$stat['rating']] = (int)$stat['count'];
            $total += (int)$stat['count'];
        }
        
        return [
            'total' => $total,
            'distribution' => $result,
            'average' => self::getAverageRating($productId),
        ];
    }
}
