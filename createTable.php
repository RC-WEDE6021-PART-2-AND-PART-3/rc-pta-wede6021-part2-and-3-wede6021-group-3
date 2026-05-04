<?php
// createTable.php - Creates tblUser table and loads data from userData.txt
include('DBConn.php');

echo "<h2>Clothing Store Database - Table Recreation</h2>";

// Disable foreign key checks temporarily
$conn->query("SET FOREIGN_KEY_CHECKS=0");

// Drop tblUser if exists
if ($conn->query("DROP TABLE IF EXISTS tblUser") === TRUE) {
    echo "✓ Old tblUser dropped successfully.<br>";
} else {
    echo "✗ Error dropping table: " . $conn->error . "<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS=1");

// Create tblUser with proper structure
$createTable = "CREATE TABLE IF NOT EXISTS tblUser (
    userID      INT AUTO_INCREMENT PRIMARY KEY,
    firstName   VARCHAR(50) NOT NULL,
    lastName    VARCHAR(50) NOT NULL,
    email       VARCHAR(100) UNIQUE NOT NULL,
    username    VARCHAR(50) UNIQUE NOT NULL,
    password    VARCHAR(255) NOT NULL,
    status      ENUM('pending','verified') DEFAULT 'pending',
    is_active   TINYINT(1) DEFAULT 1,
    createdAt   DATETIME DEFAULT CURRENT_TIMESTAMP,
    verified_at DATETIME NULL
)";

if ($conn->query($createTable) === TRUE) {
    echo "✓ tblUser created successfully.<br>";
} else {
    echo "✗ Error creating table: " . $conn->error . "<br>";
    $conn->close();
    exit();
}

// Load data from userData.txt
$file_path = "database/userData.txt";

// Check if file exists in different possible locations
if (!file_exists($file_path)) {
    // Try alternative paths
    if (file_exists("userData.txt")) {
        $file_path = "userData.txt";
    } elseif (file_exists("../database/userData.txt")) {
        $file_path = "../database/userData.txt";
    } else {
        echo "<br>✗ Error: userData.txt file not found!<br>";
        echo "<p>Please ensure userData.txt exists in the database folder or root directory.</p>";
        $conn->close();
        exit();
    }
}

$file = fopen($file_path, "r");

if ($file) {
    $success_count = 0;
    $error_count = 0;
    
    echo "<br><strong>Loading users from: " . basename($file_path) . "</strong><br><br>";
    
    while (($line = fgets($file)) !== false) {
        $line = trim($line);
        
        // Skip empty lines
        if (empty($line)) {
            continue;
        }
        
        $data = explode("|", $line);
        
        // Handle both 5-field and 6-field formats
        if (count($data) >= 5) {
            $firstName = $conn->real_escape_string($data[0]);
            $lastName  = $conn->real_escape_string($data[1]);
            $email     = $conn->real_escape_string($data[2]);
            $username  = $conn->real_escape_string($data[3]);
            $password  = $conn->real_escape_string($data[4]);
            
            // Check if status is provided (6th field), otherwise default to 'verified'
            $status = (count($data) >= 6) ? $conn->real_escape_string($data[5]) : 'verified';
            
            // Check if user already exists
            $check_sql = "SELECT userID FROM tblUser WHERE email = '$email' OR username = '$username'";
            $check_result = $conn->query($check_sql);
            
            if ($check_result->num_rows == 0) {
                $insert = "INSERT INTO tblUser 
                           (firstName, lastName, email, username, password, status) 
                           VALUES 
                           ('$firstName', '$lastName', '$email', '$username', '$password', '$status')";
                
                if ($conn->query($insert) === TRUE) {
                    $success_count++;
                    echo "✓ Inserted: $firstName $lastName (Username: $username, Status: $status)<br>";
                } else {
                    $error_count++;
                    echo "✗ Error inserting $firstName $lastName: " . $conn->error . "<br>";
                }
            } else {
                echo "⚠ Skipped duplicate: $firstName $lastName (already exists)<br>";
            }
        } else {
            echo "✗ Invalid line format: $line<br>";
        }
    }
    fclose($file);
    
    echo "<br><strong>Summary:</strong> $success_count users inserted successfully, $error_count errors.<br>";
    
} else {
    echo "<br>✗ Error opening file: $file_path<br>";
}

// Display current users in a nice table
echo "<h3>Current Users in tblUser:</h3>";
$result = $conn->query("SELECT userID, firstName, lastName, username, email, status, createdAt FROM tblUser ORDER BY userID");

if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #667eea; color: white;'>";
    echo "<th>ID</th><th>First Name</th><th>Last Name</th><th>Username</th><th>Email</th><th>Status</th><th>Created At</th>";
    echo "</tr>";
    
    while ($row = $result->fetch_assoc()) {
        $status_color = ($row['status'] == 'verified') ? '#48bb78' : '#ed8936';
        echo "<tr>";
        echo "<td>{$row['userID']}</td>";
        echo "<td>{$row['firstName']}</td>";
        echo "<td>{$row['lastName']}</td>";
        echo "<td>{$row['username']}</td>";
        echo "<td>{$row['email']}</td>";
        echo "<td style='color: $status_color; font-weight: bold;'>" . ucfirst($row['status']) . "</td>";
        echo "<td>{$row['createdAt']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show statistics
    $stats = $conn->query("SELECT status, COUNT(*) as count FROM tblUser GROUP BY status");
    echo "<br><strong>Statistics:</strong><br>";
    while ($stat = $stats->fetch_assoc()) {
        echo "• " . ucfirst($stat['status']) . " users: " . $stat['count'] . "<br>";
    }
    
} else {
    echo "<p>No users found in the database.</p>";
}

$conn->close();
?>