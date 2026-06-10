<?php
session_start();

if (isset($_GET['reset']) || isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// If user is NOT logged in → show landing page
if (!isset($_SESSION['user_id'])) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>KOTURA SACCO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(120deg, #1e3a8a, #3b82f6);
            color: white;
        }

        .header {
            padding: 20px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
        }

        .nav a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .hero {
            text-align: center;
            padding: 100px 20px;
        }

        .hero h1 {
            font-size: 50px;
            margin-bottom: 10px;
        }

        .hero p {
            font-size: 18px;
            opacity: 0.9;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            margin-top: 20px;
            background: white;
            color: #1e3a8a;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }

        .btn:hover {
            background: #e5e7eb;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 50px;
            background: white;
            color: #1e3a8a;
        }

        .feature {
            background: #f4f6f9;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background: #0f172a;
            color: white;
        }
    </style>
</head>

<body>

<div class="header">
    <div class="logo">🏦 KOTURA SACCO</div>
    <div class="nav">
        <a href="login.php">Login</a>
    </div>
</div>

<div class="hero">
    <h1>Save. Borrow. Grow.</h1>
    <p>Empowering members through savings and affordable loans.</p>

    <a href="login.php" class="btn">Login to Continue</a>
</div>

<div class="features">
    <div class="feature">💰 Savings System</div>
    <div class="feature">📄 Loan Management</div>
    <div class="feature">📊 Transparency</div>
    <div class="feature">🔔 Notifications</div>
</div>

<div class="footer">
    © <?php echo date("Y"); ?> KOTURA SACCO
</div>

</body>
</html>

<?php
exit();
}

// If logged in → redirect by role
if ($_SESSION['role_id'] == 1) {
    header("Location: admin/dashboard.php");
    exit();
}
elseif ($_SESSION['role_id'] == 2) {
    header("Location: staff/dashboard.php");
    exit();
}
else {
    header("Location: member/dashboard.php");
    exit();
}
?>
