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
        // Validate input
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $role = isset($_POST['role']) ? trim($_POST['role']) : 'user';

        // Basic validation
        if (empty($username) || empty($email) || empty($password)) {
            throw new Exception('All fields are required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }

        // Check for existing user with debug logging
        error_log("Checking for existing username: " . $username);
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE LOWER(username) = LOWER(?)");
        $stmt->execute([strtolower($username)]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            throw new Exception('Username already exists');
        }

        // Check for existing email with debug logging
        error_log("Checking for existing email: " . $email);
        
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE LOWER(email) = LOWER(?)");
        $stmt->execute([strtolower($email)]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            throw new Exception('Email already exists');
        }

        // Insert new user
        $hashedPassword = password_hash(
            $password,
            PASSWORD_DEFAULT,
            ['cost' => 10]
        );

        // Verify the hash can be checked immediately
        if (!password_verify($password, $hashedPassword)) {
            throw new Exception('Password hashing failed');
        }

        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, role, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");

        if (!$stmt->execute([$username, $email, $hashedPassword, $role])) {
            throw new Exception('Failed to create user');
        }

        // Verify the user was created
        $newUserId = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$newUserId]);
        $newUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$newUser || !password_verify($password, $newUser['password'])) {
            throw new Exception('User creation verification failed');
        }

        error_log("User created successfully: " . $username);
        error_log("Password hash verification successful");

        echo json_encode([
            'success' => true,
            'message' => 'User added successfully'
        ]);

    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Database error occurred'
        ]);
    } catch (Exception $e) {
        error_log("Add user error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}