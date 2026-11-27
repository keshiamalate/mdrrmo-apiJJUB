<?php
/**
 * Connection test. Not inside public/, so the web server never serves it.
 * Run it from the Render Shell tab:   php test-db.php
 */
require_once __DIR__ . '/src/db_connection.php';

try {
    $v = db()->query('SELECT version()')->fetchColumn();
    echo "Connected to PostgreSQL: $v\n";
    $g = db()->query('SELECT PostGIS_Version()')->fetchColumn();
    echo "PostGIS: $g\n";
    $t = db()->query("SELECT to_regclass('public.incidents')")->fetchColumn();
    echo $t ? "Table 'incidents' exists.\n" : "Table 'incidents' is missing. Run schema.sql first.\n";
} catch (Throwable $e) {
    echo 'Connection failed: ' . $e->getMessage() . "\n";
    exit(1);
}
