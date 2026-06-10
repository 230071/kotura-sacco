<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT l.*, m.fullname 
        FROM loans l
        JOIN members m ON l.member_id = m.member_id
        ORDER BY l.loan_id DESC";

$result = mysqli_query($conn, $sql);

// Check for success/error messages
$success_message = isset($_SESSION['message']) ? $_SESSION['message'] : '';
$error_message = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['message']);
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Loans - KOTURA SACCO</title>

    <style>
        body{
            font-family: Arial;
            background:#f4f6f9;
            padding:20px;
        }

        .container{
            background:white;
            padding:20px;
            border-radius:10px;
        }

        h2{
            color:#1e3a8a;
            margin-top:0;
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: none;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        th{
            background:#1e3a8a;
            color:white;
            padding:12px;
            text-align:left;
        }

        td{
            padding:12px;
            border:1px solid #ddd;
        }

        tr:hover{
            background:#f9f9f9;
        }

        .pending{ 
            color:#ff9800; 
            font-weight: bold; 
            background:#fff3e0;
            padding:5px 10px;
            border-radius:3px;
            display:inline-block;
        }

        .approved{ 
            color:green; 
            font-weight: bold;
            background:#e8f5e9;
            padding:5px 10px;
            border-radius:3px;
            display:inline-block;
        }

        .rejected{ 
            color:red; 
            font-weight: bold;
            background:#ffebee;
            padding:5px 10px;
            border-radius:3px;
            display:inline-block;
        }

        .action-links {
            white-space:nowrap;
        }

        .action-links a {
            padding: 8px 15px;
            margin: 0 3px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            display:inline-block;
            font-weight:bold;
            cursor:pointer;
        }

        .action-links a.approve {
            background: #4CAF50;
        }

        .action-links a.approve:hover {
            background: #45a049;
        }

        .action-links a.reject {
            background: #f44336;
        }

        .action-links a.reject:hover {
            background: #da190b;
        }

        .action-links span {
            color:gray;
        }

        .back-link {
            margin-bottom: 20px;
        }

        .back-link a {
            color: #1e3a8a;
            text-decoration: none;
            font-weight:bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .loan-count {
            background:#f5f5f5;
            padding:10px 15px;
            border-radius:5px;
            margin-bottom:20px;
            font-weight:bold;
            color:#1e3a8a;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="back-link">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>

    <?php if($success_message): ?>
        <div class="message success">✓ <?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if($error_message): ?>
        <div class="message error">✗ <?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <h2>📋 Loan Applications</h2>

    <div class="loan-count">
        Total Loan Applications: <?php echo mysqli_num_rows($result); ?>
    </div>

    <table>

        <tr>
            <th>Member</th>
            <th>Amount</th>
            <th>Interest</th>
            <th>Duration</th>
            <th>Purpose</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php 
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) { 
        ?>

        <tr>

            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
            <td>UGX <?php echo number_format($row['amount'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['interest_rate']); ?>%</td>
            <td><?php echo htmlspecialchars($row['duration_months']); ?> months</td>
            <td><?php echo htmlspecialchars($row['purpose']); ?></td>

            <td>
                <?php 
                    $status = htmlspecialchars($row['status']);
                    $class = strtolower($status);
                    echo "<span class='$class'>$status</span>";
                ?>
            </td>

            <td class="action-links">

                <?php if($row['status'] == 'Pending'){ ?>

                    <a href="approve_loan.php?id=<?php echo urlencode($row['loan_id']); ?>" class="approve" onclick="return confirm('✓ Are you sure you want to APPROVE this loan for ' + '<?php echo htmlspecialchars($row['fullname']); ?>' + '?');">✓ Approve</a>
                    <a href="reject_loan.php?id=<?php echo urlencode($row['loan_id']); ?>" class="reject" onclick="return confirm('✗ Are you sure you want to REJECT this loan for ' + '<?php echo htmlspecialchars($row['fullname']); ?>' + '?');">✗ Reject</a>

                <?php } else { ?>

                    <span>—</span>

                <?php } ?>

            </td>

        </tr>

        <?php 
            }
        } else {
            echo "<tr><td colspan='7' style='text-align:center; padding:20px;'>No loan applications found</td></tr>";
        }
        ?>

    </table>

</div>

</body>
</html>
