<?php
// features.php - CampusAssist "What we do" page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CampusAssist — Features</title>

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- Global site CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">

    <style>
        .feature-hero {
            padding: 48px 20px;
            border-radius: 12px;
            background: linear-gradient(180deg, rgba(13,110,253,0.06), rgba(13,110,253,0.02));
            margin-bottom: 24px;
        }
        .feature-icon {
            width:72px;height:72px;border-radius:14px;
            display:flex;align-items:center;justify-content:center;font-size:28px;
            box-shadow:0 6px 18px rgba(0,0,0,.05);
        }
        .feature-card .card-body { min-height:160px; }
        .feature-grid .card { border-radius:12px; }
        @media (max-width:768px){
            .feature-hero{padding:28px 12px;}
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-light bg-white shadow-sm fixed-top px-4 py-3">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-graduation-cap text-primary fs-3"></i>
            <h4 class="m-0 fw-semibold text-primary">CampusAssist</h4>
        </div>
        <div class="d-flex align-items-center gap-3">
            <button id="themeToggle" class="btn btn-light border">
                <i id="themeIcon" class="fa-solid fa-moon"></i>
            </button>
            <a href="auth/login.php" class="btn btn-outline-primary d-none d-sm-inline">Login</a>
            <a href="auth/register.php" class="btn btn-primary">Get Started</a>
        </div>
    </div>
</nav>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="sidebar-content p-3 pt-4">
        <div class="mb-3">
            <a href="index.php" class="new-chat-btn w-100 text-decoration-none d-flex justify-content-center align-items-center">
                <i class="fa-solid fa-arrow-left me-2"></i> Back to Chat
            </a>
        </div>

        <ul class="nav flex-column gap-2">
            <li class="nav-item">
                <a class="nav-link active" href="help.php">
                    <i class="fa-solid fa-star me-2"></i> Features
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="help.php">
                    <i class="fa-solid fa-circle-question me-2"></i> Help
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="help.php">
                    <i class="fa-solid fa-envelope me-2"></i> Contact
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <section class="feature-hero text-center">
        <div class="container">
            <div class="row align-items-center gy-3">
                <div class="col-md-7 text-md-start">
                    <h1 class="fw-bold">What CampusAssist can do for you</h1>
                    <p class="text-muted">
                        Smart campus assistant helping students with paperwork, schedules, and fast campus information.
                    </p>
                    <div class="d-flex gap-2 justify-content-center justify-content-md-start">
                        <a href="index.php#chat" class="btn btn-primary">
                            <i class="fa-solid fa-robot me-2"></i> Try the chat
                        </a>
                        <a href="help.php" class="btn btn-outline-primary">Help & FAQ</a>
                    </div>
                </div>
                <div class="col-md-5 d-none d-md-flex justify-content-end">
                    <div class="feature-icon bg-white border">
                        <i class="fa-solid fa-robot text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container">
        <h5 class="fw-semibold mb-3">Core features</h5>

        <div class="row g-4 feature-grid">
            <?php
            $features = [
                ["comments","Conversational Help","Ask natural language questions and get clear answers."],
                ["file-lines","Document Requests","Submit and track document requests easily."],
                ["clock","Office Hours","Check schedules and important academic dates."],
                ["shield-halved","Privacy & Security","Your data is secure and access-controlled."],
                ["bolt","Fast Answers","Optimized responses for common questions."],
                ["gear","Request Tracking","Track request status with updates."]
            ];
            foreach($features as $f):
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 feature-card">
                    <div class="card-body text-center">
                        <div class="feature-icon bg-white mx-auto mb-3">
                            <i class="fa-solid fa-<?= $f[0] ?> text-primary"></i>
                        </div>
                        <h6 class="fw-bold"><?= $f[1] ?></h6>
                        <p class="text-muted small"><?= $f[2] ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="row mt-5 g-4">
            <div class="col-md-7">
                <h5 class="fw-semibold">Frequently Asked</h5>
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq1">
                                How do I request a transcript?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse">
                            <div class="accordion-body small text-muted">
                                Request transcripts directly through chat or your dashboard.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Is my data private?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse">
                            <div class="accordion-body small text-muted">
                                Yes — only required data is stored securely.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <h5 class="fw-semibold">Ready to try?</h5>
                <p class="text-muted small">Open the chat and ask your question.</p>
                <div class="d-flex gap-2">
                    <a href="index.php#chat" class="btn btn-primary">
                        <i class="fa-solid fa-robot me-2"></i> Open chat
                    </a>
                    <a href="auth/register.php" class="btn btn-outline-primary">Sign up</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="text-center py-3 border-top mt-5">
    <p class="text-muted small mb-0">© <?= date('Y') ?> CampusAssist</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
