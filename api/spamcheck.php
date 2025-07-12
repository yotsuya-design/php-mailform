<?php
// Set headers
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

// Set character encoding
mb_internal_encoding('UTF-8');

// Reject non-POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
	exit;
}

// Receive JSON data
$json = file_get_contents("php://input");
$data = json_decode($json, true);
$text = $data['text'] ?? '';

$isSpam = false;
$reason = '';

// Load spam word list from external file
$spamWordsJson = file_get_contents('spam_words.json');
$spamWordsData = json_decode($spamWordsJson, true);
$spamWords = $spamWordsData['spam_words'] ?? [];

// Check for spam words
foreach ($spamWords as $word) {
    if (mb_strpos($text, $word) !== false) {
        $isSpam = true;
        $reason = $word;
        break;
    }
}

// Check for URLs
if (!$isSpam && preg_match('/https?:\/\/[^\s]+/', $text)) {
	$isSpam = true;
	$reason = 'URL';
}

// Check for email addresses
if (!$isSpam && preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text)) {
	$isSpam = true;
	$reason = 'Email address';
}

// Send response
echo json_encode([
	'status' => 'success',
	'isSpam' => $isSpam,
	'reason' => $reason
]);
?>