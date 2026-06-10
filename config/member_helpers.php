<?php

function get_current_member($conn) {
    if(!isset($_SESSION['user_id'])){
        return null;
    }

    $user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);
    $user_result = mysqli_query($conn, "SELECT member_id, fullname FROM users WHERE user_id='$user_id' LIMIT 1");
    $user = $user_result ? mysqli_fetch_assoc($user_result) : null;

    if($user && !empty($user['member_id'])){
        $member_id = mysqli_real_escape_string($conn, $user['member_id']);
        $member_result = mysqli_query($conn, "SELECT * FROM members WHERE member_id='$member_id' LIMIT 1");
        if($member_result && mysqli_num_rows($member_result) > 0){
            return mysqli_fetch_assoc($member_result);
        }
    }

    $fullname = mysqli_real_escape_string($conn, $_SESSION['fullname'] ?? ($user['fullname'] ?? ''));
    if($fullname !== ''){
        $member_result = mysqli_query($conn, "SELECT * FROM members WHERE fullname='$fullname' LIMIT 1");
        if($member_result && mysqli_num_rows($member_result) > 0){
            return mysqli_fetch_assoc($member_result);
        }
    }

    $member_result = mysqli_query($conn, "SELECT * FROM members ORDER BY member_id DESC LIMIT 1");
    return $member_result ? mysqli_fetch_assoc($member_result) : null;
}

function create_notification($conn, $member_id, $loan_id, $title, $message, $type) {
    $member_id = mysqli_real_escape_string($conn, $member_id);
    $loan_id_value = $loan_id ? "'" . mysqli_real_escape_string($conn, $loan_id) . "'" : "NULL";
    $title = mysqli_real_escape_string($conn, $title);
    $message = mysqli_real_escape_string($conn, $message);
    $type = mysqli_real_escape_string($conn, $type);

    return mysqli_query($conn, "INSERT INTO notifications (member_id, loan_id, title, message, type)
        VALUES ('$member_id', $loan_id_value, '$title', '$message', '$type')");
}

function ensure_repayment_reminders($conn, $member_id) {
    $member_id = mysqli_real_escape_string($conn, $member_id);
    $loans = mysqli_query($conn, "
        SELECT l.loan_id, l.amount, l.interest_rate, l.duration_months, l.approval_date,
               COALESCE(SUM(r.amount_paid), 0) AS total_paid
        FROM loans l
        LEFT JOIN repayments r ON l.loan_id = r.loan_id
        WHERE l.member_id='$member_id'
          AND l.status='Approved'
          AND l.approval_date IS NOT NULL
        GROUP BY l.loan_id
    ");

    if(!$loans){
        return;
    }

    while($loan = mysqli_fetch_assoc($loans)){
        $principal = (float)$loan['amount'];
        $interest = $principal * ((float)$loan['interest_rate'] / 100);
        $total_due = $principal + $interest;
        $balance = $total_due - (float)$loan['total_paid'];

        if($balance <= 0){
            continue;
        }

        $due_date = date('Y-m-d', strtotime($loan['approval_date'] . ' +' . (int)$loan['duration_months'] . ' months'));
        $days_left = floor((strtotime($due_date) - strtotime(date('Y-m-d'))) / 86400);

        if($days_left >= 0 && $days_left <= 7){
            $loan_id = mysqli_real_escape_string($conn, $loan['loan_id']);
            $existing = mysqli_query($conn, "SELECT notification_id FROM notifications
                WHERE member_id='$member_id' AND loan_id='$loan_id' AND type='Repayment Reminder'
                LIMIT 1");

            if($existing && mysqli_num_rows($existing) == 0){
                create_notification(
                    $conn,
                    $member_id,
                    $loan['loan_id'],
                    'Loan repayment reminder',
                    'Your loan repayment is due on ' . date('d/m/Y', strtotime($due_date)) . '. Balance: UGX ' . number_format($balance, 0) . '.',
                    'Repayment Reminder'
                );
            }
        }
    }
}
