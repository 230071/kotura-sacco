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

$member_id = mysqli_real_escape_string($conn, $member['member_id']);
ensure_repayment_reminders($conn, $member_id);

$notifications = mysqli_query($conn, "SELECT * FROM notifications WHERE member_id='$member_id' ORDER BY created_at DESC");
mysqli_query($conn, "UPDATE notifications SET is_read=1 WHERE member_id='$member_id'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications - KOTURA SACCO</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        body{ font-family: Arial; background:#f4f6f9; padding:20px; }
        .box{ max-width:800px; margin:auto; background:white; padding:20px; border-radius:10px; }
        .notice{ border-bottom:1px solid #ddd; padding:15px 0; }
        .notice:last-child{ border-bottom:0; }
        .notice h3{ margin:0 0 8px; color:#1e3a8a; }
        .meta{ color:#666; font-size:13px; }
        a{ color:#1e3a8a; text-decoration:none; }
    </style>
</head>
<body>
<div class="box">
    <p><a href="dashboard.php">&larr; Back to Dashboard</a></p>
    <h2>Notifications</h2>

    <?php if($notifications && mysqli_num_rows($notifications) > 0){ ?>
        <?php while($row = mysqli_fetch_assoc($notifications)){ ?>
            <div class="notice">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><?php echo htmlspecialchars($row['message']); ?></p>
                <div class="meta"><?php echo htmlspecialchars($row['type']); ?> | <?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <p>No notifications yet.</p>
    <?php } ?>
</div>
</body>
</html>
