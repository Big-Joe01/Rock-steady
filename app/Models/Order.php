<?php

declare(strict_types=1);

namespace App\Models;

class Order
{
    public static function generateOrderNumber(): string
    {
        return 'RS-' . strtoupper(bin2hex(random_bytes(4))) . '-' . date('Ymd');
    }

    public static function create(array $data): int
    {
        if (empty($data['order_number'])) {
            $data['order_number'] = self::generateOrderNumber();
        }
        
        return Database::insert('orders', $data);
    }

    public static function update(int $id, array $data): int
    {
        return Database::update('orders', $data, 'id = ?', [$id]);
    }

    public static function getById(int $id): ?array
    {
        return Database::fetch("SELECT * FROM orders WHERE id = ?", [$id]);
    }

    public static function getByOrderNumber(string $orderNumber): ?array
    {
        return Database::fetch("SELECT * FROM orders WHERE order_number = ?", [$orderNumber]);
    }

    public static function getByUser(int $userId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        
        $orders = Database::fetchAll(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$userId, $perPage, $offset]
        );
        
        $total = Database::count('orders', 'user_id = ?', [$userId]);
        
        return [
            'items' => $orders,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    public static function getItems(int $orderId): array
    {
        return Database::fetchAll(
            "SELECT oi.*, p.slug as product_slug 
             FROM order_items oi 
             LEFT JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ?",
            [$orderId]
        );
    }

    public static function addItem(int $orderId, array $data): int
    {
        $data['order_id'] = $orderId;
        return Database::insert('order_items', $data);
    }

    public static function getAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = ['1=1'];
        $params = [];
        
        if (!empty($filters['status'])) {
            $where[] = 'o.status = ?';
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = '(o.order_number LIKE ? OR o.email LIKE ? OR o.first_name LIKE ? OR o.last_name LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }
        
        if (!empty($filters['date_from'])) {
            $where[] = 'o.created_at >= ?';
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $where[] = 'o.created_at <= ?';
            $params[] = $filters['date_to'];
        }
        
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT o.*, u.first_name as user_first_name, u.last_name as user_last_name 
                FROM orders o 
                LEFT JOIN users u ON o.user_id = u.id 
                WHERE " . implode(' AND ', $where) . " 
                ORDER BY o.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $params[] = $perPage;
        $params[] = $offset;
        
        $orders = Database::fetchAll($sql, $params);
        
        $countSql = "SELECT COUNT(*) as count FROM orders o WHERE " . implode(' AND ', $where);
        $total = (int)Database::fetch($countSql, array_slice($params, 0, -2))['count'];
        
        return [
            'items' => $orders,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    public static function getStats(): array
    {
        $today = date('Y-m-d');
        $startOfMonth = date('Y-m-01');
        $startOfYear = date('Y-01-01');
        
        $totalRevenue = Database::fetch(
            "SELECT COALESCE(SUM(total), 0) as total FROM orders WHERE status != 'cancelled' AND status != 'refunded'"
        )['total'];
        
        $monthRevenue = Database::fetch(
            "SELECT COALESCE(SUM(total), 0) as total FROM orders WHERE status != 'cancelled' AND status != 'refunded' AND created_at >= ?",
            [$startOfMonth]
        )['total'];
        
        $todayRevenue = Database::fetch(
            "SELECT COALESCE(SUM(total), 0) as total FROM orders WHERE status != 'cancelled' AND status != 'refunded' AND DATE(created_at) = ?",
            [$today]
        )['total'];
        
        $totalOrders = Database::count('orders', '1=1');
        $pendingOrders = Database::count('orders', 'status = ?', ['pending']);
        $processingOrders = Database::count('orders', 'status = ?', ['processing']);
        
        $yearRevenue = Database::fetch(
            "SELECT COALESCE(SUM(total), 0) as total FROM orders WHERE status != 'cancelled' AND status != 'refunded' AND created_at >= ?",
            [$startOfYear]
        )['total'];
        
        return [
            'total_revenue' => (float)$totalRevenue,
            'month_revenue' => (float)$monthRevenue,
            'today_revenue' => (float)$todayRevenue,
            'year_revenue' => (float)$yearRevenue,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'processing_orders' => $processingOrders,
        ];
    }

    public static function updateStatus(int $id, string $status): int
    {
        $data = ['status' => $status];
        
        if ($status === 'shipped') {
            $data['shipped_at'] = date('Y-m-d H:i:s');
        } elseif ($status === 'delivered') {
            $data['delivered_at'] = date('Y-m-d H:i:s');
        }
        
        return Database::update('orders', $data, 'id = ?', [$id]);
    }

    public static function updateTracking(int $id, string $trackingNumber, string $trackingUrl = ''): int
    {
        return Database::update('orders', [
            'tracking_number' => $trackingNumber,
            'tracking_url' => $trackingUrl,
            'status' => 'shipped',
            'shipped_at' => date('Y-m-d H:i:s'),
        ], 'id = ?', [$id]);
    }

    public static function getRecent(int $limit = 5): array
    {
        return Database::fetchAll(
            "SELECT o.*, u.first_name, u.last_name 
             FROM orders o 
             LEFT JOIN users u ON o.user_id = u.id 
             ORDER BY o.created_at DESC 
             LIMIT ?",
            [$limit]
        );
    }

    public static function getStatusCounts(): array
    {
        $results = Database::fetchAll(
            "SELECT status, COUNT(*) as count FROM orders GROUP BY status"
        );
        
        $counts = [];
        foreach ($results as $row) {
            $counts[$row['status']] = (int)$row['count'];
        }
        
        return $counts;
    }
}
