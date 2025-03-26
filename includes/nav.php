<style>
    .navbar {
        background-color: #fff !important;
        padding: 1rem 0;
        box-shadow: 0 2px 10px rgba(0,0,0,.08);
    }
    .navbar-brand {
        color: #2d3748 !important;
        font-weight: 700;
    }
    .navbar-brand img {
        height: 35px;
        transition: transform 0.3s ease;
    }
    .navbar-brand:hover img {
        transform: scale(1.05);
    }
    .nav-link {
        color: #4a5568 !important;
        font-weight: 500;
        position: relative;
        padding: 0.5rem 1rem !important;
        transition: color 0.2s ease;
    }
    .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: 0;
        left: 50%;
        background-color: #fff;
        transition: all 0.3s ease;
    }
    .nav-link:hover {
        color: #3182ce !important;
    }
    .nav-link:hover::after {
        width: 100%;
        left: 0;
    }
    .navbar .btn {
        padding: 0.5rem 1.25rem;
        font-weight: 500;
    }
    .btn-primary {
        background: #3182ce;
        border: none;
    }
    .btn-outline-primary {
        color: #3182ce;
        border-color: #3182ce;
    }
    .dropdown-menu {
        border: none;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06);
    }
    .dropdown-item {
        color: #4a5568;
        padding: 0.7rem 1.5rem;
        transition: all 0.2s ease;
    }
    .dropdown-item:hover {
        background-color: #ebf8ff;
        color: #3182ce;
        padding-left: 1.8rem;
    }
    .btn-outline-light {
        border-width: 2px;
        transition: all 0.3s ease;
    }
    .btn-outline-light:hover {
        transform: translateY(-1px);
    }
</style>

<nav class="navbar navbar-expand-lg navbar-light">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="assets/img/logo.png" alt="Text Summarizer Logo" class="me-2">
            <span>Text Summarizer</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i>Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="about.php"><i class="fas fa-info-circle me-1"></i>About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="contact.php"><i class="fas fa-envelope me-1"></i>Contact</a>
                </li>
            </ul>
            <div class="nav-buttons d-flex align-items-center">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>
                            <span><?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item" href="user.php"><i class="fas fa-user me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="summaries.php"><i class="fas fa-file-alt me-2"></i>My Summaries</a></li>
                            <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-primary me-2">
                        <i class="fas fa-sign-in-alt me-1"></i>Login
                    </a>
                    <a href="register.php" class="btn btn-primary">
                        <i class="fas fa-user-plus me-1"></i>Register
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>