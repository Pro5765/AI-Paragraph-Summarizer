<!-- Add this where you want the delete account button to appear -->
<div class="card mt-4">
    <div class="card-header bg-danger text-white">
        Danger Zone
    </div>
    <div class="card-body">
        <h5 class="card-title">Delete Account</h5>
        <p class="card-text">Once you delete your account, there is no going back. Please be certain.</p>
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
            Delete My Account
        </button>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    Warning: This action cannot be undone! All your data will be permanently deleted.
                </div>
                <form id="deleteAccountForm">
                    <div class="mb-3">
                        <label for="deletePassword" class="form-label">Enter your password to confirm:</label>
                        <input type="password" class="form-control" id="deletePassword" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteAccount">Delete My Account</button>
            </div>
        </div>
    </div>
</div>

<!-- Add this before closing </body> tag -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/account.js"></script>