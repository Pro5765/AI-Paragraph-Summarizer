<?php
session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

try {
    $summaryId = $_POST['id'] ?? null;
    if (!$summaryId) {
        throw new Exception('No summary ID provided');
    }

    $stmt = $pdo->prepare("DELETE FROM summaries WHERE id = ? AND user_id = ?");
    $stmt->execute([$summaryId, $_SESSION['user_id']]);

    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}