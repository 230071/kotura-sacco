<?php
session_start();
include("../config/database.php");

// Allow only Admin (1)
if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1){
    header("Location: ../login.php");
    exit();
}

$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : null;

if(!$id){
    $_SESSION['error'] = "No member selected!";
    header("Location: members.php");
    exit();
}

// Fetch member details
$result = mysqli_query($conn, "SELECT * FROM members WHERE member_id='$id'");
if(!$result){
    $_SESSION['error'] = "Database error: " . mysqli_error($conn);
    header("Location: members.php");
    exit();
}

$member = mysqli_fetch_assoc($result);

if(!$member){
    $_SESSION['error'] = "Member not found!";
    header("Location: members.php");
    exit();
}

// Handle form submission
$error_message = '';
$success_message = '';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $fullname = mysqli_real_escape_string($conn, $_POST['fullname'] ?? '');
    $gender = mysqli_real_escape_string($conn, $_POST['gender'] ?? '');
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');

    // Validate inputs
    if(empty($fullname) || empty($phone) || empty($email)){
        $error_message = "All fields are required!";
    } else {
        $sql = "UPDATE members 
                SET fullname='$fullname', 
                    gender='$gender', 
                    phone='$phone', 
                    email='$email'
                WHERE member_id='$id'";

        if(mysqli_query($conn, $sql)){
            $success_message = "Member updated successfully!";
            // Refresh member data
            $result = mysqli_query($conn, "SELECT * FROM members WHERE member_id='$id'");
            $member = mysqli_fetch_assoc($result);
        } else {
            $error_message = "Error updating member: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Member</title>

    <style>
        body{
            font-family: Arial;
            background:#f4f6f9;
            padding:20px;
        }

        .container{
            background:white;
            padding:30px;
            border-radius:10px;
            max-width:600px;
            margin: 0 auto;
        }

        h2{
            color:#1e3a8a;
        }

        .form-group{
            margin-bottom:20px;
        }

        label{
            display:block;
            margin-bottom:5px;
            color:#333;
            font-weight:bold;
        }

        input, select{
            width:100%;
            padding:10px;
            border:1px solid #ddd;
            border-radius:5px;
            box-sizing:border-box;
            font-size:14px;
        }

        input:focus, select:focus{
            outline:none;
            border-color:#1e3a8a;
            box-shadow: 0 0 5px rgba(30, 58, 138, 0.3);
        }

        input[readonly]{
            background:#f5f5f5;
            cursor:not-allowed;
        }

        .button-group{
            display:flex;
            gap:10px;
            margin-top:30px;
        }

        button{
            flex:1;
            padding:12px;
            border:none;
            border-radius:5px;
            cursor:pointer;
            font-size:16px;
            font-weight:bold;
        }

        .btn-save{
            background:#1e3a8a;
            color:white;
        }

        .btn-save:hover{
            background:#1e40af;
        }

        .btn-cancel{
            background:#ddd;
            color:#333;
        }

        .btn-cancel:hover{
            background:#ccc;
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

        .back-link{
            margin-bottom: 20px;
        }

        .back-link a{
            color: #1e3a8a;
            text-decoration: none;
            font-size: 16px;
        }

        .back-link a:hover{
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="back-link">
        <a href="members.php">← Back to Members</a>
    </div>

    <?php if($success_message): ?>
        <div class="message success">✓ <?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if($error_message): ?>
        <div class="message error">✗ <?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <h2>Edit Member</h2>

    <form method="POST">

        <div class="form-group">
            <label for="membership_no">Membership No:</label>
            <input type="text" id="membership_no" value="<?php echo htmlspecialchars($member['membership_no']); ?>" readonly>
        </div>

        <div class="form-group">
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($member['fullname']); ?>" required>
        </div>

        <div class="form-group">
            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male" <?php echo $member['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo $member['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
            </select>
        </div>

        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($member['phone']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>" required>
        </div>

        <div class="form-group">
            <label>Join Date:</label>
            <input type="text" value="<?php echo htmlspecialchars($member['join_date']); ?>" readonly>
        </div>

        <div class="button-group">
            <button type="submit" class="btn-save">💾 Save Changes</button>
            <button type="button" class="btn-cancel" onclick="window.location.href='members.php';">✕ Cancel</button>
        </div>

    </form>

</div>

</body>
</html>
