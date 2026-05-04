<?php

session_start();
include('DBConn.php');

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$userID  = $_SESSION['userID'];
$message = "";
$success = false;

$sql = "SELECT tblCart.cartID, tblClothes.clothesID, tblClothes.title,
               tblClothes.brand, tblClothes.price, tblClothes.image
        FROM tblCart
        JOIN tblClothes ON tblCart.clothesID = tblClothes.clothesID
        WHERE tblCart.userID = $userID";
$result    = $conn->query($sql);
$cartItems = [];
$total     = 0;
while ($item = $result->fetch_assoc()) {
    $cartItems[] = $item;
    $total      += $item['price'];
}

if (count($cartItems) === 0) {
    header("Location: cart.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $addressType   = $conn->real_escape_string($_POST['addressType']);
    $streetAddress = $conn->real_escape_string(trim($_POST['streetAddress']));
    $city          = $conn->real_escape_string(trim($_POST['city']));
    $province      = $conn->real_escape_string($_POST['province']);
    $postalCode    = $conn->real_escape_string(trim($_POST['postalCode']));

    if (empty($streetAddress) || empty($city) || empty($postalCode)) {
        $message = "Please fill in all delivery fields.";
    } else {
        foreach ($cartItems as $item) {
            $clothesID = $item['clothesID'];
            $price     = $item['price'];
            $conn->query("INSERT INTO tblAorder
                (userID,clothesID,totalAmount,addressType,streetAddress,city,province,postalCode)
                VALUES ($userID,$clothesID,$price,'$addressType',
                '$streetAddress','$city','$province','$postalCode')");
            $conn->query("UPDATE tblClothes SET status='sold' WHERE clothesID=$clothesID");
        }
        $conn->query("DELETE FROM tblCart WHERE userID=$userID");
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — Pastimes</title>
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
    User <?php echo htmlspecialchars($_SESSION['firstName'].' '.$_SESSION['lastName']); ?> is logged in
</div>

<div class="container">
    <?php if ($success): ?>
        <div style="text-align:center; padding:60px 20px;">
            <h2 style="color:#0077b6; font-size:28px; margin-bottom:15px;">
                Order Confirmed! 🎉
            </h2>
            <p style="color:#666; font-size:16px; margin-bottom:10px;">
                Thank you for your order, <?php echo htmlspecialchars($_SESSION['firstName']); ?>!
            </p>
            <p style="color:#666; font-size:16px; margin-bottom:30px;">
                Your items will be delivered to your address soon.
            </p>
            <a href="listings.php">
                <button class="btn-primary" style="max-width:250px;">Continue Shopping</button>
            </a>
        </div>
    <?php else: ?>
        <h2>Checkout</h2>

        <?php if (!empty($message)): ?>
            <div class="error-msg"><?php echo $message; ?></div>
        <?php endif; ?>

        <h3 style="margin:20px 0 10px; font-size:16px; color:#0077b6;">Order Summary</h3>
        <table class="data-table" style="margin-bottom:30px;">
            <tr><th>Item</th><th>Brand</th><th>Price</th></tr>
            <?php foreach ($cartItems as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['title']); ?></td>
                <td><?php echo htmlspecialchars($item['brand']); ?></td>
                <td>R <?php echo number_format($item['price'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2" style="text-align:right; font-weight:600; padding:15px;">TOTAL:</td>
                <td style="font-weight:bold; font-size:18px; color:#023e8a; padding:15px;">
                    R <?php echo number_format($total, 2); ?>
                </td>
            </tr>
        </table>

        <h3 style="margin:20px 0 10px; font-size:16px; color:#0077b6;">Delivery Details</h3>
        <div class="card" style="max-width:100%;">
            <form method="POST" action="checkout.php">
                <div class="form-group">
                    <label>Address Type</label>
                    <div style="display:flex; gap:30px; margin-top:8px;">
                        <label style="display:flex; align-items:center; gap:8px;
                               font-weight:normal; text-transform:none; letter-spacing:0;">
                            <input type="radio" name="addressType" value="residential" checked>
                            Residential
                        </label>
                        <label style="display:flex; align-items:center; gap:8px;
                               font-weight:normal; text-transform:none; letter-spacing:0;">
                            <input type="radio" name="addressType" value="work"> Work
                        </label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Street Address</label>
                    <input type="text" name="streetAddress" required
                           placeholder="e.g. 12 Main Road">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:15px;">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" required placeholder="e.g. Cape Town">
                    </div>
                    <div class="form-group">
                        <label>Postal Code</label>
                        <input type="text" name="postalCode" required placeholder="e.g. 8001">
                    </div>
                </div>
                <div class="form-group">
                    <label>Province</label>
                    <select name="province" style="width:100%; padding:12px 15px;
                            border:1.5px solid #90e0ef; border-radius:8px; font-size:14px;">
                        <option>Gauteng</option>
                        <option>Western Cape</option>
                        <option>KwaZulu-Natal</option>
                        <option>Eastern Cape</option>
                        <option>Limpopo</option>
                        <option>Mpumalanga</option>
                        <option>North West</option>
                        <option>Free State</option>
                        <option>Northern Cape</option>
                    </select>
                </div>
                <div style="display:flex; gap:15px; flex-wrap:wrap;">
                    <a href="cart.php">
                        <button type="button" class="btn-secondary" style="max-width:200px;">
                            Back to Cart
                        </button>
                    </a>
                    <button type="submit" class="btn-primary" style="max-width:200px;">
                        Confirm Order
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2026 PASTIMES — Second-Hand Fashion</p>
</footer>
</body>
</html>