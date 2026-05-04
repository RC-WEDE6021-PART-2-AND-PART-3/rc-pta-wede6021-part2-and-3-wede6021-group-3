<?php


session_start();
include('DBConn.php');

$sql    = "SELECT * FROM tblClothes WHERE status='available' ORDER BY createdAt DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listings — Pastimes</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }
        .popup-overlay.active { display: flex; }
        .popup-box {
            background: #ffffff;
            border-radius: 12px;
            padding: 40px;
            max-width: 400px;
            width: 90%;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0,119,182,0.2);
        }
        .popup-box h3 {
            font-size: 20px;
            color: #0077b6;
            margin-bottom: 10px;
        }
        .popup-box .popup-price {
            font-size: 36px;
            font-weight: bold;
            color: #023e8a;
            margin: 15px 0;
        }
        .popup-box p { color: #666; font-size:14px; margin-bottom:25px; }
        .popup-box .btn-primary { max-width:200px; margin:0 auto 10px; display:block; }
        .popup-box .btn-close {
            background: none; border: none;
            color: #0077b6; font-size: 13px;
            cursor: pointer; margin-top: 10px;
            text-decoration: underline;

            /* List view for listings */
.listings-layout {
    display: flex;
    gap: 30px;
    max-width: 1100px;
    margin: 0 auto;
}
.listings-sidebar {
    width: 220px;
    flex-shrink: 0;
}
.filter-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    border: 1px solid #caf0f8;
    margin-bottom: 20px;
}
.filter-card h4 {
    font-size: 13px;
    font-weight: 700;
    color: #023e8a;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e0f4ff;
}
.filter-option {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 10px;
    font-size: 13px;
    color: #444;
    cursor: pointer;
}
.listings-main { flex: 1; }
.listings-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.listings-header h2 {
    font-size: 22px;
    color: #023e8a;
    border: none;
    padding: 0;
    margin: 0;
}
.item-count {
    font-size: 13px;
    color: #888;
}
        }
    </style>
</head>
<body>
<nav>
    <a href="index.php" class="logo">PASTIMES</a>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="listings.php">Listings</a></li>
        <?php if (isset($_SESSION['userID'])): ?>
            <li><a href="cart.php">Cart</a></li>
            <li><a href="dashboard.php">My Account</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

<?php if (isset($_SESSION['userID'])): ?>
<div class="user-bar">
    User <?php echo htmlspecialchars($_SESSION['firstName'].' '.$_SESSION['lastName']); ?> is logged in
</div>
<?php endif; ?>

<!-- POPUP -->
<div class="popup-overlay" id="cartPopup">
    <div class="popup-box">
        <h3 id="popupTitle"></h3>
        <div class="popup-price" id="popupPrice"></div>
        <p id="popupDetails"></p>
        <form method="POST" action="cart.php">
            <input type="hidden" name="clothesID" id="popupClothesID">
            <button type="submit" name="addToCart" class="btn-primary">
                Confirm Add to Cart
            </button>
        </form>
        <button class="btn-close" onclick="closePopup()">Continue Shopping</button>
    </div>
</div>

<div class="container" style="max-width:1200px;">

    <?php if ($result->num_rows > 0): ?>
    <div class="listings-layout">

        <!-- SIDEBAR FILTERS -->
        <div class="listings-sidebar">
            <div class="filter-card">
                <h4>Category</h4>
                <label class="filter-option">
                    <input type="checkbox" checked> All Items
                </label>
                <label class="filter-option">
                    <input type="checkbox"> Tops
                </label>
                <label class="filter-option">
                    <input type="checkbox"> Bottoms
                </label>
                <label class="filter-option">
                    <input type="checkbox"> Jackets
                </label>
                <label class="filter-option">
                    <input type="checkbox"> Shoes
                </label>
                <label class="filter-option">
                    <input type="checkbox"> Skirts
                </label>
            </div>
            <div class="filter-card">
                <h4>Condition</h4>
                <label class="filter-option">
                    <input type="checkbox" checked> All
                </label>
                <label class="filter-option">
                    <input type="checkbox"> New
                </label>
                <label class="filter-option">
                    <input type="checkbox"> Good
                </label>
                <label class="filter-option">
                    <input type="checkbox"> Fair
                </label>
            </div>
            <div class="filter-card">
                <h4>Price Range</h4>
                <label class="filter-option">
                    <input type="checkbox" checked> All Prices
                </label>
                <label class="filter-option">
                    <input type="checkbox"> Under R300
                </label>
                <label class="filter-option">
                    <input type="checkbox"> R300 — R600
                </label>
                <label class="filter-option">
                    <input type="checkbox"> Over R600
                </label>
            </div>
        </div>

        <!-- MAIN LISTINGS -->
        <div class="listings-main">
            <div class="listings-header">
                <h2>Available Clothing</h2>
                <span class="item-count">
                    <?php 
                    // Count available items
                    $countResult = $conn->query("SELECT COUNT(*) as total 
                                                FROM tblClothes 
                                                WHERE status='available'");
                    $count = $countResult->fetch_assoc();
                    echo $count['total'] . " items found";
                    ?>
                </span>
            </div>
            <div class="product-grid">
                <?php while ($item = $result->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="images/<?php echo htmlspecialchars($item['image']); ?>"
                         alt="<?php echo htmlspecialchars($item['title']); ?>"
                         onerror="this.src='images/placeholder.jpg'">
                    <div class="product-info">
                        <div class="product-brand">
                            <?php echo htmlspecialchars($item['brand']); ?>
                            &middot; Size <?php echo htmlspecialchars($item['size']); ?>
                            &middot; <?php echo htmlspecialchars($item['condition']); ?>
                        </div>
                        <div class="product-title">
                            <?php echo htmlspecialchars($item['title']); ?>
                        </div>
                        <div class="product-price">
                            R <?php echo number_format($item['price'], 2); ?>
                        </div>
                        <button class="btn-cart" onclick="openPopup(
                            '<?php echo htmlspecialchars($item['title']); ?>',
                            '<?php echo $item['price']; ?>',
                            '<?php echo htmlspecialchars($item['brand']); ?>',
                            '<?php echo htmlspecialchars($item['size']); ?>',
                            '<?php echo $item['clothesID']; ?>'
                        )">Add to Cart</button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <?php else: ?>
        <div class="info-msg">No items available. Check back soon!</div>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2026 PASTIMES — Second-Hand Fashion</p>
</footer>

<script>
    function openPopup(title, price, brand, size, clothesID) {
        document.getElementById('popupTitle').innerText = title;
        document.getElementById('popupPrice').innerText = 'R ' + parseFloat(price).toFixed(2);
        document.getElementById('popupDetails').innerText = brand + ' · Size ' + size;
        document.getElementById('popupClothesID').value = clothesID;
        document.getElementById('cartPopup').classList.add('active');
    }
    function closePopup() {
        document.getElementById('cartPopup').classList.remove('active');
    }
    document.getElementById('cartPopup').addEventListener('click', function(e) {
        if (e.target === this) closePopup();
    });
</script>
</body>
</html>