<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../login.php");
    exit();
}

if($_SESSION['role_id'] != 1){
    header("Location: ../login.php");
    exit();
}

// Get dashboard statistics
$members = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM members"))['total'];
$savings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount), 0) AS total FROM savings"))['total'];
$loans = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM loans"))['total'];
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM loans WHERE status='Pending'"))['total'];
$approved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM loans WHERE status='Approved'"))['total'];
$rejected = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM loans WHERE status='Rejected'"))['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - KOTURA SACCO</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        .sidebar a { display: block; padding: 12px 15px; color: white; text-decoration: none; margin: 5px 0; border-radius: 5px; }
        .sidebar a:hover { background: rgba(255,255,255,0.2); }
        .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); text-align: center; }
        .card h3 { margin: 0; color: #1e3a8a; }
        .card p { font-size: 24px; font-weight: bold; color: #1e3a8a; margin: 10px 0 0 0; }
        .stat-label { color: #666; font-size: 12px; }
        .quick-links { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin: 20px 0; }
        .quick-links a { background: #1e3a8a; color: white; padding: 15px; border-radius: 5px; text-decoration: none; text-align: center; }
        .quick-links a:hover { background: #1e40af; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>KOTURA ADMIN</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="members.php">Members</a>
    <a href="add_member.php">Add Member</a>
    <a href="loans.php">Manage Loans</a>
    <a href="savings.php">Savings Records</a>
    <a href="loans.php">Approve / Reject Loans</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="main">
    <h1>Admin Dashboard</h1>
    <p>Welcome <strong><?php echo $_SESSION['fullname']; ?></strong> | <a href="../logout.php">Logout</a></p>

    <div class="cards">
        <div class="card">
            <h3>👥 Total Members</h3>
            <p><?php echo $members; ?></p>
        </div>

        <div class="card">
            <h3>💰 Total Savings</h3>
            <p>UGX <?php echo number_format($savings, 0); ?></p>
        </div>

        <div class="card">
            <h3>📋 Total Loans</h3>
            <p><?php echo $loans; ?></p>
        </div>

        <div class="card">
            <h3>⏳ Pending Loans</h3>
            <p><?php echo $pending; ?></p>
            <p class="stat-label">Awaiting approval</p>
        </div>

        <div class="card">
            <h3>✅ Approved Loans</h3>
            <p><?php echo $approved; ?></p>
        </div>

        <div class="card">
            <h3>❌ Rejected Loans</h3>
            <p><?php echo $rejected; ?></p>
        </div>
    </div>

    <h2>Quick Actions</h2>
    <div class="quick-links">
        <a href="add_member.php">➕ Add New Member</a>
        <a href="loans.php">📋 View All Loans</a>
        <a href="members.php">👥 View Members</a>
        <a href="savings.php">💾 View Savings</a>
    </div>

    <h2>Recent Activity</h2>
    <div style="background: white; padding: 20px; border-radius: 10px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="background: #f5f5f5;">
                <th style="padding: 10px; text-align: left; border-bottom: 2px solid #1e3a8a;">Type</th>
                <th style="padding: 10px; text-align: left; border-bottom: 2px solid #1e3a8a;">Count</th>
                <th style="padding: 10px; text-align: left; border-bottom: 2px solid #1e3a8a;">Action</th>
            </tr>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px;">Pending Loan Applications</td>
                <td style="padding: 10px;"><strong><?php echo $pending; ?></strong></td>
                <td style="padding: 10px;"><a href="loans.php" style="color: #1e3a8a; text-decoration: none;">Review →</a></td>
            </tr>
            <tr style="border-bottom: 1px solid #ddd;">
                <td style="padding: 10px;">Total Members</td>
                <td style="padding: 10px;"><strong><?php echo $members; ?></strong></td>
                <td style="padding: 10px;"><a href="members.php" style="color: #1e3a8a; text-decoration: none;">View →</a></td>
            </tr>
            <tr>
                <td style="padding: 10px;">Total Savings</td>
                <td style="padding: 10px;"><strong>UGX <?php echo number_format($savings, 0); ?></strong></td>
                <td style="padding: 10px;"><a href="savings.php" style="color: #1e3a8a; text-decoration: none;">View →</a></td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>