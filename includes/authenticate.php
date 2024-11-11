<?php
session_start();
include_once 'db.php';
include_once __DIR__ . '/../logging/logactivity.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');

    if (empty($username) || empty($password)) {
        echo "Username and password are required.";
        exit;
    }

    // Prepare and bind
    $stmt = $conn->prepare("SELECT id, username, password, role, active FROM users WHERE username = ?");
    if ($stmt === false) {
        error_log('mysqli prepare() failed: ' . htmlspecialchars($conn->error));
        echo "An error occurred. Please try again later.";
        exit;
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $hashed_password, $role, $active);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        if ($active) {
            // Regenerate session ID to prevent session fixation attacks
            session_regenerate_id(true);

            // Store user information in session
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
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
            ?>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" type="text/css" href="../css/Site.css">
                <form>
                <div class="alert alert-danger">
                    <?php echo "User $username is inactive, please contact your administrator."; ?>
                    <br><br>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='../index.php'">Go Back</button>
                </div>
                </form>
            <?php      
        }
    } else {
        // Log failed login attempt
        logActivity($username, 'login', 'failure');
        ?>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link rel="stylesheet" type="text/css" href="../css/Site.css">
                <form>
                <div class="alert alert-danger">
                    <?php echo "Invalid Username or Password."; ?>
                    <br><br>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='../index.php'">Go Back</button>
                </div>
                </form>
            <?php  
    }

    $stmt->close();
}

$conn->close();
?>
