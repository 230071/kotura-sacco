<?php
session_start();
include("../config/database.php");

// Allow only Admin (1)
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : null;

if(!$id){
    $_SESSION['error'] = "No member selected!";
    header("Location: members.php");
    exit();
}

// Verify member exists before deletion
$check = mysqli_query($conn, "SELECT member_id FROM members WHERE member_id='$id'");
if(!$check || mysqli_num_rows($check) == 0){
    $_SESSION['error'] = "Member not found!";
    header("Location: members.php");
    exit();
}

// Delete the member
$sql = "DELETE FROM members WHERE member_id='$id'";

if(mysqli_query($conn, $sql)){
    $_SESSION['message'] = "Member deleted successfully!";
    header("Location: members.php?success=1");
    exit();
} else {
    $_SESSION['error'] = "Error deleting member: " . mysqli_error($conn);
    header("Location: members.php?error=1");
    exit();
}
?>