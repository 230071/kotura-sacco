<?php
session_start();
include("../config/database.php");
include("../config/member_helpers.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : null;

if(!$id){
    $_SESSION['error'] = "No loan selected.";
    header("Location: loans.php");
    exit();
}

$sql = "UPDATE loans 
        SET status='Rejected'
        WHERE loan_id='$id' AND status='Pending'";

if(mysqli_query($conn, $sql)){
    if(mysqli_affected_rows($conn) > 0){
        $loan_result = mysqli_query($conn, "SELECT member_id, amount FROM loans WHERE loan_id='$id' LIMIT 1");
        if($loan_result && $loan = mysqli_fetch_assoc($loan_result)){
            create_notification(
                $conn,
                $loan['member_id'],
                $id,
                "Loan rejected",
                "Your loan request of UGX " . number_format($loan['amount'], 0) . " was rejected.",
                "Loan Rejected"
            );
        }
        $_SESSION['message'] = "Loan rejected successfully.";
    } else {
        $_SESSION['error'] = "Loan was not rejected. It may already be processed.";
    }
    header("Location: loans.php");
    exit();
} else {
    $_SESSION['error'] = "Error rejecting loan: " . mysqli_error($conn);
    header("Location: loans.php");
    exit();
}
?>
