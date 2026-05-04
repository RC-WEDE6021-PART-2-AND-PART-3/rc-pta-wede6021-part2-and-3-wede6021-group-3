<?php


session_start();
include('DBConn.php');

$firstName = $lastName = $email = $username = "";
$error = $success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName       = $conn->real_escape_string(trim($_POST['firstName']));
    $lastName        = $conn->real_escape_string(trim($_POST['lastName']));
    $email           = $conn->real_escape_string(trim($_POST['email']));
    $username        = $conn->real_escape_string(trim($_POST['username']));
    $password        = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    if (empty($firstName) || empty($lastName) || empty($email) || 
        empty($username) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->query("SELECT userID FROM tblUser 
                               WHERE email='$email' OR username='$username'");
        if ($check->num_rows > 0) {
            $error = "Email or username already exists.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO tblUser
                    (firstName, lastName, email, username, password, status)
                    VALUES
                    ('$firstName','$lastName','$email',
                     '$username','$hashedPassword','pending')";
            if ($conn->query($sql) === TRUE) {
                $success   = "Registration successful! Please wait for admin approval.";
                $firstName = $lastName = $email = $username = "";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Pastimes</title>
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
        <h2>Create Account</h2>
        <p class="subtitle">Join Pastimes — Second-Hand Fashion</p>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="success-msg"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="firstName"
                       value="<?php echo htmlspecialchars($firstName); ?>"
                       required placeholder="Enter your first name">
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" name="lastName"
                       value="<?php echo htmlspecialchars($lastName); ?>"
                       required placeholder="Enter your last name">
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email"
                       value="<?php echo htmlspecialchars($email); ?>"
                       required placeholder="Enter your email">
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username"
                       value="<?php echo htmlspecialchars($username); ?>"
                       required placeholder="Choose a username">
            </div>
            <div class="form-group">
                <label>Password (min. 8 characters)</label>
                <input type="password" name="password"
                       required placeholder="Create a password">
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirmPassword"
                       required placeholder="Confirm your password">
            </div>
            <button type="submit" class="btn-primary">Register</button>
        </form>

        <div class="card-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2026 PASTIMES — Second-Hand Fashion</p>
</footer>
</body>
</html>