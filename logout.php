<?php
session_start();

// Destroy the session to log out the user
session_destroy();

// Redirect to the login page or home page
header('Location: index.php');
exit();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Logout</title>
</head>
<body>
    <form method="post" action="">
        <div>
            <!-- You can add any additional content here if needed -->
        </div>
    </form>
</body>
</html>
