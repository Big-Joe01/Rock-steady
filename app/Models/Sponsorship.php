<?php

declare(strict_types=1);

namespace App\Models;

class Sponsorship
{
    public static function create(array $data): int
    {
        if (isset($data['platforms']) && is_array($data['platforms'])) {
            $data['platforms'] = json_encode($data['platforms']);
        }
        if (isset($data['social_links']) && is_array($data['social_links'])) {
            $data['social_links'] = json_encode($data['social_links']);
        }
        if (isset($data['attachments']) && is_array($data['attachments'])) {
            $data['attachments'] = json_encode($data['attachments']);
        }
        
        return Database::insert('sponsorships', $data);
    }

    public static function update(int $id, array $data): int
    {
        if (isset($data['platforms']) && is_array($data['platforms'])) {
            $data['platforms'] = json_encode($data['platforms']);
        }
        if (isset($data['social_links']) && is_array($data['social_links'])) {
            $data['social_links'] = json_encode($data['social_links']);
        }
        if (isset($data['attachments']) && is_array($data['attachments'])) {
            $data['attachments'] = json_encode($data['attachments']);
        }
        
        return Database::update('sponsorships', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): int
    {
        return Database::delete('sponsorships', 'id = ?', [$id]);
    }

    public static function getById(int $id): ?array
    {
        $sponsorship = Database::fetch("SELECT * FROM sponsorships WHERE id = ?", [$id]);
        
        if ($sponsorship) {
            $sponsorship['platforms'] = json_decode($sponsorship['platforms'] ?? '[]', true);
            $sponsorship['social_links'] = json_decode($sponsorship['social_links'] ?? '[]', true);
            $sponsorship['attachments'] = json_decode($sponsorship['attachments'] ?? '[]', true);
        }
        
        return $sponsorship;
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
            $where[] = '(name LIKE ? OR brand LIKE ? OR email LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }
        
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM sponsorships WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $items = Database::fetchAll($sql, $params);
        
        foreach ($items as &$item) {
            $item['platforms'] = json_decode($item['platforms'] ?? '[]', true);
            $item['social_links'] = json_decode($item['social_links'] ?? '[]', true);
        }
        
        $countSql = "SELECT COUNT(*) as count FROM sponsorships WHERE " . implode(' AND ', $where);
        $total = (int)Database::fetch($countSql, array_slice($params, 0, -2))['count'];
        
        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    public static function updateStatus(int $id, string $status, string $notes = ''): int
    {
        return Database::update('sponsorships', [
            'status' => $status,
            'notes' => $notes,
        ], 'id = ?', [$id]);
    }

    public static function getStats(): array
    {
        return [
            'total' => Database::count('sponsorships'),
            'pending' => Database::count('sponsorships', 'status = ?', ['pending']),
            'reviewed' => Database::count('sponsorships', 'status = ?', ['reviewed']),
            'approved' => Database::count('sponsorships', 'status = ?', ['approved']),
            'rejected' => Database::count('sponsorships', 'status = ?', ['rejected']),
        ];
    }
}
