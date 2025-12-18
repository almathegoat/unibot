<?php
// chatbot.php
header('Content-Type: application/json; charset=utf-8');

// load FAQ data
$faqFile = __DIR__ . '/faq.php';
if (!file_exists($faqFile)) {
    echo json_encode(['reply' => 'FAQ data file missing. Create faq_data.php as instructed.']);
    exit;
}
include $faqFile;

$raw = trim($_POST['question'] ?? '');
if ($raw === '') {
    echo json_encode(['reply' => 'Please type a question.']);
    exit;
}

$q = mb_strtolower($raw, 'UTF-8');

// Matching algorithm:
// 1) direct substring of keys
// 2) token overlap (most tokens matched)
// 3) fallback
$best = null;
$bestScore = 0;
foreach ($FAQ as $key => $answer) {
    $keyLower = mb_strtolower($key, 'UTF-8');
    // direct substring
    if (mb_strpos($q, $keyLower) !== false) {
        $best = $answer;
        $bestScore = PHP_INT_MAX;
        break;
    }
    // token matching
    $tokens = preg_split('/\s+/', $keyLower);
    $score = 0;
    foreach ($tokens as $tok) {
        if ($tok === '') continue;
        if (mb_strpos($q, $tok) !== false) $score++;
    }
    if ($score > 0 && $score > $bestScore) {
        $bestScore = $score;
        $best = $answer;
    }
}

if ($best) {
    echo json_encode(['reply' => $best]);
    exit;
}

// fallback answer (friendlier)
echo json_encode(['reply' => "Sorry, I don't have an answer for that yet. Try asking about transcripts, passwords, registration, or office hours."]);
exit;
