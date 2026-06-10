<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['user_id']) || ($_SESSION['role_id'] != 1 && $_SESSION['role_id'] != 2)){
    header("Location: ../login.php");
    exit();
}

// Fetch members
$members = mysqli_query($conn, "SELECT member_id, fullname FROM members");

$error_message = "";
$success_message = "";
$member_info = null;

if(isset($_POST['apply'])){
    $member_id = $_POST['member_id'];
    $amount = $_POST['amount'];
    $duration = $_POST['duration'];
    $purpose = $_POST['purpose'];
    $date = date("Y-m-d");

    // Get member's savings
    $savings_query = "SELECT COALESCE(SUM(amount), 0) as total_savings FROM savings WHERE member_id = '$member_id'";
    $savings_result = mysqli_query($conn, $savings_query);
    $savings_data = mysqli_fetch_assoc($savings_result);
    $total_savings = $savings_data['total_savings'];
    $max_loan_amount = $total_savings * 2;

    // Determine interest rate
    $interest_rate = ($total_savings > 0) ? 10 : 15;

    // Validate loan amount
    if($total_savings == 0){
        $error_message = "This member has no savings. They must save before qualifying for a loan.";
    } else if($amount > $max_loan_amount){
        $error_message = "Loan amount cannot exceed UGX " . number_format($max_loan_amount, 0) . " (2x their savings of UGX " . number_format($total_savings, 0) . ")";
    } else if($amount <= 0){
        $error_message = "Loan amount must be greater than 0";
    } else {
        $sql = "INSERT INTO loans
        (member_id, amount, interest_rate, duration_months, purpose, application_date, status)
        VALUES
        ('$member_id','$amount','$interest_rate','$duration','$purpose','$date','Pending')";

        if(mysqli_query($conn, $sql)){
            $success_message = "✅ Loan application submitted successfully!";
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
}

// If member selected, get their info
if(isset($_POST['get_member_info'])){
    $member_id = $_POST['member_id'];
    $info_query = "SELECT member_id, fullname FROM members WHERE member_id = '$member_id'";
    $info_result = mysqli_query($conn, $info_query);
    $member_info = mysqli_fetch_assoc($info_result);

    if($member_info){
        $savings_query = "SELECT COALESCE(SUM(amount), 0) as total_savings FROM savings WHERE member_id = '$member_id'";
        $savings_result = mysqli_query($conn, $savings_query);
        $savings_data = mysqli_fetch_assoc($savings_result);
        $member_info['total_savings'] = $savings_data['total_savings'];
        $member_info['max_loan'] = $savings_data['total_savings'] * 2;
        $member_info['interest'] = ($savings_data['total_savings'] > 0) ? 10 : 15;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Process Loan Application</title>

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

        .info-box { background: #e3f2fd; padding: 15px; border-left: 4px solid #1e3a8a; margin-bottom: 20px; border-radius: 5px; }
        .info-box strong { color: #1e3a8a; }
        .error { color: #d32f2f; background: #ffebee; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .success { color: #388e3c; background: #e8f5e9; padding: 10px; border-radius: 5px; margin-bottom: 15px; }

        input, select, textarea{
            width:100%;
            padding:10px;
            margin:10px 0;
            box-sizing: border-box;
        }

        button{
            background:#1e3a8a;
            color:white;
            padding:10px;
            width:100%;
            border:none;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover { background: #1e40af; }
        button:disabled { background: #ccc; cursor: not-allowed; }
    </style>
</head>

<body>

<div class="box">

    <h2>Process Loan Application</h2>
    <p><a href="dashboard.php">← Back Dashboard</a></p>

    <?php if($error_message): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if($success_message): ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Select Member</label>
        <select name="member_id" required onchange="this.form.submit();">
            <option value="">-- Select Member --</option>
            <?php while($row = mysqli_fetch_assoc($members)) { ?>
                <option value="<?php echo $row['member_id']; ?>" <?php echo (isset($_POST['member_id']) && $_POST['member_id'] == $row['member_id']) ? 'selected' : ''; ?>>
                    <?php echo $row['fullname']; ?>
                </option>
            <?php } ?>
        </select>
        <input type="hidden" name="get_member_info" value="1">
    </form>

    <?php if($member_info): ?>
        <div class="info-box">
            <strong>💰 Member Information:</strong><br>
            Member: <?php echo $member_info['fullname']; ?><br>
            Total Savings: UGX <?php echo number_format($member_info['total_savings'], 0); ?><br>
            Max Loan Available: UGX <?php echo number_format($member_info['max_loan'], 0); ?><br>
            Interest Rate: <strong><?php echo $member_info['interest']; ?>%</strong>
            <?php if($member_info['total_savings'] == 0): ?>
                <br><span style="color: #d32f2f;">⚠️ This member has no savings and cannot qualify for a loan!</span>
            <?php endif; ?>
        </div>

        <form method="POST">
            <input type="hidden" name="member_id" value="<?php echo $member_info['member_id']; ?>">

            <label>Loan Amount (UGX) - Max: UGX <?php echo number_format($member_info['max_loan'], 0); ?></label>
            <input type="number" name="amount" placeholder="Amount" step="1" min="1" max="<?php echo $member_info['max_loan']; ?>" required <?php echo ($member_info['total_savings'] == 0) ? 'disabled' : ''; ?>>

            <label>Purpose</label>
            <textarea name="purpose" placeholder="Reason for loan" required <?php echo ($member_info['total_savings'] == 0) ? 'disabled' : ''; ?>></textarea>

            <label>Duration (months)</label>
            <input type="number" name="duration" placeholder="e.g., 12" min="1" required <?php echo ($member_info['total_savings'] == 0) ? 'disabled' : ''; ?>>

            <button type="submit" name="apply" <?php echo ($member_info['total_savings'] == 0) ? 'disabled' : ''; ?>>Submit Loan Application</button>
        </form>
    <?php endif; ?>

</div>

</body>
</html>
            <option value="">-- Select Member --</option>

            <?php while($row = mysqli_fetch_assoc($members)) { ?>
                <option value="<?php echo $row['member_id']; ?>">
                    <?php echo $row['fullname']; ?>
                </option>
            <?php } ?>

        </select>

        <label>Amount</label>
        <input type="number" name="amount" required>

        <label>Interest Rate (%)</label>
        <input type="number" name="interest_rate" value="10">

        <label>Duration (Months)</label>
        <input type="number" name="duration">

        <label>Purpose</label>
        <textarea name="purpose"></textarea>

        <button type="submit" name="apply">Submit Loan</button>

    </form>

</div>

</body>
</html>
