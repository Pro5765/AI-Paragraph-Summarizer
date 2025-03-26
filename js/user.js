function deleteAccount() {
    const password = document.getElementById('confirmPassword').value;
    
    if (!password) {
        alert('Please enter your password');
        return;
    }

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
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the account');
    });
}