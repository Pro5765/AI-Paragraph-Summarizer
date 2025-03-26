<?php 
session_start();
require_once 'config/db.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message_text = $_POST['message'] ?? '';

    try {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message_text]);
        $message = '<div class="alert alert-success">Message sent successfully!</div>';
    } catch (Exception $e) {
        $message = '<div class="alert alert-danger">Failed to send message. Please try again.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Text Summarizer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 mb-4">
                <h1 class="display-4 mb-4">Contact Us</h1>
                <p class="lead mb-4">Have questions? We'd love to hear from you.</p>
                
                <div class="contact-info mb-4">
                    <h3>Contact Information</h3>
                    <p><i class="fas fa-map-marker-alt me-2"></i> 123 Main Street, City, Country</p>
                    <p><i class="fas fa-phone me-2"></i> +1 234 567 890</p>
                    <p><i class="fas fa-envelope me-2"></i> info@textsummarizer.com</p>
                </div>

                <div class="social-links">
                    <h3>Follow Us</h3>
                    <a href="#" class="btn btn-outline-primary me-2"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="btn btn-outline-primary me-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="btn btn-outline-primary"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <?php echo $message; ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" class="form-control" id="subject" name="subject" required>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>