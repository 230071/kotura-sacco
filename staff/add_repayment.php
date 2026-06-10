<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)){
    header("Location: ../login.php");
    exit();
}

// Get approved loans only
$loans = mysqli_query($conn, "
    SELECT l.loan_id, m.fullname, l.amount
    FROM loans l
    JOIN members m ON l.member_id = m.member_id
    WHERE l.status = 'Approved'
");

if(isset($_POST['save'])){

    $loan_id = $_POST['loan_id'];
    $amount = $_POST['amount_paid'];
    $date = $_POST['payment_date'];
    $recorded_by = $_SESSION['user_id'];

    // Save repayment
    $sql = "INSERT INTO repayments (loan_id, amount_paid, payment_date, recorded_by)
            VALUES ('$loan_id','$amount','$date','$recorded_by')";

    mysqli_query($conn, $sql);

    echo "<script>alert('Repayment recorded successfully');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Loan Repayment</title>

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
            width:100%;
            padding:10px;
            background:#1e3a8a;
            color:white;
            border:none;
        }
    </style>
</head>

<body>

<div class="box">

    <h2>Record Loan Repayment</h2>

    <form method="POST">

        <label>Select Loan</label>
        <select name="loan_id" required>

            <option value="">-- Select Loan --</option>

            <?php while($row = mysqli_fetch_assoc($loans)) { ?>
                <option value="<?php echo $row['loan_id']; ?>">
                    <?php echo $row['fullname']; ?> - Loan: <?php echo $row['amount']; ?>
                </option>
            <?php } ?>

        </select>

        <label>Amount Paid</label>
        <input type="number" name="amount_paid" required>

        <label>Payment Date</label>
        <input type="date" name="payment_date" required>

        <button type="submit" name="save">Save Payment</button>

    </form>

</div>

</body>
</html>
