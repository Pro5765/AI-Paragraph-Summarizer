<?php
session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

try {
    $summaryId = $_GET['id'] ?? null;
    if (!$summaryId) {
        throw new Exception('No summary ID provided');
    }

    $stmt = $pdo->prepare("
        SELECT original_text, summary_text 
        FROM summaries 
        WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$summaryId, $_SESSION['user_id']]);
    $summary = $stmt->fetch();

    if (!$summary) {
        throw new Exception('Summary not found');
    }

    echo json_encode([
        'success' => true,
        'original_text' => $summary['original_text'],
        'summary_text' => $summary['summary_text']
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}