<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
    header("Location: ../login.php");
    exit();
}

$sql = "
SELECT r.*, m.fullname, l.amount AS loan_amount
FROM repayments r
JOIN loans l ON r.loan_id = l.loan_id
JOIN members m ON l.member_id = m.member_id
ORDER BY r.repayment_id DESC
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Repayments</title>

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

        table{
            width:100%;
            border-collapse:collapse;
        }

        th{
            background:#1e3a8a;
            color:white;
            padding:10px;
        }

        td{
            padding:10px;
            border:1px solid #ddd;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Loan Repayments</h2>

    <table>

        <tr>
            <th>Member</th>
            <th>Loan Amount</th>
            <th>Amount Paid</th>
            <th>Date</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($result)) { ?>

        <tr>
            <td><?php echo $row['fullname']; ?></td>
            <td><?php echo $row['loan_amount']; ?></td>
            <td><?php echo $row['amount_paid']; ?></td>
            <td><?php echo $row['payment_date']; ?></td>
        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>
