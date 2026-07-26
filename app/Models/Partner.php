<?php

declare(strict_types=1);

namespace App\Models;

class Partner
{
    public static function create(array $data): int
    {
        return Database::insert('partners', $data);
    }

    public static function update(int $id, array $data): int
    {
        return Database::update('partners', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): int
    {
        return Database::delete('partners', 'id = ?', [$id]);
    }

    public static function getById(int $id): ?array
    {
        return Database::fetch("SELECT * FROM partners WHERE id = ?", [$id]);
    }

    public static function getAll(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where = ['1=1'];
        $params = [];
        
        if (!empty($filters['status'])) {
            $where[] = 'status = ?';
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = '(company_name LIKE ? OR brand_name LIKE ? OR email LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }
        
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM partners WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $partners = Database::fetchAll($sql, $params);
        
        $countSql = "SELECT COUNT(*) as count FROM partners WHERE " . implode(' AND ', $where);
        $total = (int)Database::fetch($countSql, array_slice($params, 0, -2))['count'];
        
        return [
            'items' => $partners,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    public static function getFeatured(): array
    {
        return Database::fetchAll(
            "SELECT * FROM partners WHERE featured = 1 AND status = 'approved' ORDER BY created_at DESC"
        );
    }

    public static function getApproved(): array
    {
        return Database::fetchAll(
            "SELECT * FROM partners WHERE status = 'approved' ORDER BY featured DESC, created_at DESC"
        );
    }

    public static function updateStatus(int $id, string $status): int
    {
        return Database::update('partners', ['status' => $status], 'id = ?', [$id]);
    }

    public static function getStats(): array
    {
        return [
            'total' => Database::count('partners'),
            'pending' => Database::count('partners', 'status = ?', ['pending']),
            'approved' => Database::count('partners', 'status = ?', ['approved']),
            'rejected' => Database::count('partners', 'status = ?', ['rejected']),
        ];
    }
}
