<?php

declare(strict_types=1);

namespace App\Models;

class Settings
{
    private static array $cache = [];

    public static function get(string $key, mixed $default = null): mixed
    {
        if (empty(self::$cache)) {
            self::loadAll();
        }
        
        return self::$cache[$key]['value'] ?? $default;
    }

    public static function set(string $key, mixed $value, string $type = 'text'): bool
    {
        $existing = Database::fetch("SELECT id FROM settings WHERE `key` = ?", [$key]);
        
        if ($existing) {
            Database::update('settings', [
                'value' => $value,
                'type' => $type,
            ], '`key` = ?', [$key]);
        } else {
            Database::insert('settings', [
                'key' => $key,
                'value' => $value,
                'type' => $type,
            ]);
        }
        
        self::$cache[$key] = ['value' => $value, 'type' => $type];
        
        return true;
    }

    public static function delete(string $key): int
    {
        unset(self::$cache[$key]);
        return Database::delete('settings', '`key` = ?', [$key]);
    }

    public static function getAll(): array
    {
        if (empty(self::$cache)) {
            self::loadAll();
        }
        
        return self::$cache;
    }

    public static function getByGroup(string $group): array
    {
        $settings = Database::fetchAll(
            "SELECT * FROM settings WHERE `group` = ? ORDER BY `key` ASC",
            [$group]
        );
        
        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['key']] = $setting['value'];
        }
        
        return $result;
    }

    private static function loadAll(): void
    {
        $settings = Database::fetchAll("SELECT * FROM settings");
        
        self::$cache = [];
        foreach ($settings as $setting) {
            self::$cache[$setting['key']] = [
                'value' => $setting['value'],
                'type' => $setting['type'],
                'group' => $setting['group'],
            ];
        }
    }

    public static function clearCache(): void
    {
        self::$cache = [];
    }
}
