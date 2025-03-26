<?php 
session_start();
require_once 'config/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Text Summarizer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .feature-icon {
            font-size: 2.5rem;
            color: #0d6efd;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <div class="container py-5">
        <div class="row mb-5">
            <div class="col-md-8 mx-auto text-center">
                <h1 class="display-4 mb-4">About Text Summarizer</h1>
                <p class="lead">We help you save time by automatically summarizing long texts while preserving the most important information.</p>
            </div>
        </div>

        <!-- Features Section -->
        <div class="row mb-5">
            <h2 class="text-center mb-4">Our Features</h2>
            <div class="col-md-4 text-center mb-4">
                <div class="feature-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3>Fast Processing</h3>
                <p>Get your summaries within seconds using our advanced AI algorithms.</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="feature-icon">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3>Accurate Results</h3>
                <p>Our system maintains high accuracy while reducing text length significantly.</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="feature-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h3>Secure & Private</h3>
                <p>Your data is encrypted and never shared with third parties.</p>
            </div>
        </div>

        <!-- Team Section -->
        <div class="row mb-5">
            <h2 class="text-center mb-4">Our Team</h2>
            <div class="col-md-4 text-center mb-4">
                <div class="team-member">
                    <img src="assets/img/team1.jpg" alt="Team Member 1" class="mb-3">
                    <h4>John Doe</h4>
                    <p class="text-muted">Founder & CEO</p>
                </div>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="team-member">
                    <img src="assets/img/team2.jpg" alt="Team Member 2" class="mb-3">
                    <h4>Jane Smith</h4>
                    <p class="text-muted">Lead Developer</p>
                </div>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="team-member">
                    <img src="assets/img/team3.jpg" alt="Team Member 3" class="mb-3">
                    <h4>Mike Johnson</h4>
                    <p class="text-muted">AI Specialist</p>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>