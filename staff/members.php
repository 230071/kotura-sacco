<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2){
    header("Location: ../login.php");
    exit();
}

include("../config/database.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Members - KOTURA SACCO</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #1e3a8a; color: white; }
        tr:hover { background: #f5f5f5; }
        a { text-decoration: none; color: #1e3a8a; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>KOTURA STAFF</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="members.php">Members</a>
    <a href="apply_loan.php">Loan Applications</a>
    <a href="add_savings.php">Record Savings</a>
    <a href="add_member.php">Add Member</a>
    <a href="../logout.php">Logout</a>
</div>

<div class="main">
    <h1>Members List</h1>
    <p><a href="dashboard.php">← Back to Dashboard</a></p>

    <table>
        <tr>
            <th>ID</th>
            <th>Membership No</th>
            <th>Full Name</th>
            <th>Gender</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Address</th>
            <th>Join Date</th>
            <th>Actions</th>
        </tr>
        <?php
            $query = "SELECT * FROM members";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['member_id'] . "</td>";
                    echo "<td>" . $row['membership_no'] . "</td>";
                    echo "<td>" . $row['fullname'] . "</td>";
                    echo "<td>" . $row['gender'] . "</td>";
                    echo "<td>" . $row['phone'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['address'] . "</td>";
                    echo "<td>" . $row['join_date'] . "</td>";
                    echo "<td><a href='edit_member.php?id=" . $row['member_id'] . "'>Edit</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No members found</td></tr>";
            }
        ?>
    </table>
</div>

</body>
</html>
