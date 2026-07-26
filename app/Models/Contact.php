<?php

declare(strict_types=1);

namespace App\Models;

class Contact
{
    public static function create(array $data): int
    {
        $data['ip_address'] = get_client_ip();
        return Database::insert('contacts', $data);
    }

    public static function update(int $id, array $data): int
    {
        return Database::update('contacts', $data, 'id = ?', [$id]);
    }

    public static function delete(int $id): int
    {
        return Database::delete('contacts', 'id = ?', [$id]);
    }

    public static function getById(int $id): ?array
    {
        return Database::fetch("SELECT * FROM contacts WHERE id = ?", [$id]);
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
            $where[] = '(name LIKE ? OR email LIKE ? OR subject LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }
        
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM contacts WHERE " . implode(' AND ', $where) . " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $contacts = Database::fetchAll($sql, $params);
        
        $countSql = "SELECT COUNT(*) as count FROM contacts WHERE " . implode(' AND ', $where);
        $total = (int)Database::fetch($countSql, array_slice($params, 0, -2))['count'];
        
        return [
            'items' => $contacts,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int)ceil($total / $perPage),
        ];
    }

    public static function markAsRead(int $id): int
    {
        return Database::update('contacts', ['status' => 'read'], 'id = ?', [$id]);
    }

    public static function getStats(): array
    {
        return [
            'total' => Database::count('contacts'),
            'new' => Database::count('contacts', 'status = ?', ['new']),
            'read' => Database::count('contacts', 'status = ?', ['read']),
            'replied' => Database::count('contacts', 'status = ?', ['replied']),
        ];
    }
}
