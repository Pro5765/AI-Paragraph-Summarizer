<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text Summarizer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand img { height: 40px; }
        .footer { background-color: #343a40; color: white; padding: 2rem 0; }
        .main-content { min-height: calc(100vh - 160px); }
        .nav-link { color: rgba(255,255,255,.85) !important; }
        .nav-link:hover { color: white !important; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="logo.png" alt="Text Summarizer Logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#summarize">Summarize</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#history">History</a>
                    </li>
                </ul>
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($user['username']); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#profile"><i class="fas fa-user"></i> Profile</a></li>
                        <li><a class="dropdown-item" href="#settings"><i class="fas fa-cog"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content container py-4">
        <!-- Dashboard Section -->
        <div id="dashboard" class="section">
            <h2>Dashboard</h2>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h5>Total Summaries</h5>
                            <h2 class="text-primary">0</h2>
                        </div>
                    </div>
                </div>
                <!-- Add more dashboard cards here -->
            </div>
        </div>

        <!-- Profile Section -->
        <div id="profile" class="section">
            <div class="container py-4">
                <div class="row">
                    <!-- User Info Card -->
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Profile Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <i class="fas fa-user-circle fa-5x text-primary mb-3"></i>
                                    <h4><?php echo htmlspecialchars($user['username']); ?></h4>
                                    <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                                </div>
                                <form id="updateProfileForm">
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" 
                                               value="<?php echo htmlspecialchars($user['username']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" 
                                               value="<?php echo htmlspecialchars($user['email']); ?>">
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Update Profile</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Summaries Card -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Your Summaries</h5>
                                <span class="badge bg-light text-primary" id="summaryCount">
                                    <?php
                                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM summaries WHERE user_id = ?");
                                    $stmt->execute([$_SESSION['user_id']]);
                                    echo $stmt->fetchColumn();
                                    ?> Total
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Original Length</th>
                                                <th>Summary Length</th>
                                                <th>Reduction</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $stmt = $pdo->prepare("
                                                SELECT * FROM summaries 
                                                WHERE user_id = ? 
                                                ORDER BY created_at DESC 
                                                LIMIT 10
                                            ");
                                            $stmt->execute([$_SESSION['user_id']]);
                                            while ($summary = $stmt->fetch()): ?>
                                            <tr>
                                                <td><?php echo date('M d, Y', strtotime($summary['created_at'])); ?></td>
                                                <td><?php echo $summary['characters_count']; ?> chars</td>
                                                <td><?php echo strlen($summary['summary_text']); ?> chars</td>
                                                <td><?php echo $summary['reduction_percentage']; ?>%</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" 
                                                            onclick="viewSummary(<?php echo $summary['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" 
                                                            onclick="deleteSummary(<?php echo $summary['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Section -->
        <div id="settings" class="section" style="display:none;">
            <h2>Account Settings</h2>
            <div class="card mb-4">
                <div class="card-body">
                    <h5>Change Password</h5>
                    <form id="changePasswordForm">
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="currentPassword" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" id="newPassword" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Change Password</button>
                    </form>
                </div>
            </div>

            <div class="card border-danger">
                <div class="card-body">
                    <h5 class="text-danger">Delete Account</h5>
                    <p>Warning: This action is permanent and cannot be undone.</p>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                        <i class="fas fa-trash-alt me-2"></i>Delete Account
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary View Modal -->
    <div class="modal fade" id="summaryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Summary Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <h6>Original Text:</h6>
                        <p id="modalOriginalText" class="border rounded p-3"></p>
                    </div>
                    <div>
                        <h6>Summary:</h6>
                        <p id="modalSummaryText" class="border rounded p-3"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Text Summarizer</h5>
                    <p>Simplify your reading with AI-powered text summarization.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-light">Home</a></li>
                        <li><a href="#" class="text-light">About</a></li>
                        <li><a href="#" class="text-light">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Connect With Us</h5>
                    <div class="social-icons">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
            <hr class="mt-4 mb-3 border-light">
            <div class="text-center">
                <p class="mb-0">&copy; 2024 Text Summarizer. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navigation
        document.querySelectorAll('[href^="#"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                document.querySelectorAll('.section').forEach(section => {
                    section.style.display = 'none';
                });
                document.getElementById(targetId).style.display = 'block';
            });
        });

        // Update Profile
        document.getElementById('updateProfileForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('username', document.getElementById('username').value);
            formData.append('email', document.getElementById('email').value);

            fetch('update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Profile updated successfully');
                } else {
                    alert(data.message || 'Failed to update profile');
                }
            });
        });

        // Change Password
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData();
            formData.append('currentPassword', document.getElementById('currentPassword').value);
            formData.append('newPassword', document.getElementById('newPassword').value);

            fetch('change_password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Password changed successfully');
                    this.reset();
                } else {
                    alert(data.message || 'Failed to change password');
                }
            });
        });

        // Delete Account
        function confirmDelete() {
            if (confirm('Are you sure you want to delete your account? This cannot be undone.')) {
                const password = prompt('Please enter your password to confirm:');
                if (password) {
                    const formData = new FormData();
                    formData.append('password', password);

                    fetch('delete_account.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Account deleted successfully');
                            window.location.href = 'logout.php';
                        } else {
                            alert(data.message || 'Failed to delete account');
                        }
                    });
                }
            }
        }

        // Logout
        function logoutUser() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        }

        // Add this to your existing JavaScript section
        function viewSummary(id) {
            fetch(`get_summary.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('modalOriginalText').textContent = data.original_text;
                        document.getElementById('modalSummaryText').textContent = data.summary_text;
                        new bootstrap.Modal(document.getElementById('summaryModal')).show();
                    } else {
                        alert('Failed to load summary');
                    }
                });
        }

        function deleteSummary(id) {
            if (confirm('Are you sure you want to delete this summary?')) {
                fetch('delete_summary.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to delete summary');
                    }
                });
            }
        }
    </script>
</body>
</html>