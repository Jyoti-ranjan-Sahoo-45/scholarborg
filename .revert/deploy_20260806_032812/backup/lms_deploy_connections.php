<?php
/**
 * Scholarb Chat Connection System - Deployment Script
 * Run once from: https://scholarb.org/lms_deploy_connections.php
 * Then DELETE this file immediately after running.
 */

set_time_limit(120);
error_reporting(E_ALL);
ini_set('display_errors', 1);

$lmsRoot = dirname(__FILE__);
$results = [];

function runStep(string $label, callable $fn) use (&$results) {
    try {
        $msg = $fn();
        $results[] = ['ok' => true, 'label' => $label, 'msg' => $msg ?? 'OK'];
    } catch (Throwable $e) {
        $results[] = ['ok' => false, 'label' => $label, 'msg' => $e->getMessage()];
    }
}

// ── Step 1: Create chat_connections table ────────────────────────────────────
runStep('Create chat_connections table', function () use ($lmsRoot) {
    $env = parse_ini_file($lmsRoot . '/.env');
    $pdo = new PDO(
        "mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4",
        $env['DB_USERNAME'],
        $env['DB_PASSWORD'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Check if table already exists
    $rows = $pdo->query("SHOW TABLES LIKE 'chat_connections'")->fetchAll();
    if ($rows) {
        return 'Table already exists — skipped.';
    }

    $pdo->exec("
        CREATE TABLE `chat_connections` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `sender_id` int(10) unsigned NOT NULL,
            `receiver_id` int(10) unsigned NOT NULL,
            `status` enum('pending','accepted','declined','blocked') NOT NULL DEFAULT 'pending',
            `created_at` bigint(20) NOT NULL,
            `updated_at` bigint(20) DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `chat_connections_sender_receiver_unique` (`sender_id`,`receiver_id`),
            CONSTRAINT `cc_sender_fk` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
            CONSTRAINT `cc_receiver_fk` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    return 'Table created successfully.';
});

// ── Step 2: Add source_id nullable to public_chat_conversations ──────────────
runStep('Ensure source_id column is nullable in public_chat_conversations', function () use ($lmsRoot) {
    $env = parse_ini_file($lmsRoot . '/.env');
    $pdo = new PDO(
        "mysql:host={$env['DB_HOST']};dbname={$env['DB_DATABASE']};charset=utf8mb4",
        $env['DB_USERNAME'],
        $env['DB_PASSWORD'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Check if table exists
    $rows = $pdo->query("SHOW TABLES LIKE 'public_chat_conversations'")->fetchAll();
    if (!$rows) return 'Table does not exist yet — skipped.';

    // Modify source_id to allow NULL and add 'direct' as a source_type option
    $pdo->exec("ALTER TABLE `public_chat_conversations` MODIFY `source_id` int(10) unsigned NULL DEFAULT 0");

    return 'source_id column updated to nullable.';
});

// ── Step 3: Clear Laravel config and route caches ───────────────────────────
runStep('Clear Laravel caches via artisan', function () use ($lmsRoot) {
    $artisan = $lmsRoot . '/artisan';
    if (!file_exists($artisan)) return 'artisan not found — skipped.';

    $out = [];
    exec("php {$artisan} config:clear 2>&1", $out);
    exec("php {$artisan} route:clear 2>&1", $out);
    exec("php {$artisan} view:clear 2>&1", $out);
    exec("php {$artisan} cache:clear 2>&1", $out);

    return implode(' | ', array_filter($out));
});

// ── Render Results ──────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html>
<head><title>Connections Deployment</title>
<style>
  body { font-family: monospace; background: #0d1117; color: #e6edf3; padding: 32px; }
  h1 { color: #58a6ff; }
  .ok { color: #3fb950; }
  .fail { color: #f85149; }
  .row { margin: 8px 0; padding: 8px 16px; border-radius: 6px; background: #161b22; }
</style>
</head>
<body>
<h1>🚀 Connections System Deployment</h1>
<p style="color:#8b949e;">Run at: <?= date('Y-m-d H:i:s') ?></p>
<?php foreach ($results as $r): ?>
<div class="row <?= $r['ok'] ? 'ok' : 'fail' ?>">
    <?= $r['ok'] ? '✅' : '❌' ?> <strong><?= htmlspecialchars($r['label']) ?></strong>:
    <?= htmlspecialchars($r['msg']) ?>
</div>
<?php endforeach; ?>
<hr style="border-color:#30363d;margin-top:24px;">
<p class="fail"><strong>⚠️ DELETE THIS FILE NOW!</strong> Do not leave it on the server.</p>
</body>
</html>
