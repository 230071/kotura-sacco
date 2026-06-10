<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2){
    header("Location: ../login.php");
    exit();
}

include("../config/database.php");

if(isset($_POST['save'])){

    $membership_no = $_POST['membership_no'];
    $fullname = $_POST['fullname'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $join_date = $_POST['join_date'];

    $sql = "INSERT INTO members
    (membership_no, fullname, gender, phone, email, address, join_date)
    VALUES
    ('$membership_no','$fullname','$gender','$phone','$email','$address','$join_date')";

    if(mysqli_query($conn, $sql)){
        echo "<script>alert('Member added successfully by staff');</script>";
    } else {
        echo "<script>alert('Error adding member');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff - Add Member</title>

    <style>
        body{
            font-family: Arial;
            background:#f4f6f9;
            padding:20px;
        }

        .box{
            background:white;
            padding:20px;
            max-width:600px;
            margin:auto;
            border-radius:10px;
        }

        input, select, textarea{
            width:100%;
            padding:10px;
            margin:10px 0;
        }

        button{
            padding:10px;
            background:green;
            color:white;
            border:none;
        }

        a{
            text-decoration:none;
        }
    </style>
</head>
<body>

<div class="box">

    <h2>Staff Add Member</h2>

    <p><a href="dashboard.php">← Back Dashboard</a></p>

    <form method="POST">

        <input type="text" name="membership_no" placeholder="Membership No" required>

        <input type="text" name="fullname" placeholder="Full Name" required>

        <select name="gender">
            <option>Male</option>
            <option>Female</option>
        </select>

        <input type="text" name="phone" placeholder="Phone">

        <input type="email" name="email" placeholder="Email">

        <textarea name="address" placeholder="Address"></textarea>

        <input type="date" name="join_date">

        <button type="submit" name="save">Save Member</button>

    </form>

</div>

</body>
</html>
