<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)){
    header("Location: ../login.php");
    exit();
}

// Fetch members for dropdown
$members = mysqli_query($conn, "SELECT member_id, fullname FROM members");

if(isset($_POST['save'])){

    $member_id = $_POST['member_id'];
    $amount = $_POST['amount'];
    $date = $_POST['savings_date'];
    $recorded_by = $_SESSION['user_id'];

    $sql = "INSERT INTO savings (member_id, amount, savings_date, recorded_by)
            VALUES ('$member_id', '$amount', '$date', '$recorded_by')";

    if(mysqli_query($conn, $sql)){
        echo "<script>alert('Savings recorded successfully');</script>";
    } else {
        echo "<script>alert('Error recording savings');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Savings</title>

    <style>
        body{
            font-family: Arial;
            background:#f4f6f9;
            padding:20px;
        }

        .box{
            max-width:600px;
            margin:auto;
            background:white;
            padding:20px;
            border-radius:10px;
        }

        input, select{
            width:100%;
            padding:10px;
            margin:10px 0;
        }

        button{
            background:#1e3a8a;
            color:white;
            padding:10px;
            border:none;
            width:100%;
        }
    </style>
</head>

<body>

<div class="box">

    <h2>Record Savings</h2>

    <form method="POST">

        <label>Select Member</label>
        <select name="member_id" required>
            <option value="">-- Select Member --</option>

            <?php while($row = mysqli_fetch_assoc($members)) { ?>
                <option value="<?php echo $row['member_id']; ?>">
                    <?php echo $row['fullname']; ?>
                </option>
            <?php } ?>

        </select>

        <label>Amount (UGX)</label>
        <input type="number" name="amount" required>

        <label>Date</label>
        <input type="date" name="savings_date" required>

        <button type="submit" name="save">Record Savings</button>

    </form>

</div>

</body>
</html>
