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
ensure_repayment_reminders($conn, $member_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Status - KOTURA SACCO</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #1e3a8a; color: white; }
        tr:hover { background: #f5f5f5; }
        .status-pending { color: orange; }
        .status-approved { color: green; }
        .status-rejected { color: red; }
        a { text-decoration: none; color: #1e3a8a; }
    </style>
</head>
<body>

<div class="main">
    <h1>My Loan Applications</h1>
    <p><a href="dashboard.php">← Back to Dashboard</a></p>

    <table>
        <tr>
            <th>Loan ID</th>
            <th>Amount (UGX)</th>
            <th>Interest (%)</th>
            <th>Duration</th>
            <th>Purpose</th>
            <th>Status</th>
            <th>Applied</th>
        </tr>
        <?php
            $query = "SELECT * FROM loans WHERE member_id = '$member_id' ORDER BY loan_id DESC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $status_class = 'status-' . strtolower($row['status']);
                    echo "<tr>";
                    echo "<td>" . $row['loan_id'] . "</td>";
                    echo "<td>UGX " . number_format($row['amount'], 0) . "</td>";
                    echo "<td>" . $row['interest_rate'] . "%</td>";
                    echo "<td>" . $row['duration_months'] . " months</td>";
                    echo "<td>" . $row['purpose'] . "</td>";
                    echo "<td class='" . $status_class . "'>" . $row['status'] . "</td>";
                    echo "<td>" . date('d/m/Y', strtotime($row['application_date'])) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No loan applications found. <a href='apply_loan.php'>Apply for a loan</a></td></tr>";
            }
        ?>
    </table>
</div>

</body>
</html>
