// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    
    toast.classList.remove('bg-success', 'bg-danger');
    toast.classList.add(type === 'success' ? 'bg-success' : 'bg-danger');
    toastMessage.textContent = message;
    
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}

// Add User Form Handler
document.getElementById('addUserForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const formData = new FormData(form);
    
    // Validate inputs
    const username = formData.get('username').trim();
    const email = formData.get('email').trim();
    const password = formData.get('password');

    // Client-side validation
    if (!username || !email || !password) {
        showToast('All fields are required', 'danger');
        return;
    }

    if (!isValidEmail(email)) {
        showToast('Invalid email format', 'danger');
        return;
    }

    // Disable submit button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';
    
    fetch('add_user.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showToast('User added successfully');
            form.reset();
            const modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
            modal.hide();
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Error adding user', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error adding user', 'danger');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Add User';
    });
});

// Add this helper function for email validation
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Edit User Handlers
document.querySelectorAll('.edit-user').forEach(btn => {
    btn.addEventListener('click', function() {
        const userId = this.dataset.id;
        
        fetch(`edit_user.php?id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('edit_user_id').value = data.user.id;
                    document.getElementById('edit_username').value = data.user.username;
                    document.getElementById('edit_email').value = data.user.email;
                    document.getElementById('edit_password').value = '';
                    document.getElementById('edit_role').value = data.user.role;
                    
                    const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
                    modal.show();
                } else {
                    showToast(data.message, 'danger');
                }
            })
            .catch(error => showToast('Error loading user data', 'danger'));
    });
});

// Delete User Handler
document.querySelectorAll('.delete-user').forEach(btn => {
    btn.addEventListener('click', function() {
        const userId = this.dataset.id;
        const row = this.closest('tr');
        const username = row.querySelector('td:first-child').textContent.trim();

        if (confirm(`Are you sure you want to delete user "${username}"?`)) {
            // Show loading state
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            fetch('delete_user.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ id: userId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Animate row removal
                    row.style.backgroundColor = '#ffe6e6';
                    row.style.transition = 'all 0.3s';
                    setTimeout(() => {
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.remove();
                            showToast('User deleted successfully', 'success');
                        }, 300);
                    }, 300);
                } else {
                    showToast(data.message || 'Error deleting user', 'danger');
                    // Reset button state
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-trash"></i>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error deleting user', 'danger');
                // Reset button state
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-trash"></i>';
            });
        }
    });
});

let userIdToDelete = null;

function deleteUser(userId) {
    userIdToDelete = userId;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
    deleteModal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('confirmDeleteUser').addEventListener('click', function() {
        if (!userIdToDelete) return;

        fetch('delete_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'user_id=' + userIdToDelete,
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteUserModal'));
            modal.hide();

            if (data.success) {
                const userRow = document.querySelector(`tr[data-user-id="${userIdToDelete}"]`);
                if (userRow) {
                    userRow.remove();
                }
                alert('User deleted successfully');
            } else {
                alert(data.message || 'Failed to delete user');
            }
            userIdToDelete = null;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to delete user. Please try again.');
            userIdToDelete = null;
        });
    });
});