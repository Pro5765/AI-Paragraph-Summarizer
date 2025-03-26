<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="section">
        <h2>Account Management</h2>
        <div class="card border-danger">
            <div class="card-body">
                <h5 class="text-danger">Delete Account</h5>
                <p>Warning: This action is permanent and cannot be undone.</p>
                <form id="deleteAccountForm" onsubmit="return confirmDelete(event)">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash-alt me-2"></i>Delete Account
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function confirmDelete(event) {
        event.preventDefault();
        
        const confirmed = confirm("Are you sure you want to delete your account? This cannot be undone.");
        if (!confirmed) return false;

        const password = prompt("Please enter your password to confirm:");
        if (!password) return false;

        const formData = new FormData();
        formData.append('password', password);
        
        fetch('delete_account.php', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin' // Important for session handling
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Account deleted successfully');
                window.location.href = 'logout.php';
            } else {
                alert(data.message || 'Failed to delete account');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });

        return false;
    }
    </script>
    
    <!-- Add Bootstrap and other required scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-kit-code.js"></script>
</body>
</html>