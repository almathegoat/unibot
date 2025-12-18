<?php
// index.php - CampusAssist (HTML unchanged; CSS moved to assets/css/custom.css)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Unibot- Student Helpdesk</title>

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

    <!-- Your external CSS -->
    <link rel="stylesheet" href="assets/css/custom.css">

    <!-- Tutorial script (you said you already created it) -->
    <script src="assets/js/tutorial.js" defer></script>
</head>
<body>

<!-- NAV -->
<nav class="navbar navbar-light shadow-sm px-4 py-3 fixed-top bg-white">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-graduation-cap text-primary fs-3"></i>
            <h4 class="m-0 fw-semibold text-primary">Unibot</h4>
        </div>
        <div class="d-flex align-items-center gap-3">
            <button id="themeToggle" class="btn btn-light border" aria-label="Toggle theme">
                <i id="themeIcon" class="fa-solid fa-moon"></i>
            </button>
            <a href="auth/login.php" class="btn btn-outline-primary d-none d-sm-inline">Login</a>
            <a href="auth/register.php" class="btn btn-primary">Get Started</a>
        </div>
    </div>
</nav>

<!-- SIDEBAR -->
<div class="sidebar" aria-hidden="false">
    <div class="sidebar-content p-3 pt-4">
        <div class="mb-3">
            <button id="newChat" class="new-chat-btn w-100" aria-label="New Chat">
                <i class="fa-solid fa-plus"></i> New Chat
            </button>
        </div>
        <ul class="nav flex-column gap-2" role="navigation" aria-label="Sidebar">
            <li class="nav-item">
                <a class="nav-link" href="help.php">
                    <i class="fa-solid fa-question-circle me-2"></i> Help
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- MAIN -->
<div class="main-content">
    <section class="text-center px-4">
        <h1 class="fw-bold mb-3">Welcome to Unibot</h1>
        <p class="text-muted mb-4">You will see your messages here once you ask. Log in for personalised answers.</p>

        <!-- CHAT AREA -->
        <div class="chat-container">
            <div class="chat-box">
                <div id="messages" class="messages" aria-live="polite" role="log">
                    <div class="msg bot" data-id="greeting">
                        <div class="avatar bot" aria-hidden="true">🤖</div>
                        <div class="bubble bot"><strong>Hello! Need help?</strong> I'm CampusAssist — ask me anything about transcripts, registration, fees, and campus services.</div>
                    </div>
                </div>

                <!-- Input area -->
                <div class="chat-input-area mt-3">
                    <input id="chatInput" class="chat-input" placeholder="Ask a question..." aria-label="Ask a question">
                    <button id="sendBtn" class="btn btn-primary btn-send" aria-label="Send question"><i class="fa-solid fa-paper-plane me-2"></i>Send</button>
                </div>
            </div>
        </div>
    </section>

    <!-- SUGGESTIONS -->
    <div class="container mt-5">
        <h5 class="fw-semibold text-center mb-4">Popular questions</h5>
        <div class="row justify-content-center g-3">
            <div class="col-md-4 col-lg-3">
                <div class="suggestion-card p-3 shadow-sm rounded text-center" data-q="How do I request a transcript?">
                    <i class="fa-solid fa-file-lines text-primary fs-3 mb-2"></i>
                    <p class="m-0 small">How do I request a transcript?</p>
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="suggestion-card p-3 shadow-sm rounded text-center" data-q="I forgot my portal password">
                    <i class="fa-solid fa-lock text-primary fs-3 mb-2"></i>
                    <p class="m-0 small">I forgot my portal password</p>
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <div class="suggestion-card p-3 shadow-sm rounded text-center" data-q="Campus office hours?">
                    <i class="fa-solid fa-clock text-primary fs-3 mb-2"></i>
                    <p class="m-0 small">Campus office hours?</p>
                </div>
            </div>
        </div>
    </div>

    <!-- FEATURES -->
    <div class="container mt-5 mb-5">
        <h5 class="fw-semibold text-center mb-4">What CampusAssist can do</h5>
        <div class="row g-4 justify-content-center">
            <div class="col-md-5">
                <div class="feature-card p-4 rounded shadow-sm text-center">
                    <i class="fa-solid fa-comments text-primary fs-1 mb-3"></i>
                    <h6 class="fw-bold">24/7 Support</h6>
                    <p class="text-muted small">Get instant answers to your questions anytime, anywhere</p>
                </div>
            </div>
            <div class="col-md-5">
                <div class="feature-card p-4 rounded shadow-sm text-center">
                    <i class="fa-solid fa-tasks text-primary fs-1 mb-3"></i>
                    <h6 class="fw-bold">Request Tracking</h6>
                    <p class="text-muted small">Track all your document requests and submissions in one place</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="text-center py-3 border-top mt-5">
    <p class="text-muted small mb-0">© <?= date('Y') ?> CampusAssist. All rights reserved.</p>
