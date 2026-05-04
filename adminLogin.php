<?php


session_start();
include('DBConn.php');

if (isset($_SESSION['adminID'])) {
    header("Location: adminDashboard.php");
    exit();
}

$adminEmail = "";
$error      = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $adminEmail = $conn->real_escape_string(trim($_POST['adminEmail']));
    $password   = trim($_POST['password']);

    if (empty($adminEmail) || empty($password)) {
        $error = "All fields are required.";
    } else {
        $result = $conn->query("SELECT * FROM tblAdmin 
                                WHERE adminEmail='$adminEmail'");
        if ($result->num_rows === 1) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                $_SESSION['adminID']    = $admin['adminID'];
                $_SESSION['adminEmail'] = $admin['adminEmail'];
                header("Location: adminDashboard.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No admin account found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Pastimes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav>
    <a href="index.php" class="logo">PASTIMES</a>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="login.php">User Login</a></li>
    </ul>
</nav>

<div class="page-wrapper">
    <div class="card">
        <h2>Admin Login</h2>
        <p class="subtitle">Pastimes Administrator Access</p>

        <?php if (!empty($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="adminLogin.php">
            <div class="form-group">
                <label>Admin Email</label>
                <input type="email" name="adminEmail"
                       value="<?php echo htmlspecialchars($adminEmail); ?>"
                       required placeholder="Enter admin email">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password"
                       required placeholder="Enter admin password">
            </div>
            <button type="submit" class="btn-primary">Login as Admin</button>
        </form>

        <div class="card-link">
            Not an admin? <a href="login.php">User login here</a>
        </div>
    </div>
</div>

<footer>
    <p>&copy; 2026 PASTIMES — Second-Hand Fashion</p>
</footer>
</body>
</html>