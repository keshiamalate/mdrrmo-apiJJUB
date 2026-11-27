<?php
/**
 * Database connection (PostgreSQL / PostGIS).
 * NEVER put passwords in this file. Set them as environment variables:
 *   DATABASE_URL = postgresql://user:password@host:5432/dbname   (Render gives you this)
 * or the separate DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASSWORD variables.
 * Optional: DB_SSLMODE (default "prefer"; use "require" for Render's external URL).
 */

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $url = getenv('DATABASE_URL');
    if ($url) {
        $p    = parse_url($url);
        $host = $p['host'] ?? '';
        $port = $p['port'] ?? 5432;
        $name = ltrim($p['path'] ?? '', '/');
        $user = isset($p['user']) ? urldecode($p['user']) : '';
        $pass = isset($p['pass']) ? urldecode($p['pass']) : '';
    } else {
        $host = getenv('DB_HOST') ?: '';
        $port = getenv('DB_PORT') ?: 5432;
        $name = getenv('DB_NAME') ?: '';
        $user = getenv('DB_USER') ?: '';
        $pass = getenv('DB_PASSWORD') ?: '';
    }

    if ($host === '' || $name === '' || $user === '') {
        throw new RuntimeException('Database settings are missing. Set DATABASE_URL in the environment.');
    }

    $ssl = getenv('DB_SSLMODE') ?: 'prefer';
    $dsn = "pgsql:host=$host;port=$port;dbname=$name;sslmode=$ssl";

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}
