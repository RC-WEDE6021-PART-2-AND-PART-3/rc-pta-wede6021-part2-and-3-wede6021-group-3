<?php


session_start();
include('DBConn.php');

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID = $_SESSION['userID'];
$result = $conn->query("SELECT * FROM tblUser WHERE userID = $userID");
$user   = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Pastimes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav>
    <a href="index.php" class="logo">PASTIMES</a>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="listings.php">Listings</a></li>
        <li><a href="cart.php">Cart</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="user-bar">
    User <?php echo htmlspecialchars($user['firstName'].' '.$user['lastName']); ?> is logged in
</div>

<div class="container">
    <h2>My Account</h2>
    <table class="data-table">
        <tr><th>Field</th><th>Details</th></tr>
        <tr><td>First Name</td><td><?php echo htmlspecialchars($user['firstName']); ?></td></tr>
        <tr><td>Last Name</td><td><?php echo htmlspecialchars($user['lastName']); ?></td></tr>
        <tr><td>Email Address</td><td><?php echo htmlspecialchars($user['email']); ?></td></tr>
        <tr><td>Username</td><td><?php echo htmlspecialchars($user['username']); ?></td></tr>
        <tr><td>Account Status</td><td><?php echo ucfirst($user['status']); ?></td></tr>
        <tr><td>Member Since</td><td><?php echo date('d M Y', strtotime($user['createdAt'])); ?></td></tr>
    </table>
    <br>
    <a href="listings.php">
        <button class="btn-primary" style="max-width:250px;">Browse Listings</button>
    </a>
    &nbsp;
    <a href="logout.php">
        <button class="btn-secondary" style="max-width:250px;">Logout</button>
    </a>
</div>

<footer>
    <p>&copy; 2026 PASTIMES — Second-Hand Fashion</p>
</footer>
</body>
</html>