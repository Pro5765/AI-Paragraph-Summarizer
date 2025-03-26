<?php
session_start();
require_once '../config/db.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
        
        if (!$userId) {
            throw new Exception('Invalid user ID');
        }

        // Prevent admin from deleting themselves
        if ($userId === $_SESSION['user_id']) {
            throw new Exception('Cannot delete your own account');
        }

        // Start transaction
        $pdo->beginTransaction();

        // Delete user's summaries first
        $stmt = $pdo->prepare("DELETE FROM summaries WHERE user_id = ?");
        $stmt->execute([$userId]);

        // Delete the user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);

        if ($stmt->rowCount() === 0) {
            throw new Exception('User not found');
        }

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);

    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log("Delete user error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}