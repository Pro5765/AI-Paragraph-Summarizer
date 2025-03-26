<style>
    .footer {
        background-color: #fff;
        color: #4a5568;
        padding: 4rem 0 2rem;
        border-top: 1px solid #e2e8f0;
    }
    .footer h5 {
        color: #2d3748;
        font-weight: 600;
        margin-bottom: 1.25rem;
        font-size: 1.1rem;
    }
    .footer-link {
        color: #718096;
        text-decoration: none;
        display: block;
        margin-bottom: 0.75rem;
        transition: color 0.2s ease;
    }
    .footer-link:hover {
        color: #3182ce;
    }
    .social-links a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #f7fafc;
        color: #4a5568;
        margin-right: 0.75rem;
        transition: all 0.2s ease;
    }
    .social-links a:hover {
        background: #3182ce;
        color: white;
        transform: translateY(-2px);
    }
    .newsletter-form .form-control {
        border: 1px solid #e2e8f0;
        padding: 0.75rem;
        border-radius: 8px 0 0 8px;
    }
    .newsletter-form .btn {
        border-radius: 0 8px 8px 0;
        padding: 0.75rem 1.5rem;
    }
    .copyright {
        padding-top: 2rem;
        margin-top: 2rem;
        border-top: 1px solid #e2e8f0;
        color: #718096;
    }
</style>

<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <h5>Text Summarizer</h5>
                <p class="text-muted mb-4">Transform your reading experience with our AI-powered text summarization tool.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#"><i class="fab fa-github"></i></a>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 mb-4">
                <h5>Product</h5>
                <a href="features.php" class="footer-link">Features</a>
                <a href="how-it-works.php" class="footer-link">How it Works</a>
                <a href="pricing.php" class="footer-link">Pricing</a>
            </div>
            <div class="col-lg-2 col-md-3 mb-4">
                <h5>Company</h5>
                <a href="about.php" class="footer-link">About Us</a>
                <a href="contact.php" class="footer-link">Contact</a>
                <a href="careers.php" class="footer-link">Careers</a>
            </div>
            <div class="col-lg-4 mb-4">
                <h5>Stay Updated</h5>
                <p class="text-muted mb-3">Get notified about the latest features and updates.</p>
                <form class="newsletter-form">
                    <div class="input-group">
                        <input type="email" class="form-control" placeholder="Enter your email">
                        <button class="btn btn-primary">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="copyright text-center">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> Text Summarizer. All rights reserved.</p>
            <div class="mt-2">
                <a href="privacy.php" class="text-muted text-decoration-none mx-2">Privacy Policy</a>
                <a href="terms.php" class="text-muted text-decoration-none mx-2">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>