<?php

// loadClothingStore.php - Drops and recreates all ClothingStore tables

include('DBConn.php');

// Disable foreign key checks before dropping
$conn->query("SET FOREIGN_KEY_CHECKS=0");

//  DROP ALL TABLES FIRST
$tables = ["tblCart", "tblAorder", "tblClothes", "tblAdmin", "tblUser"];
foreach ($tables as $table) {
    $conn->query("DROP TABLE IF EXISTS $table");
    echo "Dropped $table if it existed.<br>";
}

// Re-enable foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS=1");

//  CREATE tblUser 
$sql = "CREATE TABLE IF NOT EXISTS tblUser (
    userID INT AUTO_INCREMENT PRIMARY KEY,
    firstName VARCHAR(50) NOT NULL,
    lastName VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    status ENUM('pending','verified') DEFAULT 'pending',
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
)";
echo $conn->query($sql) ? "tblUser created.<br>" : "Error: " . $conn->error . "<br>";

//  CREATE tblAdmin 
$sql = "CREATE TABLE IF NOT EXISTS tblAdmin (
    adminID INT AUTO_INCREMENT PRIMARY KEY,
    adminEmail VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
)";
echo $conn->query($sql) ? "tblAdmin created.<br>" : "Error: " . $conn->error . "<br>";

//  CREATE tblClothes
$sql = "CREATE TABLE IF NOT EXISTS tblClothes (
    clothesID INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    brand VARCHAR(50) NOT NULL,
    size VARCHAR(10) NOT NULL,
    `condition` VARCHAR(20) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    status ENUM('available','sold') DEFAULT 'available',
    createdAt DATETIME DEFAULT CURRENT_TIMESTAMP
)";
echo $conn->query($sql) ? "tblClothes created.<br>" : "Error: " . $conn->error . "<br>";

//  CREATE tblAorder 
$sql = "CREATE TABLE IF NOT EXISTS tblAorder (
    orderID INT AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    clothesID INT NOT NULL,
    orderDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    totalAmount DECIMAL(10,2) NOT NULL,
    addressType ENUM('residential','work') NOT NULL,
    streetAddress VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    postalCode VARCHAR(10) NOT NULL,
    FOREIGN KEY (userID) REFERENCES tblUser(userID),
    FOREIGN KEY (clothesID) REFERENCES tblClothes(clothesID)
)";
echo $conn->query($sql) ? "tblAorder created.<br>" : "Error: " . $conn->error . "<br>";

//  INSERT DEFAULT ADMIN 
$adminEmail = "admin@pastimes.co.za";
$adminPassword = password_hash("Admin@123", PASSWORD_DEFAULT);
$sql = "INSERT IGNORE INTO tblAdmin (adminEmail, password) 
        VALUES ('$adminEmail', '$adminPassword')";
echo $conn->query($sql) ? "Default admin inserted.<br>" : "Error: " . $conn->error . "<br>";

//  INSERT SAMPLE CLOTHES 
// Insert sample clothes
$clothes = [
    ["Ralph Lauren V-Neck Tee", "Ralph Lauren", "M", "Good", 280.00, "ralph_tee.jpg"],
    ["Khaite Black Blazer", "Khaite", "S", "New", 850.00, "khaite_blazer.jpg"],
    ["Leather Bomber Jacket", "Aelfric Eden", "L", "Good", 650.00, "leather_jacket.jpg"],
    ["Denim Cut-Off Shorts", "Agolde", "28", "Good", 320.00, "denim_shorts.jpg"],
    ["Adidas Campus Sneakers", "Adidas", "7", "New", 450.00, "adidas_campus.jpg"],
    ["Black Satin Midi Skirt", "Reformation", "S", "New", 380.00, "satin_skirt.jpg"],
];

foreach ($clothes as $item) {
    $title     = $conn->real_escape_string($item[0]);
    $brand     = $conn->real_escape_string($item[1]);
    $size      = $conn->real_escape_string($item[2]);
    $condition = $conn->real_escape_string($item[3]);
    $price     = $item[4];
    $image     = $conn->real_escape_string($item[5]);

    $sql = "INSERT INTO tblClothes (title, brand, size, `condition`, price, image) 
            VALUES ('$title','$brand','$size','$condition',$price,'$image')";
    echo $conn->query($sql) ? "Inserted: $title<br>" : "Error: " . $conn->error . "<br>";
}

//  CREATE tblCart 
$sql = "CREATE TABLE IF NOT EXISTS tblCart (
    cartID INT AUTO_INCREMENT PRIMARY KEY,
    userID INT NOT NULL,
    clothesID INT NOT NULL,
    addedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userID) REFERENCES tblUser(userID),
    FOREIGN KEY (clothesID) REFERENCES tblClothes(clothesID)
)";
echo $conn->query($sql) ? "tblCart created.<br>" : "Error: " . $conn->error . "<br>";

echo "<br><strong>All tables created and data loaded successfully!</strong>";
$conn->close();
?>