</footer>

<!-- Floating assistant (starts tutorial) -->
<a onclick="startTutorial()" class="fab-button shadow-lg" style="position:fixed; right:18px; bottom:18px; cursor:pointer;">
    <i class="fa-solid fa-robot"></i>
    <span class="fab-tooltip">How can I help?</span>
</a>

<!-- Tutorial Overlay elements (ready for tutorial.js to use) -->
<div id="tutorial-overlay" style="display:none;"></div>
<div id="tutorial-highlight" style="display:none;"></div>

<div id="tutorial-tooltip" class="tutorial-box" style="display:none;">
    <p id="tutorial-text"></p>
    <div style="display:flex; gap:8px; justify-content:center; margin-top:12px;">
        <button id="tutorial-next" class="btn btn-primary btn-sm">Next</button>
        <button id="tutorial-close" class="btn btn-danger btn-sm">Close</button>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Main JS (chat + theme + UI behavior) -->
<script>
(() => {
    const messagesEl = document.getElementById('messages');
    const inputEl = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendBtn');
    const newChatBtn = document.getElementById('newChat');
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');

    // Ensure elements exist
    function safe(el) { return el !== null && el !== undefined; }

    // Scroll messages to bottom
    function scrollToBottom() {
        if (!safe(messagesEl)) return;
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    // Append bubble
    function appendMessage(text, who = 'bot') {
        if (!safe(messagesEl)) return;
        const msg = document.createElement('div');
        msg.className = 'msg ' + (who === 'bot' ? 'bot' : 'user');

        const avatar = document.createElement('div');
        avatar.className = 'avatar ' + (who === 'bot' ? 'bot' : 'user');
        avatar.setAttribute('aria-hidden', 'true');
        avatar.innerHTML = who === 'bot' ? '🤖' : '<i class="fa-solid fa-user"></i>';

        const bubble = document.createElement('div');
        bubble.className = 'bubble ' + (who === 'bot' ? 'bot' : 'user');
        bubble.innerHTML = text;

        msg.appendChild(avatar);
        msg.appendChild(bubble);

        messagesEl.appendChild(msg);
        scrollToBottom();
    }

    // Typing indicator
    function showTypingIndicator() {
        if (!safe(messagesEl)) return null;
        const t = document.createElement('div');
        t.className = 'msg bot typing';
        t.setAttribute('data-typing', '1');
        t.innerHTML = `
            <div class="avatar bot" aria-hidden="true">🤖</div>
            <div class="bubble bot"><em>...</em></div>
        `;
        messagesEl.appendChild(t);
        scrollToBottom();
        return t;
    }
    function removeTypingIndicator(node) {
        if (node && node.parentNode === messagesEl) messagesEl.removeChild(node);
    }

    // Send question to backend (only after pressing Send)
    function sendQuestion(text) {
        if (!text) return;
        appendMessage(text, 'user');
        const typingNode = showTypingIndicator();

        fetch('chatbot.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'question=' + encodeURIComponent(text)
        })
        .then(r => {
            if (!r.ok) throw new Error('Network response not ok');
            return r.json();
        })
        .then(data => {
            removeTypingIndicator(typingNode);
            // accept both { reply: "..." } or {answer: "..."} defensive
            const reply = data.reply ?? data.answer ?? data.response ?? '';
            appendMessage('<strong>Answer:</strong> ' + (reply || 'No answer.'), 'bot');
        })
        .catch(err => {
            removeTypingIndicator(typingNode);
            appendMessage('Error: Could not reach server.', 'bot');
            console.error(err);
        });
    }

    // send button behavior
    if (safe(sendBtn)) {
        sendBtn.addEventListener('click', () => {
            const val = inputEl.value.trim();
            if (!val) return;
            sendQuestion(val);
            inputEl.value = '';
            inputEl.focus();
        });
    }

    // Enter sends (no auto-send while typing)
    if (safe(inputEl)) {
        inputEl.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (safe(sendBtn)) sendBtn.click();
            }
        });
    }

    // New Chat
    if (safe(newChatBtn)) {
        newChatBtn.addEventListener('click', () => {
            if (!safe(messagesEl)) return;
            while (messagesEl.firstChild) messagesEl.removeChild(messagesEl.firstChild);
            const greeting = document.createElement('div');
            greeting.className = 'msg bot';
            greeting.setAttribute('data-id', 'greeting');
            greeting.innerHTML = `
                <div class="avatar bot" aria-hidden="true">🤖</div>
                <div class="bubble bot"><strong>Hello! Need help?</strong> I'm CampusAssist — ask me anything about transcripts, registration, fees, and campus services.</div>
            `;
            messagesEl.appendChild(greeting);
            if (safe(inputEl)) inputEl.value = '';
            if (safe(inputEl)) inputEl.focus();
            scrollToBottom();
        });
    }

    // suggestion cards
    document.querySelectorAll('.suggestion-card').forEach(card => {
        card.addEventListener('click', () => {
            const q = card.getAttribute('data-q') || card.textContent.trim();
            if (safe(inputEl)) inputEl.value = q;
            if (safe(sendBtn)) sendBtn.click();
        });
    });

    // THEME: System + toggle + saved preference (Option 3)
    const THEME_KEY = 'campus_theme_pref';
    function applyThemeClass(theme) {
        if (theme === 'dark') {
            document.documentElement.classList.add('dark-mode');
            document.documentElement.classList.remove('light-mode');
            if (safe(themeIcon)) themeIcon.className = 'fa-solid fa-sun';
        } else {
            document.documentElement.classList.remove('dark-mode');
            document.documentElement.classList.add('light-mode');
            if (safe(themeIcon)) themeIcon.className = 'fa-solid fa-moon';
        }
    }

    // Determine initial theme: saved -> system -> light
    const saved = localStorage.getItem(THEME_KEY);
    if (saved === 'dark' || saved === 'light') {
        applyThemeClass(saved);
    } else {
        // follow system preference
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        applyThemeClass(prefersDark ? 'dark' : 'light');
    }

    // Update on system changes (only if user hasn't set a preference)
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            const savedNow = localStorage.getItem(THEME_KEY);
            if (savedNow !== 'dark' && savedNow !== 'light') {
                applyThemeClass(e.matches ? 'dark' : 'light');
            }
        });
    }

    // Toggle button
    if (safe(themeToggle)) {
        themeToggle.addEventListener('click', () => {
            // toggle and save preference
            const nowDark = document.documentElement.classList.contains('dark-mode');
            const newTheme = nowDark ? 'light' : 'dark';
            applyThemeClass(newTheme);
            localStorage.setItem(THEME_KEY, newTheme);
        });
    }

    // initial scroll
    scrollToBottom();
})();
</script>
</body>
</html>
