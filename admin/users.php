<?php
session_start();
require_once '../config/db.php';

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $_SESSION['error'] = "Unauthorized access. Please login with admin credentials.";
    header('Location: ../login.php');
    exit;
}

try {
    // Fetch all users with their stats
    $stmt = $pdo->query("
        SELECT 
            u.*,
            COUNT(s.id) as summary_count,
            MAX(s.created_at) as last_activity,
            DATE_FORMAT(MAX(s.created_at), '%Y-%m-%dT%H:%i:%s') as last_activity_iso,
            COALESCE(SUM(s.characters_count), 0) as chars_processed
        FROM users u
        LEFT JOIN summaries s ON u.id = s.user_id
        GROUP BY u.id
        ORDER BY u.created_at DESC
    ");
    $users = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Manage Users Error: " . $e->getMessage());
    $error = "Database error occurred. Please try again.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        }
        
        .admin-sidebar {
            background: var(--gradient);
            min-height: 100vh;
            color: white;
        }

        .user-table th {
            background: #f8f9fa;
            white-space: nowrap;
        }

        .action-btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        .role-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        .role-admin {
            background-color: #dc3545;
            color: white;
        }

        .role-user {
            background-color: #198754;
            color: white;
        }

        .stats-badge {
            font-size: 0.75rem;
            background-color: #f8f9fa;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 admin-sidebar p-4">
                <h4 class="mb-4">Admin Panel</h4>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a href="dashboard.php" class="nav-link text-white">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="users.php" class="nav-link text-white active">
                            <i class="fas fa-users me-2"></i>
                            Manage Users
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="../index.php" class="nav-link text-white">
                            <i class="fas fa-home me-2"></i>
                            Back to Site
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../logout.php" class="nav-link text-white">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Manage Users</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="fas fa-plus me-2"></i>Add User
                    </button>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Summaries</th>
                                        <th>Last Activity</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr data-user-id="<?php echo htmlspecialchars($user['id']); ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-circle fa-2x text-muted me-2"></i>
                                                <div>
                                                    <div><?php echo htmlspecialchars($user['username']); ?></div>
                                                    <small class="text-muted">ID: <?php echo $user['id']; ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td>
                                            <span class="role-badge <?php echo $user['role'] === 'admin' ? 'role-admin' : 'role-user'; ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="stats-badge">
                                                <i class="fas fa-file-alt me-1"></i>
                                                <?php echo $user['summary_count']; ?> summaries
                                            </div>
                                        </td>
                                        <td>
                                            <span class="<?php echo $user['last_activity'] ? 'text-success' : 'text-muted'; ?>">
                                                <?php 
                                                    if ($user['last_activity']) {
                                                        echo date('M j, Y g:i A', strtotime($user['last_activity']));
                                                    } else {
                                                        echo 'Never';
                                                    }
                                                ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-primary action-btn edit-user" data-id="<?php echo $user['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                                <button type="button" 
                                                        onclick="deleteUser(<?php echo htmlspecialchars($user['id']); ?>)" 
                                                        class="btn btn-sm btn-danger action-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'modals/user_modals.php'; // Include modals ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/users.js"></script> <!-- Include JavaScript for user management -->
</body>
</html>