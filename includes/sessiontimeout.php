<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Timed Out</title>
    <link rel="stylesheet" type="text/css" href="../css/Site.css">
</head>
<body>
    <h2>Session Timed Out</h2>
        <form>
                <div class="alert alert-danger">
                    <p>Your session has timed out due to inactivity. Please <a href="../index.php">log in</a> again to continue.</p>
                </div>
            </form>
</body>
</html>
