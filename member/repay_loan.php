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

$member_id = mysqli_real_escape_string($conn, $member['member_id']);
ensure_repayment_reminders($conn, $member_id);

$error_message = "";
$success_message = "";

if(isset($_POST['pay'])){
    $loan_id = mysqli_real_escape_string($conn, $_POST['loan_id']);
    $amount = (float)$_POST['amount'];
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $provider = mysqli_real_escape_string($conn, $_POST['provider']);
    $today = date('Y-m-d');

    $loan_result = mysqli_query($conn, "
        SELECT l.*, COALESCE(SUM(r.amount_paid), 0) AS total_paid
        FROM loans l
        LEFT JOIN repayments r ON l.loan_id = r.loan_id
        WHERE l.loan_id='$loan_id' AND l.member_id='$member_id' AND l.status='Approved'
        GROUP BY l.loan_id
        LIMIT 1
    ");
    $loan = $loan_result ? mysqli_fetch_assoc($loan_result) : null;

    if(!$loan){
        $error_message = "Selected loan was not found.";
    } else {
        $total_due = (float)$loan['amount'] + ((float)$loan['amount'] * ((float)$loan['interest_rate'] / 100));
        $balance = $total_due - (float)$loan['total_paid'];

        if($amount <= 0){
            $error_message = "Payment amount must be greater than zero.";
        } else if($amount > $balance){
            $error_message = "Payment cannot exceed the remaining balance of UGX " . number_format($balance, 0) . ".";
        } else {
            $reference = "LOAN-" . $loan_id . "-" . time();
            $recorded_by = mysqli_real_escape_string($conn, $_SESSION['user_id']);

            $momo_query = "INSERT INTO mobile_money_transactions
                (member_id, phone, amount, provider, payment_type, loan_id, status, transaction_ref, transaction_date)
                VALUES ('$member_id', '$phone', '$amount', '$provider', 'Loan Repayment', '$loan_id', 'Success', '$reference', '$today')";

            if(mysqli_query($conn, $momo_query)){
                if(mysqli_query($conn, "INSERT INTO repayments (loan_id, amount_paid, payment_date, recorded_by)
                    VALUES ('$loan_id', '$amount', '$today', '$recorded_by')")){
                    create_notification(
                        $conn,
                        $member_id,
                        $loan_id,
                        "Repayment received",
                        "Your repayment of UGX " . number_format($amount, 0) . " was received by " . $provider . ". Reference: " . $reference . ".",
                        "Repayment Received"
                    );
                    $success_message = "Payment processed successfully. Reference: " . $reference;
                } else {
                    $error_message = "Mobile money transaction succeeded, but could not save repayment: " . mysqli_error($conn);
                }
            } else {
                $error_message = "Mobile money transaction simulation failed: " . mysqli_error($conn);
            }
        }
    }
}

$loans = mysqli_query($conn, "
    SELECT l.*, COALESCE(SUM(r.amount_paid), 0) AS total_paid
    FROM loans l
    LEFT JOIN repayments r ON l.loan_id = r.loan_id
    WHERE l.member_id='$member_id' AND l.status='Approved'
    GROUP BY l.loan_id
    ORDER BY l.loan_id DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pay Loan - KOTURA SACCO</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        body{ font-family:Arial; background:#f4f6f9; padding:20px; }
        .box{ max-width:800px; margin:auto; background:white; padding:20px; border-radius:10px; }
        input, select, button{ width:100%; padding:10px; margin:8px 0 15px; box-sizing:border-box; }
        button{ background:#1e3a8a; color:white; border:0; border-radius:5px; cursor:pointer; }
        .success{ background:#e8f5e9; color:#1b5e20; padding:10px; border-radius:5px; }
        .error{ background:#ffebee; color:#b71c1c; padding:10px; border-radius:5px; }
        .loan-summary{ background:#f8fafc; padding:12px; border-left:4px solid #1e3a8a; margin-bottom:15px; }
        a{ color:#1e3a8a; text-decoration:none; }
    </style>
</head>
<body>
<div class="box">
    <p><a href="dashboard.php">&larr; Back to Dashboard</a></p>
    <h2>Pay Loan</h2>

    <?php if($success_message){ ?><div class="success"><?php echo htmlspecialchars($success_message); ?></div><?php } ?>
    <?php if($error_message){ ?><div class="error"><?php echo htmlspecialchars($error_message); ?></div><?php } ?>

    <?php if($loans && mysqli_num_rows($loans) > 0){ ?>
        <form method="POST">
            <label>Select Loan</label>
            <select name="loan_id" required>
                <option value="">-- Select approved loan --</option>
                <?php while($loan = mysqli_fetch_assoc($loans)){
                    $total_due = (float)$loan['amount'] + ((float)$loan['amount'] * ((float)$loan['interest_rate'] / 100));
                    $balance = $total_due - (float)$loan['total_paid'];
                    if($balance <= 0){
                        continue;
                    }
                ?>
                    <option value="<?php echo htmlspecialchars($loan['loan_id']); ?>">
                        Loan #<?php echo htmlspecialchars($loan['loan_id']); ?> - Balance UGX <?php echo number_format($balance, 0); ?>
                    </option>
                <?php } ?>
            </select>

            <div class="loan-summary" style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
                <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 15px;">📲 MTN MoMo / Airtel Money Payment Instructions</h4>
                <p style="margin: 5px 0; font-size: 13px; color: #334155;">To pay manually, follow these steps:</p>
                <ol style="margin: 5px 0; padding-left: 20px; font-size: 13px; color: #334155;">
                    <li>Dial <strong>*165#</strong> (MTN) or <strong>*185#</strong> (Airtel).</li>
                    <li>Send Money to Phone Number: <strong>0771638254</strong></li>
                    <li>Registered Name: <strong>Mindra George Bush</strong></li>
                    <li>Reference/Reason Code: Enter your Member ID: <strong><?php echo htmlspecialchars($member['member_id']); ?></strong></li>
                </ol>
                <p style="margin: 8px 0 0 0; font-size: 12px; color: #475569; font-style: italic;">
                    * After sending the money, fill in the form below with the transaction details to record your repayment.
                </p>
            </div>

            <label>Mobile Money Provider</label>
            <select name="provider" required>
                <option value="MTN">MTN Mobile Money</option>
                <option value="Airtel">Airtel Money</option>
            </select>

            <label>Phone Number</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($member['phone']); ?>" required>

            <label>Amount (UGX)</label>
            <input type="number" name="amount" min="1" step="1" required>

            <button type="submit" name="pay">Pay Now</button>
        </form>
    <?php } else { ?>
        <p>You do not have an approved loan to repay.</p>
    <?php } ?>
</div>
</body>
</html>
