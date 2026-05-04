<?php


session_start();
include('DBConn.php');

if (!isset($_SESSION['adminID'])) {
    header("Location: adminLogin.php");
    exit();
}

$message = "";

// Verify user
if (isset($_GET['verify'])) {
    $verifyID = intval($_GET['verify']);
    $conn->query("UPDATE tblUser SET status='verified' WHERE userID=$verifyID");
    $message = "User verified successfully!";
}

// Delete user
if (isset($_GET['delete'])) {
    $deleteID = intval($_GET['delete']);
    $conn->query("DELETE FROM tblUser WHERE userID=$deleteID");
    $message = "User deleted successfully!";
}

// Add user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['addUser'])) {
    $firstName = $conn->real_escape_string(trim($_POST['firstName']));
    $lastName  = $conn->real_escape_string(trim($_POST['lastName']));
    $email     = $conn->real_escape_string(trim($_POST['email']));
    $username  = $conn->real_escape_string(trim($_POST['username']));
    $password  = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $sql = "INSERT INTO tblUser (firstName,lastName,email,username,password,status)
            VALUES ('$firstName','$lastName','$email','$username','$password','verified')";
    $message = $conn->query($sql) ? "User added!" : "Error: ".$conn->error;
}

// Update user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['updateUser'])) {
    $userID    = intval($_POST['userID']);
    $firstName = $conn->real_escape_string(trim($_POST['firstName']));
    $lastName  = $conn->real_escape_string(trim($_POST['lastName']));
    $email     = $conn->real_escape_string(trim($_POST['email']));
    $username  = $conn->real_escape_string(trim($_POST['username']));
    $status    = $conn->real_escape_string($_POST['status']);
    $sql = "UPDATE tblUser SET firstName='$firstName',lastName='$lastName',
            email='$email',username='$username',status='$status'
            WHERE userID=$userID";
    $message = $conn->query($sql) ? "User updated!" : "Error: ".$conn->error;
}

$users = $conn->query("SELECT * FROM tblUser ORDER BY status ASC, createdAt DESC");

$editUser = null;
if (isset($_GET['edit'])) {
    $editID     = intval($_GET['edit']);
    $editResult = $conn->query("SELECT * FROM tblUser WHERE userID=$editID");
    $editUser   = $editResult->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Pastimes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav>
    <a href="index.php" class="logo">PASTIMES</a>
    <ul>
        <li><a href="adminDashboard.php">Dashboard</a></li>
        <li><a href="adminLogout.php">Logout</a></li>
    </ul>
</nav>

<div class="user-bar">
    Admin: <?php echo htmlspecialchars($_SESSION['adminEmail']); ?> is logged in
</div>

<div class="container">
    <?php if (!empty($message)): ?>
        <div class="success-msg"><?php echo $message; ?></div>
    <?php endif; ?>

    <h2>Add New User</h2>
    <div class="card" style="max-width:100%; margin-bottom:30px;">
        <form method="POST" action="adminDashboard.php">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="firstName" required placeholder="First name">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="lastName" required placeholder="Last name">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required placeholder="Email">
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Username">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Password">
                </div>
            </div>
            <button type="submit" name="addUser" class="btn-primary"
                    style="max-width:200px;">Add User</button>
        </form>
    </div>

    <?php if ($editUser): ?>
    <h2>Edit User</h2>
    <div class="card" style="max-width:100%; margin-bottom:30px; border:2px solid #0077b6;">
        <form method="POST" action="adminDashboard.php">
            <input type="hidden" name="userID" value="<?php echo $editUser['userID']; ?>">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="firstName"
                           value="<?php echo htmlspecialchars($editUser['firstName']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="lastName"
                           value="<?php echo htmlspecialchars($editUser['lastName']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email"
                           value="<?php echo htmlspecialchars($editUser['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username"
                           value="<?php echo htmlspecialchars($editUser['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" style="width:100%; padding:12px;
                            border:1.5px solid #90e0ef; border-radius:8px;">
                        <option value="pending"
                            <?php echo $editUser['status']=='pending'?'selected':''; ?>>
                            Pending</option>
                        <option value="verified"
                            <?php echo $editUser['status']=='verified'?'selected':''; ?>>
                            Verified</option>
                    </select>
                </div>
            </div>
            <button type="submit" name="updateUser" class="btn-primary"
                    style="max-width:200px;">Update User</button>
            <a href="adminDashboard.php">
                <button type="button" class="btn-secondary"
                        style="max-width:200px;">Cancel</button>
            </a>
        </form>
    </div>
    <?php endif; ?>

    <h2>Manage Users</h2>
    <table class="data-table">
        <tr>
            <th>ID</th><th>Name</th><th>Email</th>
            <th>Username</th><th>Status</th><th>Actions</th>
        </tr>
        <?php while ($user = $users->fetch_assoc()): ?>
        <tr>
            <td><?php echo $user['userID']; ?></td>
            <td><?php echo htmlspecialchars($user['firstName'].' '.$user['lastName']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td>
                <?php if ($user['status']==='pending'): ?>
                    <span style="color:#cc0000; font-weight:600;">Pending</span>
                <?php else: ?>
                    <span style="color:#006600; font-weight:600;">Verified</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($user['status']==='pending'): ?>
                    <a href="adminDashboard.php?verify=<?php echo $user['userID']; ?>"
                       style="color:#0077b6; margin-right:10px; font-weight:600;">✓ Verify</a>
                <?php endif; ?>
                <a href="adminDashboard.php?edit=<?php echo $user['userID']; ?>"
                   style="color:#333; margin-right:10px;">✎ Edit</a>
                <a href="adminDashboard.php?delete=<?php echo $user['userID']; ?>"
                   style="color:red;"
                   onclick="return confirm('Delete this user?')">✕ Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>

<footer>
    <p>&copy; 2026 PASTIMES — Second-Hand Fashion</p>
</footer>
</body>
</html>