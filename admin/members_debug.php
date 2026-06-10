<?php
session_start();
include("../config/database.php");

echo "Session ID: " . (isset($_SESSION["user_id"]) ? $_SESSION["user_id"] : "NOT SET") . "<br>";
echo "Role ID: " . (isset($_SESSION["role_id"]) ? $_SESSION["role_id"] : "NOT SET") . "<br>";
echo "Database connected: " . (isset($conn) ? "YES" : "NO") . "<br>";

// Allow Admin (1) and Staff (2)
if(!isset($_SESSION["user_id"]) || ($_SESSION["role_id"] != 1 && $_SESSION["role_id"] != 2)){
    echo "Access denied - redirecting to login";
    header("Location: ../login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM members ORDER BY member_id DESC");
if(!$result){
    die("Database error: " . mysqli_error($conn));
}
echo "Members loaded successfully";
?>
