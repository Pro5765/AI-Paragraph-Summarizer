document.addEventListener('DOMContentLoaded', function() {
    const deleteAccountBtn = document.getElementById('confirmDeleteAccount');
    const deleteAccountForm = document.getElementById('deleteAccountForm');
    const deletePassword = document.getElementById('deletePassword');

    if (deleteAccountBtn) {
        deleteAccountBtn.addEventListener('click', function() {
            if (!deletePassword.value) {
                alert('Please enter your password');
                return;
            }

            if (confirm('Are you absolutely sure you want to delete your account?')) {
                const formData = new FormData();
                formData.append('password', deletePassword.value);

                fetch('delete_account.php', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
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
            }
        });
    }
});