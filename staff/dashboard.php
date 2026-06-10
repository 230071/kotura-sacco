<?php
session_start();
include("../config/database.php");

// Staff only
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2){
    header("Location: ../login.php");
    exit();
}

// TOTAL MEMBERS
$members = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM members"));

// RECENT MEMBERS (last 7 days)
$recent_members = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM members WHERE join_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)"));

// PENDING LOANS
$pending_loans = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM loans WHERE status='Pending'"));

// APPROVED LOANS
$approved_loans = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM loans WHERE status='Approved'"));

// TOTAL SAVINGS
$savings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(amount),0) AS total FROM savings"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - KOTURA SACCO</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        body { margin:0; font-family: Arial, sans-serif; background:#f4f6f9; }
        .sidebar { width:220px; height:100vh; background:#0f172a; color:white; position:fixed; padding:20px; }
        .sidebar h2 { text-align:center; margin-bottom:30px; color: #3b82f6; }
        .sidebar a { display:block; color:white; text-decoration:none; padding:12px 15px; border-radius:5px; margin:5px 0; }
        .sidebar a:hover { background:#1e3a8a; }
        .main { margin-left:240px; padding:20px; }
        .topbar { background:white; padding:15px 20px; border-radius:10px; margin-bottom:20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:15px; }
        .card { background:white; padding:20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); text-align: center; }
        .card h3 { margin:0; color:#1e3a8a; font-size: 16px; }
        .card p { font-size:22px; font-weight:bold; margin-top:10px; color: #0f172a; }
        .quick-links { margin-top:30px; background:white; padding:20px; border-radius:10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .quick-links h3 { color: #1e3a8a; margin-top: 0; }
        .quick-links a { display:inline-block; margin:5px; padding:10px 15px; background:#1e3a8a; color:white; border-radius:5px; text-decoration:none; font-weight: bold; }
        .quick-links a:hover { background:#3749bb; }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>KOTURA STAFF</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="members.php">Members</a>
    <a href="add_member.php">Add Member</a>
    <a href="add_savings.php">Record Savings</a>
    <a href="add_repayment.php">Record Repayment</a>
    <a href="../logout.php">Logout</a>
</div>

<!-- MAIN -->
<div class="main">

    <div class="topbar">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?></h2>
        <p style="margin: 0; color: #666;">Staff Operations Panel</p>
    </div>

    <div class="cards">
        <div class="card">
            <h3>👥 Total Members</h3>
            <p><?php echo $members['total']; ?></p>
        </div>

        <div class="card">
            <h3>📈 New Members (7 Days)</h3>
            <p><?php echo $recent_members['total']; ?></p>
        </div>

        <div class="card">
            <h3>⏳ Pending Loans</h3>
            <p><?php echo $pending_loans['total']; ?></p>
        </div>

        <div class="card">
            <h3>✅ Approved Loans</h3>
            <p><?php echo $approved_loans['total']; ?></p>
        </div>

        <div class="card">
            <h3>💰 Total Savings</h3>
            <p>UGX <?php echo number_format($savings['total'], 0); ?></p>
        </div>
    </div>

    <div class="quick-links">
        <h3>Quick Actions</h3>
        <a href="add_member.php">➕ Add Member</a>
        <a href="members.php">👥 View Members List</a>
        <a href="add_savings.php">💰 Record Savings</a>
        <a href="add_repayment.php">💳 Record Loan Repayment</a>
    </div>

</div>

</body>
</html>
