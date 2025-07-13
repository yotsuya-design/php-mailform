<?php
// ==================================================
// General-purpose PHP for sending emails
//
// Required settings:
// - TO_EMAIL: Recipient's email address
// - FROM_NAME: Sender's name
// - ORG_NAME: Organization name
// ==================================================

// --- Start of settings ---
$TO_EMAIL = "your-email@example.com"; // TODO: Please change this to your email address
$FROM_NAME = "Contact Form";
$FROM_MAIL = "noreply@example.com";
$ORG_NAME = "Your Organization";
// --- End of settings ---

// Set headers
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

// Set character encoding
mb_internal_encoding('UTF-8');

// Deny anything other than POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

// Receive JSON
$json = file_get_contents("php://input");
$emailData = json_decode($json, true);

// Validation of required fields
$requiredFields = ['title', 'email', 'body'];
foreach ($requiredFields as $field) {
	if (empty($emailData[$field])) {
		http_response_code(400);
		echo json_encode(['status' => 'error', 'message' => 'Missing required information.']);
		exit;
	}
}

// Email data
$title = $emailData['title'];
$email = $emailData['email'];
$body = $emailData['body'];

// Mail settings
$to = $TO_EMAIL;
$from_name = $FROM_NAME;
$from = mb_encode_mimeheader($from_name) . " <" . $FROM_MAIL . ">";
$org_name = mb_encode_mimeheader($ORG_NAME);

// Header settings
$header  = "MIME-Version: 1.0\r\n";
$header .= "Content-Type: text/plain; charset=UTF-8\r\n";
$header .= "Content-Transfer-Encoding: 8bit\r\n";
$header .= "From: " . $from . "\r\n";
$header .= "Reply-To: " . $email . "\r\n";
$header .= "Organization: " . $org_name . "\r\n";


$today = date("Y-m-d H:i:s");

$email_body = "A message has been sent from " . $email . " with the following title:\n\n";
$email_body .= "Time of sending: " . $today . "\n\n";
$email_body .= "Title: " . $title . "\n";
$email_body .= "Email: " . $email . "\n\n";
$email_body .= "Message:\n" . $body . "\n\n";
$email_body .= "USER IP: " . $_SERVER['REMOTE_ADDR'];

$subject = "Message from your website";
$result = mb_send_mail($to, $subject, $email_body, $header);

if ($result) {
	http_response_code(200);
	echo json_encode(['status' => 'success', 'message' => 'Mail sent successfully']);
} else {
	http_response_code(500);
	echo json_encode(['status' => 'error', 'message' => 'Failed to send mail']);
}
?>