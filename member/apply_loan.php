<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3){
    header("Location: ../login.php");
    exit();
}

include("../config/database.php");
include("../config/member_helpers.php");

$member = get_current_member($conn);
if(!$member){
    die("Member profile not found.");
}
$member_id = $member['member_id'];

// Get member's total savings
$savings_query = "SELECT COALESCE(SUM(amount), 0) as total_savings FROM savings WHERE member_id = '$member_id'";
$savings_result = mysqli_query($conn, $savings_query);
$savings_data = mysqli_fetch_assoc($savings_result);
$total_savings = $savings_data['total_savings'];
$max_loan_amount = $total_savings * 2; // Can borrow up to 2x their savings

// Determine interest rate (10% for members with savings, 15% for non-members)
$interest_rate = ($total_savings > 0) ? 10 : 15;

$error_message = "";
$success_message = "";

if(isset($_POST['save'])){
    $loan_amount = $_POST['loan_amount'];
    $loan_purpose = $_POST['loan_purpose'];
    $duration_months = $_POST['repayment_period'];

    // Validate loan amount
    if($loan_amount > $max_loan_amount && $max_loan_amount > 0){
        $error_message = "Loan amount cannot exceed UGX " . number_format($max_loan_amount, 0) . " (2x your savings)";
    } else if($max_loan_amount == 0 && $loan_amount > 0){
        $error_message = "You must have savings first to qualify for a loan. Please record your savings first.";
    } else if($loan_amount <= 0){
        $error_message = "Loan amount must be greater than 0";
    } else {
        $sql = "INSERT INTO loans (member_id, amount, purpose, status, application_date, interest_rate, duration_months)
                VALUES ('$member_id', '$loan_amount', '$loan_purpose', 'Pending', NOW(), '$interest_rate', '$duration_months')";

        if(mysqli_query($conn, $sql)){
            $success_message = "✅ Loan application submitted successfully!";
            echo "<script>setTimeout(function() { window.location='loan_status.php'; }, 2000);</script>";
        } else {
            $error_message = "Error submitting application: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Loan - KOTURA SACCO</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        body { font-family: Arial; background: #f4f6f9; padding: 20px; }
        .box { background: white; padding: 30px; max-width: 600px; margin: auto; border-radius: 10px; }
        .info-box { background: #e3f2fd; padding: 15px; border-left: 4px solid #1e3a8a; margin-bottom: 20px; border-radius: 5px; }
        .info-box strong { color: #1e3a8a; }
        .error { color: #d32f2f; background: #ffebee; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        .success { color: #388e3c; background: #e8f5e9; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
        input, select, textarea { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { padding: 10px 20px; background: #1e3a8a; color: white; border: none; cursor: pointer; border-radius: 5px; width: 100%; }
        button:hover { background: #1e40af; }
        a { text-decoration: none; color: #1e3a8a; }
    </style>
</head>
<body>

<div class="box">
    <h2>Apply for Loan</h2>
    <p><a href="dashboard.php">← Back to Dashboard</a></p>

    <?php if($error_message): ?>
        <div class="error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if($success_message): ?>
        <div class="success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <div class="info-box">
        <strong>💰 Your Savings Information:</strong><br>
        Total Savings: UGX <?php echo number_format($total_savings, 0); ?><br>
        Maximum Loan Amount: UGX <?php echo number_format($max_loan_amount, 0); ?><br>
        Interest Rate: <strong><?php echo $interest_rate; ?>%</strong>
        <?php if($total_savings == 0): ?>
            <br><span style="color: #d32f2f;">⚠️ You need to save money first to qualify for a loan!</span>
        <?php endif; ?>
    </div>

    <form method="POST">
        <label>Loan Amount (UGX) - Max: <?php echo number_format($max_loan_amount, 0); ?></label>
        <input type="number" name="loan_amount" placeholder="Amount" step="1" min="1" max="<?php echo $max_loan_amount; ?>" required>

        <label>Purpose</label>
        <textarea name="loan_purpose" placeholder="Reason for loan" required></textarea>

        <label>Loan Duration (months)</label>
        <input type="number" name="repayment_period" placeholder="e.g., 12" min="1" required>

        <button type="submit" name="save" <?php echo ($max_loan_amount == 0) ? 'disabled' : ''; ?>>Submit Application</button>
    </form>
</div>

</body>
</html>
