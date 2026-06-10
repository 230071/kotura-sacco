<?php
session_start();
include("../config/database.php");

// Allow Admin (1) and Staff (2)
if(!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)){
    header("Location: ../login.php");
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM members ORDER BY member_id DESC");

if(!$result){
    die("Database error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Members List</title>

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
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:20px;
        }

        table th{
            background:#1e3a8a;
            color:white;
            padding:10px;
        }

        table td{
            border:1px solid #ddd;
            padding:10px;
        }

        .btn{ 
            padding:5px 10px; 
            border-radius:5px; 
            color:white; 
            text-decoration:none; 
            margin-right:5px; 
            display:inline-block;
            cursor: pointer;
        }

        .edit{ background:green; }
        .edit:hover{ background:darkgreen; }

        .delete{ background:red; }
        .delete:hover{ background:darkred; }

        .top-links{
            margin-bottom:20px;
        }

        .top-links a{
            text-decoration:none;
            margin-right:10px;
            color:#1e3a8a;
        }

        .add-btn{
            background:#1e3a8a;
            color:white;
            padding:8px 12px;
            border-radius:5px;
            display:inline-block;
        }

        .add-btn:hover{
            background:#1e40af;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Members List</h2>

    <div class="top-links">
        <a href="dashboard.php">← Back to Dashboard</a>
        <?php if($_SESSION['role_id'] == 1){ ?>
            <a href="add_member.php" class="add-btn">➕ Add Member</a>
        <?php } ?>
    </div>

    <table>

        <tr>
            <th>ID</th>
            <th>Membership No</th>
            <th>Full Name</th>
            <th>Gender</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Join Date</th>
            <th>Action</th>
        </tr>

        <?php 
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)) { 
        ?>

        <tr>

            <td><?php echo htmlspecialchars($row['member_id']); ?></td>
            <td><?php echo htmlspecialchars($row['membership_no']); ?></td>
            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
            <td><?php echo htmlspecialchars($row['gender']); ?></td>
            <td><?php echo htmlspecialchars($row['phone']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['join_date']); ?></td>

            <td>

                <?php if($_SESSION['role_id'] == 1){ ?>

                    <a href="edit_member.php?id=<?php echo urlencode($row['member_id']); ?>" class="btn edit">✎ Edit</a>
                    <a href="delete_member.php?id=<?php echo urlencode($row['member_id']); ?>" class="btn delete" onclick="return confirm('Are you sure you want to delete this member?');">🗑 Delete</a>

                <?php } else { ?>

                    <span style="color:gray;">View Only</span>

                <?php } ?>

            </td>

        </tr>

        <?php 
            }
        } else {
            echo "<tr><td colspan='8' style='text-align:center;'>No members found</td></tr>";
        }
        ?>

    </table>

</div>

</body>
</html>
