<?php

declare(strict_types=1);

namespace App\Services;

class Logger
{
    private static string $logFile;
    private static array $logs = [];

    public static function init(): void
    {
        self::$logFile = STORAGE_PATH . '/logs/' . date('Y-m-d') . '.log';
        
        if (!is_dir(dirname(self::$logFile))) {
            mkdir(dirname(self::$logFile), 0755, true);
        }
    }

    public static function info(string $message, array $context = []): void
    {
        self::log('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log('ERROR', $message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        if (APP_DEBUG) {
            self::log('DEBUG', $message, $context);
        }
    }

    public static function critical(string $message, array $context = []): void
    {
        self::log('CRITICAL', $message, $context);
    }

    private static function log(string $level, string $message, array $context = []): void
    {
        self::init();
        
        $timestamp = date('Y-m-d H:i:s');
        $contextString = empty($context) ? '' : ' ' . json_encode($context);
        $logEntry = "[{$timestamp}] [{$level}] {$message}{$contextString}" . PHP_EOL;
        
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND);
        
        self::$logs[] = [
            'timestamp' => $timestamp,
            'level' => $level,
            'message' => $message,
            'context' => $context,
        ];
    }

    public static function getLogs(): array
    {
        return self::$logs;
    }

    public static function clear(): void
    {
        self::$logs = [];
    }
}
