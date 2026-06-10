<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3){
    header("Location: ../login.php");
    exit();
}

include("../config/database.php");
include("../config/member_helpers.php");

$member = get_current_member($conn);
if(!$member){
    die("Member profile not found.");
}
$member_id = $member['member_id'];

// Get savings total
$savings_query = "SELECT SUM(amount) as total_savings FROM savings WHERE member_id = '$member_id'";
$savings_result = mysqli_query($conn, $savings_query);
$savings_data = mysqli_fetch_assoc($savings_result);
$total_savings = $savings_data['total_savings'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - KOTURA SACCO</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        .profile-box { background: white; padding: 20px; border-radius: 10px; max-width: 600px; margin: 20px auto; }
        .profile-item { margin: 15px 0; }
        .profile-item strong { display: inline-block; width: 150px; }
        a { text-decoration: none; color: #1e3a8a; }
    </style>
</head>
<body>

<div class="profile-box">
    <h1>My Profile</h1>
    <p><a href="dashboard.php">← Back to Dashboard</a></p>

    <div class="profile-item">
        <strong>Name:</strong> <?php echo htmlspecialchars($member['fullname']); ?>
    </div>

    <div class="profile-item">
        <strong>Member ID:</strong> <?php echo $member_id; ?>
    </div>

    <div class="profile-item">
        <strong>Total Savings:</strong> UGX <?php echo number_format($total_savings, 2); ?>
    </div>

    <div class="profile-item">
        <strong>Account Status:</strong> <span style="color: green;">Active</span>
    </div>

    <hr>

    <p><a href="../logout.php">Logout</a></p>
</div>

</body>
</html>
