<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$dataDir = __DIR__ . '/data';
$stateFile = $dataDir . '/signage-state.json';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!is_file($stateFile)) {
        echo '{}';
        exit;
    }
    readfile($stateFile);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}

$raw = file_get_contents('php://input');
$decoded = json_decode($raw ?: '', true);
if (!is_array($decoded)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'Invalid JSON']);
    exit;
}

if (!is_dir($dataDir) && !mkdir($dataDir, 0755, true)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Failed to create data directory']);
    exit;
}

$json = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
if ($json === false || file_put_contents($stateFile, $json, LOCK_EX) === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Failed to write state']);
    exit;
}

echo json_encode(['ok' => true]);
