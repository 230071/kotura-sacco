<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
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

    if(mysqli_query($conn,$sql)){
        echo "<script>alert('Member added successfully');</script>";
    }else{
        echo "<script>alert('Error adding member');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Member</title>
</head>
<body>

<h2>Add New Member</h2>

<form method="POST">

    <label>Membership Number</label><br>
    <input type="text" name="membership_no" required><br><br>

    <label>Full Name</label><br>
    <input type="text" name="fullname" required><br><br>

    <label>Gender</label><br>
    <select name="gender">
        <option>Male</option>
        <option>Female</option>
    </select><br><br>

    <label>Phone</label><br>
    <input type="text" name="phone"><br><br>

    <label>Email</label><br>
    <input type="email" name="email"><br><br>

    <label>Address</label><br>
    <textarea name="address"></textarea><br><br>

    <label>Join Date</label><br>
    <input type="date" name="join_date"><br><br>

    <button type="submit" name="save">Save Member</button>
    <a href="add_member.php">Add Member</a>

</form>

</body>
</html>
