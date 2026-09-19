<?php
ini_set('display_errors', '0');
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

$name = trim($_POST['name'] ?? '');
$guests = trim($_POST['guests'] ?? '');
$date = trim($_POST['date'] ?? '');
$time = trim($_POST['time'] ?? '');
$note = trim($_POST['note'] ?? '');

if ($name === '' || $date === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please fill in your name and reservation date.']);
    exit;
}

$recipientAddress = trim(getenv('RESERVATION_EMAIL') ?: 'sipitcoffeeshop@gmail.com');
$gmailAddress = trim(getenv('GMAIL_EMAIL') ?: getenv('BLUDIT_SMTP_USERNAME') ?: $recipientAddress);
$gmailAppPassword = preg_replace('/\s+/', '', getenv('GMAIL_APP_PASSWORD') ?: getenv('BLUDIT_SMTP_PASSWORD') ?: '');

if ($gmailAppPassword === '') {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Email is not configured on the server.']);
    exit;
}

$body = "New reservation request\n\nName: {$name}\nGuests: {$guests}\nDate: {$date}\nTime: {$time}\nNotes: " . ($note !== '' ? $note : 'No additional notes') . "\n";
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = getenv('BLUDIT_SMTP_HOST') ?: 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $gmailAddress;
    $mail->Password = $gmailAppPassword;
    $mail->SMTPSecure = (getenv('BLUDIT_SMTP_ENCRYPTION') ?: 'tls') === 'ssl'
        ? PHPMailer::ENCRYPTION_SMTPS
        : PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = (int)(getenv('BLUDIT_SMTP_PORT') ?: 587);
    $mail->Timeout = 20;
    $mail->Timelimit = 25;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom($gmailAddress, 'Sip It Coffee Shop');
    $mail->addAddress($recipientAddress, 'Sip It Coffee Shop');
    $mail->Subject = 'New Reservation Request - Sip It Coffee Shop';
    $mail->Body = $body;
    $mail->AltBody = $body;
    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Thanks, ' . $name . '! Your reservation request has been sent.']);
} catch (Exception $exception) {
    error_log('Sip It reservation email failed: ' . $mail->ErrorInfo);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Email delivery failed. Check the server SMTP settings.']);
}
