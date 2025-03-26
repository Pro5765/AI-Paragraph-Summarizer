<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        // Add debug logging
        error_log("Login attempt for email: " . $email);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(email) = LOWER(?)");
        $stmt->execute([strtolower($email)]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug password verification
        if ($user) {
            error_log("User found, verifying password");
            $passwordValid = password_verify($password, $user['password']);
            error_log("Password valid: " . ($passwordValid ? 'true' : 'false'));
        }

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Always redirect to index.php first
            header('Location: index.php');
            exit;
        } else {
            $error = "Invalid email or password";
        }
    } catch (PDOException $e) {
        $error = "An error occurred. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AI Text Summarizer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        }
        
        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .login-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 30px -15px rgba(0, 0, 0, 0.15);
        }
        
        .card-header {
            background: var(--gradient);
            color: white;
            border-radius: 1rem 1rem 0 0 !important;
            padding: 1.5rem;
        }

        .admin-note {
            font-size: 0.875rem;
            color: #6c757d;
            text-align: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card login-card animate__animated animate__fadeIn">
                    <div class="card-header text-center">
                        <h4 class="mb-0">Login</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                            <p class="text-center mb-0">
                                Don't have an account? <a href="signup.php">Sign Up</a>
                            </p>
                            <div class="admin-note">
                                <i class="fas fa-info-circle me-1"></i>
                                Admin users will be redirected to the admin panel automatically.
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>