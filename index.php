<!DOCTYPE html>
<html>
<head>
<?php
include 'includes/authenticate.php'; 


?>

    <meta charset="UTF-8">
    <title>Login Form</title>
    <link rel="stylesheet" type="text/css" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel="icon" type="image/x-icon" href="images/favicon.png">


</head>
<body>
    <div class="login">
        <img src="images/logo.png" width="300px" /><br /><br />
        <form action="includes/authenticate.php" method="post">
            <input type="text" name="username" placeholder="Enter Your Username Here" required="required">
            <input type="password" name="password" placeholder="Enter Your Password Here" required="required">
            <br />
            <button type="submit" class="btn btn-primary btn-block btn-large">Login</button>
        </form>
    </div>
    <script src="js/index.js"></script>
</body>
</html>
