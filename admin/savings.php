<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)){
    header("Location: ../login.php");
    exit();
}

$sql = "SELECT s.*, m.fullname 
        FROM savings s
        JOIN members m ON s.member_id = m.member_id
        ORDER BY s.saving_id DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Savings Records</title>

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

    <h2>Savings History</h2>

    <table>

        <tr>
            <th>Member</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Recorded By</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($result)) { ?>

        <tr>
            <td><?php echo $row['fullname']; ?></td>
            <td><?php echo $row['amount']; ?></td>
            <td><?php echo $row['savings_date']; ?></td>
            <td><?php echo $row['recorded_by']; ?></td>
        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>
