<?php
session_start();
include_once 'db.php';
include_once __DIR__ . '/../logging/logactivity.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    if (empty($username) || empty($password)) {
        echo "Username and password are required.";
        exit;
    }

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, username, password, role FROM Users WHERE username = ?");
    if ($stmt === false) {
        error_log('mysqli prepare() failed: ' . htmlspecialchars($conn->error));
        echo "An error occurred. Please try again later.";
        exit;
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $hashed_password, $role);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        // Regenerate session ID to prevent session fixation attacks
        session_regenerate_id(true);

        // Store user information in session
        $_SESSION['user_id'] = $id;
        $_SESSION['user'] = $username;
        $_SESSION['role'] = $role;
        $_SESSION['loggedin'] = true;

        // Log successful login
                logActivity($username, 'login', 'success');

        // Redirect based on role
        if ($role == 'ADMIN') {
            header("Location: ../admin/admin_dashboard.php");
        } else {
            header("Location: ../user/user_dashboard.php");
        }
        exit;
    } else {
        // Log failed login attempt
        logActivity($username, 'login', 'failure');
        echo "Invalid username or password.";
    }

    $stmt->close();
}

$conn->close();
?>
