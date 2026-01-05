<?php
require __DIR__ . '/../app/config/connect.php';

// header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => "Warning",
        "message" => "Invalid request method"
    ]);
    exit;
}

$id = $_POST['id'] ?? null;
$email = strtolower($_POST['email']) ?? '';
$password = $_POST['password'] ?? '';

if (!$id || !$email || !$password) {
    /* die("All fields are required"); */
    echo json_encode([
        "status" => "error",
        "message" => "All fields are required"
    ]);
    exit;
}

/*
  Step 1: Fetch user by id & email
*/
$stmt = $pdo->prepare("SELECT password FROM users WHERE id = ? AND email = ?");
$stmt->execute([$id, $email]);
$user = $stmt->fetch();

if (!$user) {
    echo json_encode([
        "status" => "Warning",
        "message" => "User not found with provided ID and Email"
    ]);
    exit;
}

/*
  Step 2: Verify password
  If you stored password as HASH (recommended)
*/
if (!password_verify($password, $user['password'])) {
    echo json_encode([
        "status" => "Warning",
        "message" => "Incorrect password"
    ]);
    exit;
}

/*
  Step 3: Delete user
*/
$delete = $pdo->prepare("DELETE FROM users WHERE id = ?");
$delete->execute([$id]);

echo json_encode([
    "status" => "success",
    "message" => "User deleted successfully"
]);
// header("Location: display.php?deleted=1");
exit;

?>
