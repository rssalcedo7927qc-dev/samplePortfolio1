<?php
// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contact.php");
    exit();
}

// Get form data - matching your database columns: Name, Email, Message
$name = isset($_POST['Name']) ? trim($_POST['Name']) : '';
$email = isset($_POST['Email']) ? trim($_POST['Email']) : '';
$message = isset($_POST['Message']) ? trim($_POST['Message']) : '';

// Fallback to alternative field names if primary ones are empty
if (empty($name) && isset($_POST['visitor_name'])) {
    $name = trim($_POST['visitor_name']);
}
if (empty($email) && isset($_POST['email'])) {
    $email = trim($_POST['email']);
}
if (empty($message) && isset($_POST['message'])) {
    $message = trim($_POST['message']);
}

// Validate required fields
if (empty($name) || empty($email) || empty($message)) {
    header("Location: contact.php?name=" . urlencode($name) . "&status=error");
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: contact.php?name=" . urlencode($name) . "&status=error");
    exit();
}

// Connect to database
$conn = new mysqli('localhost', 'admin', '1234', 'portfolio_db');

// Check connection
if ($conn->connect_error) {
    header("Location: contact.php?name=" . urlencode($name) . "&status=error");
    exit();
}

// Generate timestamp
$timestamp = date('Y-m-d H:i:s');

// Prepare SQL statement - Using your table name: contact_messages
$sql = "INSERT INTO `contact_messages` (`Name`, `Email`, `Message`, `Date_sent`) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    $conn->close();
    header("Location: contact.php?name=" . urlencode($name) . "&status=error");
    exit();
}

// Bind parameters (all strings)
$stmt->bind_param("ssss", $name, $email, $message, $timestamp);

// Execute the statement
if ($stmt->execute()) {
    $status = 'success';
} else {
    $status = 'error';
}

// Close connections
$stmt->close();
$conn->close();

// Redirect back with status
header("Location: contact.php?name=" . urlencode($name) . "&status=" . $status);
exit();
?>