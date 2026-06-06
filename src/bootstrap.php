<?php
declare(strict_types=1);

// ─── Simple PSR-4-ish autoloader ───────────────────────────────────────
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) return;
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) require $file;
});

// ─── Database ──────────────────────────────────────────────────────────
require __DIR__ . '/Database.php';
App\Database::init(__DIR__ . '/../database/portfolio.sqlite');

// ─── Run migrations & seed on first boot ───────────────────────────────
require __DIR__ . '/../database/migrate.php';

// ─── Global helpers ────────────────────────────────────────────────────
require __DIR__ . '/helpers.php';
