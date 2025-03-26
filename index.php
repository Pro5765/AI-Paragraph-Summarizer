<?php
session_start();
require_once 'config/db.php';

// Clear any previous output
ob_clean();

// Error handling
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
ini_set('error_log', __DIR__ . '/debug.log');

// Autoloader
require_once __DIR__ . '/vendor/autoload.php';

use TextSummarizer\TextAnalyzer;

// Only process AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        if (empty($_POST['text'])) {
            throw new Exception('No text provided');
        }

        $inputText = trim($_POST['text']);
        $summary = summarizeText($inputText);

        if (empty($summary)) {
            throw new Exception('Failed to generate summary');
        }

        // Store the summary if user is logged in
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id'];
            $charactersCount = strlen($inputText);
            $reductionPercentage = round((1 - strlen($summary) / strlen($inputText)) * 100, 1);

            $stmt = $pdo->prepare("
                INSERT INTO summaries (
                    user_id, 
                    original_text, 
                    summary_text, 
                    characters_count, 
                    reduction_percentage
                ) VALUES (?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $userId,
                $inputText,
                $summary,
                $charactersCount,
                $reductionPercentage
            ]);

            // Add this after the summary storage code
            error_log("Summary stored for user: " . $_SESSION['user_id'] . ", Length: " . strlen($inputText));
        }

        echo json_encode([
            'success' => true,
            'summary' => $summary,
            'original_length' => strlen($inputText),
            'summary_length' => strlen($summary),
            'reduction_percentage' => round((1 - strlen($summary) / strlen($inputText)) * 100, 1)
        ]);
        
    } catch (Exception $e) {
        error_log("Summarizer Error: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

// For non-AJAX requests, show the HTML interface
header('Content-Type: text/html; charset=UTF-8');

function summarizeText($text) {
    $analyzer = new TextAnalyzer();
    
    // Normalize text
    $text = preg_replace('/\s+/', ' ', trim($text));
    
    // Split into sentences with improved handling
    $abbreviations = '(Mr|Mrs|Ms|Dr|Prof|Sr|Jr|vs|Vol|etc|avg|dept|est|min|max)';
    $sentences = preg_split('/(?<!' . $abbreviations . '.)(?<=[.!?])\s+(?=[A-Z])/', $text);
    
    if (count($sentences) <= 3) {
        return $text;
    }

    // Calculate sentence scores
    $sentenceScores = [];
    $totalSentences = count($sentences);
    
    foreach ($sentences as $index => $sentence) {
        $score = 0;
        
        // Calculate importance based on similarity with other sentences
        $score += $analyzer->calculateImportance($sentence, $sentences);
        
        // Position score
        $positionScore = 1.0;
        if ($index === 0 || $index === $totalSentences - 1) {
            $positionScore = 1.5;
        } elseif ($index < $totalSentences * 0.2) {
            $positionScore = 1.3;
        }
        $score *= $positionScore;
        
        // Length score
        $wordCount = str_word_count($sentence);
        if ($wordCount >= 5 && $wordCount <= 25) {
            $score *= 1.2;
        }
        
        $sentenceScores[$index] = $score;
    }

    // Dynamic summary length
    $textLength = strlen($text);
    if ($textLength < 1000) {
        $percentToKeep = 0.6;
    } elseif ($textLength < 5000) {
        $percentToKeep = 0.4;
    } else {
        $percentToKeep = 0.3;
    }

    $summaryLength = max(3, ceil(count($sentences) * $percentToKeep));
    
    // Select top scoring sentences
    arsort($sentenceScores);
    $topSentenceIndices = array_keys(array_slice($sentenceScores, 0, $summaryLength, true));
    sort($topSentenceIndices);
    
    // Build summary
    $summary = '';
    $prevIndex = -1;
    
    foreach ($topSentenceIndices as $index) {
        $sentence = trim($sentences[$index]);
        if ($prevIndex !== -1 && $index - $prevIndex > 1) {
            $summary .= "Moreover, ";
        }
        $summary .= $sentence . ' ';
        $prevIndex = $index;
    }
    
    $summary = trim($summary);
    
    // Store summary if user is logged in
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("
                INSERT INTO summaries (
                    user_id,
                    original_text,
                    summary_text,
                    characters_count,
                    reduction_percentage
                ) VALUES (?, ?, ?, ?, ?)
            ");
            
            $charactersCount = strlen($text);
            $reductionPercentage = round((1 - strlen($summary) / strlen($text)) * 100, 1);
            
            $stmt->execute([
                $_SESSION['user_id'],
                $text,
                $summary,
                $charactersCount,
                $reductionPercentage
            ]);
            
            error_log("Summary stored successfully for user ID: " . $_SESSION['user_id']);
        } catch (PDOException $e) {
            error_log("Error storing summary: " . $e->getMessage());
        }
    }
    
    return $summary;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Text Summarizer - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand img { height: 40px; }
        .footer { background-color: #343a40; color: white; padding: 2rem 0; }
        .main-content { min-height: calc(100vh - 160px); }
        .nav-link { color: rgba(255,255,255,.85) !important; }
        .nav-link:hover { color: white !important; }
        :root {
            --bs-primary: #0d6efd;
            --bs-primary-rgb: 13, 110, 253;
            --gradient: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        }

        body {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 2rem 1rem;
        }

        .container {
            max-width: 1000px;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 10px 30px -15px rgba(0, 0, 0, 0.15);
            background: white;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px -15px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background: var(--gradient);
            color: white;
            border-radius: 1rem 1rem 0 0 !important;
            padding: 1.5rem;
        }

        .card-header h2 {
            margin: 0;
            font-weight: 600;
            font-size: 1.75rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 0.75rem;
            padding: 1rem;
            transition: all 0.3s ease;
            min-height: 200px;
        }

        .form-control:focus {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.25);
        }

        .btn-primary {
            background: var(--gradient);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 0.75rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(var(--bs-primary-rgb), 0.4);
        }

        .loader {
            display: none;
            padding: 1rem;
            text-align: center;
        }

        .loader .spinner-border {
            width: 3rem;
            height: 3rem;
            color: var(--bs-primary);
        }

        #result {
            display: none;
            animation: fadeIn 0.5s ease-out;
        }

        #result.show {
            display: block;
        }

        .summary-stats {
            background: #f8f9fa;
            border-radius: 0.75rem;
            padding: 1rem;
            font-size: 0.875rem;
            color: #6c757d;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .card-header {
                padding: 1rem;
            }
            
            .card-header h2 {
                font-size: 1.5rem;
            }
        }

        .navbar-brand {
            font-weight: 600;
        }

        .footer {
            background-color: #ffffff;
            border-top: 1px solid rgba(0,0,0,0.1);
        }

        .modal-header {
            background: var(--gradient);
            color: white;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .user-profile-link {
            padding: 0.5rem !important;
        }

        .user-profile-link:hover .user-avatar {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .user-avatar i {
            font-size: 1.1rem;
            color: white;
        }

        .admin-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            transition: all 0.3s ease;
        }

        .admin-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <?php include 'includes/nav.php'; ?>

    <div class="main-content">
        <div class="container mt-4">
            <div class="card animate__animated animate__fadeIn">
                <div class="card-header">
                    <h2 class="text-center">AI Text Summarizer</h2>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <textarea 
                            id="inputText" 
                            class="form-control" 
                            placeholder="Enter your text here to summarize..."
                        ></textarea>
                    </div>
                    <div class="text-center">
                        <button onclick="summarizeText()" class="btn btn-primary">
                            Summarize Text
                        </button>
                    </div>
                    
                    <div class="loader mt-4" id="loader">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Analyzing and summarizing your text...</p>
                    </div>

                    <div id="result" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Replace the existing summarizeText function
        function summarizeText() {
            const text = document.getElementById('inputText').value;
            const loader = document.getElementById('loader');
            const result = document.getElementById('result');

            if (!text.trim()) {
                showToast('Please enter some text to summarize');
                return;
            }

            // Show loader and hide previous results
            loader.style.display = 'block';
            result.style.display = 'none';
            result.innerHTML = '';

            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'index.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            // Update the xhr.onload function in your existing JavaScript
            xhr.onload = function() {
                loader.style.display = 'none';
                
                try {
                    if (xhr.status !== 200) {
                        throw new Error(`Server error: ${xhr.status}`);
                    }

                    let response;
                    try {
                        response = JSON.parse(xhr.responseText);
                    } catch (parseError) {
                        console.error('Invalid JSON response:', xhr.responseText);
                        throw new Error('Server returned invalid data');
                    }

                    if (!response.success) {
                        throw new Error(response.error || 'Unknown error occurred');
                    }

                    result.innerHTML = `
                        <div class="card animate__animated animate__fadeIn">
                            <div class="card-body">
                                <h3 class="card-title mb-4">Summary</h3>
                                <p class="lead">${escapeHtml(response.summary)}</p>
                                <div class="summary-stats mt-4">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong>Original Length:</strong><br>
                                            ${response.original_length} characters
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Summary Length:</strong><br>
                                            ${response.summary_length} characters
                                        </div>
                                        <div class="col-md-4">
                                            <strong>Reduction:</strong><br>
                                            ${response.reduction_percentage}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                    result.style.display = 'block';
                    result.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

                } catch (e) {
                    console.error('Error:', e.message);
                    console.error('Server response:', xhr.responseText);
                    showError(e.message);
                }
            };

            xhr.onerror = function() {
                loader.style.display = 'none';
                showError('Network error occurred');
            };

            xhr.send('text=' + encodeURIComponent(text));
        }

        // Add this helper function for secure HTML escaping
        function escapeHtml(unsafe) {
            return unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        // Update the error display function
        function showError(message) {
            const result = document.getElementById('result');
            result.innerHTML = `
                <div class="alert alert-danger animate__animated animate__fadeIn">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <div>
                            <strong>Error:</strong> ${escapeHtml(message)}
                            <br>
                            <small>Please try again or contact support if the problem persists.</small>
                        </div>
                    </div>
                </div>`;
            result.style.display = 'block';
            result.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function showToast(message) {
            // Remove any existing toasts
            const existingToast = document.querySelector('.toast-container');
            if (existingToast) {
                existingToast.remove();
            }

            const toastHtml = `
                <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11">
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-body bg-danger text-white rounded">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            ${message}
                        </div>
                    </div>
                </div>`;
            
            document.body.insertAdjacentHTML('beforeend', toastHtml);
            
            setTimeout(() => {
                const toast = document.querySelector('.toast-container');
                if (toast) {
                    toast.remove();
                }
            }, 3000);
        }

        // Add this function after the existing JavaScript functions
        function debugResponse(responseText) {
            try {
                return JSON.parse(responseText);
            } catch (e) {
                console.error('Raw response:', responseText);
                console.error('Parse error:', e);
                return null;
            }
        }
    </script>

    <!-- Add this before the closing </body> tag -->
    <!-- Delete Account Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-danger">Warning: This action cannot be undone!</p>
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
</body>
</html>