<?php
error_reporting(E_ALL);
session_start();
include("config/database.php");

if (isset($_POST['login'])) {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "
    SELECT users.*, roles.role_name
    FROM users
    JOIN roles ON users.role_id = roles.role_id
    WHERE username='$username'
    LIMIT 1
    ";

    $result = mysqli_query($conn, $query);

    if ($row = mysqli_fetch_assoc($result)) {

        if ($password == $row['password']) {

            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['fullname'] = $row['fullname'];
            $_SESSION['role_id'] = $row['role_id'];
            $_SESSION['role_name'] = $row['role_name'];

            if ($row['role_name'] == 'Admin') {
                header("Location: admin/dashboard.php");
            } 
            elseif ($row['role_name'] == 'Staff') {
                header("Location: staff/dashboard.php");
            } 
            else {
                header("Location: member/dashboard.php");
            }

            exit();

        } else {
            echo "<script>alert('Wrong password');</script>";
        }

    } else {
        echo "<script>alert('User not found');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KOTURA SACCO Login</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-container">

    <div class="login-box">

        <h2>KOTURA SACCO</h2>
        <p>Login to your account</p>

        <form method="POST">

            <input type="text" name="username" placeholder="Username" required>

            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" name="login">Login</button>

        </form>

    </div>

</div>

</body>
</html>