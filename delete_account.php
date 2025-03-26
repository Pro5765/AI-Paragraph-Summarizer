<?php
session_start();
require_once 'config/db.php';

header('Content-Type: application/json');

// Enhanced error logging
ini_set('display_errors', 1);
error_reporting(E_ALL);
error_log("Delete account request received for user ID: " . ($_SESSION['user_id'] ?? 'none'));

if (!isset($_SESSION['user_id'])) {
    error_log("Delete account failed: No user session");
    echo json_encode(['success' => false, 'message' => 'Please login to continue']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['password'])) {
    error_log("Delete account failed: Invalid request or missing password");
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

try {
    $userId = $_SESSION['user_id'];
    $password = $_POST['password'];
    
    // Verify password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        error_log("Delete account failed: Invalid password for user ID: $userId");
        throw new Exception('Incorrect password');
    }

    // Start transaction
    $pdo->beginTransaction();

    // Delete user's summaries
    $stmt = $pdo->prepare("DELETE FROM summaries WHERE user_id = ?");
    $stmt->execute([$userId]);

    // Delete user account
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$userId]);

    $pdo->commit();
    
    error_log("Account successfully deleted for user ID: $userId");
    session_destroy();

    echo json_encode([
        'success' => true,
        'message' => 'Account deleted successfully'
    ]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log("Delete account error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>