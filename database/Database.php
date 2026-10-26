<?php
declare(strict_types=1);

const DB_FILE = __DIR__ . '/reservations.sqlite';

function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    if (!extension_loaded('pdo_sqlite')) { http_response_code(500); exit('PHP PDO SQLite is required. Enable pdo_sqlite in cPanel.'); }
    if (!is_dir(__DIR__)) mkdir(__DIR__, 0750, true);
    $pdo = new PDO('sqlite:' . DB_FILE, null, null, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    $pdo->exec('PRAGMA foreign_keys=ON');
    $pdo->exec('PRAGMA journal_mode=WAL');
    $pdo->exec('PRAGMA busy_timeout=5000');
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    if ($schema === false) throw new RuntimeException('Database schema file is missing.');
    $pdo->exec($schema);
    return $pdo;
}
