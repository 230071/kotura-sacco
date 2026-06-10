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

$error_message = "";
$success_message = "";

if(isset($_POST['save'])){
    $amount = (float)$_POST['amount'];
    $savings_date = mysqli_real_escape_string($conn, $_POST['savings_date']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $provider = mysqli_real_escape_string($conn, $_POST['provider']);
    $recorded_by = mysqli_real_escape_string($conn, $_SESSION['user_id']);

    if($amount <= 0){
        $error_message = "Savings amount must be greater than zero.";
    } else {
        $reference = "SAV-" . $member_id . "-" . time();

        // Start mobile money transaction
        $momo_query = "INSERT INTO mobile_money_transactions
            (member_id, phone, amount, provider, payment_type, status, transaction_ref, transaction_date)
            VALUES ('$member_id', '$phone', '$amount', '$provider', 'Savings', 'Success', '$reference', '$savings_date')";

        if(mysqli_query($conn, $momo_query)){
            // Insert into savings
            $savings_query = "INSERT INTO savings (member_id, amount, savings_date, recorded_by)
                              VALUES ('$member_id', '$amount', '$savings_date', '$recorded_by')";

            if(mysqli_query($conn, $savings_query)){
                create_notification(
                    $conn,
                    $member_id,
                    null,
                    "Savings deposited",
                    "Your deposit of UGX " . number_format($amount, 0) . " via " . $provider . " MoMo was received. Reference: " . $reference . ".",
                    "Savings Deposit"
                );
                $success_message = "Savings deposited and recorded successfully. Reference: " . $reference;
            } else {
                $error_message = "Mobile money transaction succeeded, but could not save savings: " . mysqli_error($conn);
            }
        } else {
            $error_message = "Mobile money transaction simulation failed: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Savings - KOTURA SACCO</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <style>
        body { font-family: Arial; background: #f4f6f9; padding: 20px; }
        .box { background: white; padding: 30px; max-width: 600px; margin: auto; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        input, select, button { width: 100%; padding: 10px; margin: 8px 0 15px; box-sizing: border-box; }
        button { background: #1e3a8a; color: white; border: none; cursor: pointer; border-radius: 5px; font-weight: bold; }
        button:hover { background: #1e40af; }
        .success { background: #e8f5e9; color: #1b5e20; padding: 12px; border-radius: 5px; margin-bottom: 15px; border-left: 4px solid green; }
        .error { background: #ffebee; color: #b71c1c; padding: 12px; border-radius: 5px; margin-bottom: 15px; border-left: 4px solid red; }
        .info-box { background: #f8fafc; padding: 12px; border-left: 4px solid #1e3a8a; margin-bottom: 15px; font-size: 13px; color: #666; }
        a { text-decoration: none; color: #1e3a8a; font-weight: bold; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="box">
    <p><a href="dashboard.php">← Back to Dashboard</a></p>
    <h2>Deposit Personal Savings</h2>

    <?php if($success_message){ ?><div class="success">✓ <?php echo htmlspecialchars($success_message); ?></div><?php } ?>
    <?php if($error_message){ ?><div class="error">✗ <?php echo htmlspecialchars($error_message); ?></div><?php } ?>

    <form method="POST">
        <div class="info-box" style="background: #eff6ff; border-left: 4px solid #3b82f6; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
            <h4 style="margin: 0 0 8px 0; color: #1e3a8a; font-size: 15px;">📲 MTN MoMo / Airtel Money Deposit Instructions</h4>
            <p style="margin: 5px 0; font-size: 13px; color: #334155;">To deposit savings manually, follow these steps:</p>
            <ol style="margin: 5px 0; padding-left: 20px; font-size: 13px; color: #334155;">
                <li>Dial <strong>*165#</strong> (MTN) or <strong>*185#</strong> (Airtel).</li>
                <li>Send Money to Phone Number: <strong>0771638254</strong></li>
                <li>Registered Name: <strong>Mindra George Bush</strong></li>
                <li>Reference/Reason Code: Enter your Member ID: <strong><?php echo htmlspecialchars($member['member_id']); ?></strong></li>
            </ol>
            <p style="margin: 8px 0 0 0; font-size: 12px; color: #475569; font-style: italic;">
                * After sending the money, fill in the form below with the transaction details to record your deposit.
            </p>
        </div>

        <label>Mobile Money Provider</label>
        <select name="provider" required>
            <option value="MTN">MTN Mobile Money</option>
            <option value="Airtel">Airtel Money</option>
        </select>

        <label>Phone Number</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($member['phone'] ?? ''); ?>" placeholder="07XXXXXXXX" required>

        <label>Amount (UGX)</label>
        <input type="number" name="amount" placeholder="e.g. 50000" min="1" step="1" required>

        <label>Deposit Date</label>
        <input type="date" name="savings_date" value="<?php echo date('Y-m-d'); ?>" required>

        <button type="submit" name="save">Deposit Now</button>
    </form>
</div>

</body>
</html>
