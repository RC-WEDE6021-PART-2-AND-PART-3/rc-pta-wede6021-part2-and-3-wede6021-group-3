<?php


session_start();
include('DBConn.php');

if (isset($_SESSION['userID'])) {
    header("Location: index.php");
    exit();
}

$username = $email = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string(trim($_POST['username']));
    $email    = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $sql    = "SELECT * FROM tblUser 
                   WHERE username='$username' AND email='$email'";
        $result = $conn->query($sql);

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                if ($user['status'] === 'verified') {
                    $_SESSION['userID']    = $user['userID'];
                    $_SESSION['firstName'] = $user['firstName'];
                    $_SESSION['lastName']  = $user['lastName'];
                    $_SESSION['username']  = $user['username'];
                    $_SESSION['email']     = $user['email'];
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Your account is pending admin approval.";
                }
            } else {
                $error = "Incorrect password. Please try again.";
            }
        } else {
            $error = "No account found with those details.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Pastimes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav>
    <a href="index.php" class="logo">PASTIMES</a>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="listings.php">Listings</a></li>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
    </ul>
</nav>

<div class="page-wrapper">
    <div class="card">
        <h2>Welcome Back</h2>
        <p class="subtitle">Sign in to your Pastimes account</p>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username"
                       value="<?php echo htmlspecialchars($username); ?>"
                       required placeholder="Enter your username">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email"
                       value="<?php echo htmlspecialchars($email); ?>"
                       required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password"
                       required placeholder="Enter your password">
            </div>
            <button type="submit" class="btn-primary">Login</button>
            <a href="register.php">
                <button type="button" class="btn-secondary">Create Account</button>
            </a>
        </form>

        <div class="card-link">
            Admin? <a href="adminLogin.php">Login here</a>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2026 PASTIMES — Second-Hand Fashion</p>
</footer>
</body>
</html>