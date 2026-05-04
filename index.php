<?php


session_start();
include('DBConn.php');

$featured = $conn->query("SELECT * FROM tblClothes 
                          WHERE status='available' 
                          ORDER BY createdAt DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pastimes — Pre-Loved Fashion</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /*  SPLIT HERO  */
        .hero-split {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 85vh;
        }
        .hero-left {
            background: linear-gradient(135deg, #0077b6, #023e8a);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            color: white;
        }
        .hero-left .tag {
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #90e0ef;
            margin-bottom: 20px;
        }
        .hero-left h1 {
            font-size: 58px;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 20px;
        }
        .hero-left h1 span {
            color: #90e0ef;
        }
        .hero-left p {
            font-size: 16px;
            color: #caf0f8;
            line-height: 1.7;
            margin-bottom: 35px;
            max-width: 380px;
        }
        .hero-cta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .cta-white {
            padding: 14px 30px;
            background: white;
            color: #0077b6;
            border: none;
            border-radius: 50px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
        }
        .cta-outline {
            padding: 14px 30px;
            background: transparent;
            color: white;
            border: 2px solid white;
            border-radius: 50px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
        }
        .hero-right {
            background: #f0f4f8;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            gap: 20px;
        }
        .hero-stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            width: 100%;
            max-width: 380px;
        }
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 25px 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,119,182,0.1);
        }
        .stat-card i {
            font-size: 28px;
            color: #0077b6;
            margin-bottom: 10px;
        }
        .stat-card .stat-number {
            font-size: 28px;
            font-weight: 800;
            color: #023e8a;
        }
        .stat-card .stat-label {
            font-size: 12px;
            color: #888;
            margin-top: 4px;
        }
        .stat-card.featured {
            grid-column: span 2;
            background: linear-gradient(135deg, #0077b6, #023e8a);
            color: white;
        }
        .stat-card.featured i,
        .stat-card.featured .stat-number,
        .stat-card.featured .stat-label {
            color: white;
        }

        /* ===== MARQUEE STRIP ===== */
        .marquee-strip {
            background: #023e8a;
            padding: 12px 0;
            overflow: hidden;
            white-space: nowrap;
        }
        .marquee-content {
            display: inline-block;
            animation: marquee 20s linear infinite;
            color: #90e0ef;
            font-size: 13px;
            letter-spacing: 2px;
        }
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        
        .how-section {
            padding: 80px 20px;
            background: white;
            text-align: center;
        }
        .how-section .section-tag {
            font-size: 11px;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #0077b6;
            margin-bottom: 10px;
        }
        .how-section h2 {
            font-size: 36px;
            color: #023e8a;
            margin-bottom: 50px;
        }
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            max-width: 900px;
            margin: 0 auto;
        }
        .step-item {
            position: relative;
            padding: 30px 20px;
        }
        .step-number {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #0077b6, #023e8a);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 800;
            margin: 0 auto 20px;
        }
        .step-item h4 {
            font-size: 16px;
            color: #023e8a;
            margin-bottom: 10px;
        }
        .step-item p {
            font-size: 13px;
            color: #666;
            line-height: 1.6;
        }

        /*  FEATURED SECTION  */
        .featured-section {
            background: #f0f4f8;
            padding: 80px 20px;
        }
        .featured-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            max-width: 1100px;
            margin: 0 auto 30px;
        }
        .featured-header h2 {
            font-size: 32px;
            color: #023e8a;
        }
        .featured-header a {
            color: #0077b6;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        
        .banner-section {
            background: linear-gradient(135deg, #023e8a, #0077b6);
            padding: 80px 40px;
            text-align: center;
            color: white;
        }
        .banner-section h2 {
            font-size: 40px;
            font-weight: 800;
            margin-bottom: 15px;
        }
        .banner-section p {
            color: #caf0f8;
            font-size: 16px;
            margin-bottom: 30px;
        }

        @media (max-width: 768px) {
            .hero-split { grid-template-columns: 1fr; }
            .hero-left h1 { font-size: 36px; }
            .hero-right { display: none; }
            .featured-header { flex-direction: column; gap: 10px; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav>
    <a href="index.php" class="logo">PASTIMES</a>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="listings.php">Shop</a></li>
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
    Welcome back, <?php echo htmlspecialchars($_SESSION['firstName']); ?>! 
    &nbsp;|&nbsp; 
    <a href="cart.php" style="color:#90e0ef;">View Cart</a>
</div>
<?php endif; ?>

<!-- MARQUEE -->
<div class="marquee-strip">
    <span class="marquee-content">
        FREE DELIVERY ON ORDERS OVER R500 &nbsp;&nbsp;•&nbsp;&nbsp; 
        ZERO SELLER FEES &nbsp;&nbsp;•&nbsp;&nbsp; 
        100% BUYER PROTECTION &nbsp;&nbsp;•&nbsp;&nbsp; 
        VERIFIED SELLERS ONLY &nbsp;&nbsp;•&nbsp;&nbsp;
        FREE DELIVERY ON ORDERS OVER R500 &nbsp;&nbsp;•&nbsp;&nbsp; 
        ZERO SELLER FEES &nbsp;&nbsp;•&nbsp;&nbsp; 
        100% BUYER PROTECTION &nbsp;&nbsp;•&nbsp;&nbsp; 
        VERIFIED SELLERS ONLY &nbsp;&nbsp;•&nbsp;&nbsp;
    </span>
</div>

<!-- SPLIT HERO -->
<div class="hero-split">
    <div class="hero-left">
        <div class="tag">South Africa's #1 Pre-Loved Fashion</div>
        <h1>Style That <span>Tells</span> A Story</h1>
        <p>Buy and sell premium second-hand branded clothing. 
           Give great fashion a second life — sustainably and affordably.</p>
        <div class="hero-cta">
            <a href="listings.php" class="cta-white">Shop Now</a>
            <?php if (!isset($_SESSION['userID'])): ?>
                <a href="register.php" class="cta-outline">Join Free</a>
            <?php else: ?>
                <a href="dashboard.php" class="cta-outline">My Account</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="hero-right">
        <div class="hero-stat-grid">
            <div class="stat-card featured">
                <i class="fas fa-tshirt"></i>
                <div class="stat-number">500+</div>
                <div class="stat-label">Items Available</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <div class="stat-number">2K+</div>
                <div class="stat-label">Happy Buyers</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-star"></i>
                <div class="stat-number">4.8</div>
                <div class="stat-label">Average Rating</div>
            </div>
        </div>
    </div>
</div>

<!-- HOW IT WORKS -->
<div class="how-section">
    <div class="section-tag">Simple Process</div>
    <h2>How Pastimes Works</h2>
    <div class="steps-grid">
        <div class="step-item">
            <div class="step-number">1</div>
            <h4>Create Account</h4>
            <p>Register for free and get verified by our admin team before you start shopping.</p>
        </div>
        <div class="step-item">
            <div class="step-number">2</div>
            <h4>Browse & Discover</h4>
            <p>Explore hundreds of branded second-hand clothing items at unbeatable prices.</p>
        </div>
        <div class="step-item">
            <div class="step-number">3</div>
            <h4>Add to Cart</h4>
            <p>Select your favourite items and add them to your cart with one click.</p>
        </div>
        <div class="step-item">
            <div class="step-number">4</div>
            <h4>Fast Delivery</h4>
            <p>Enter your delivery details and receive your order straight to your door.</p>
        </div>
    </div>
</div>

<!-- FEATURED ITEMS -->
<div class="featured-section">
    <div class="featured-header">
        <div>
            <div class="section-tag" style="text-align:left;">New Arrivals</div>
            <h2>Featured Items</h2>
        </div>
        <a href="listings.php">View All Items →</a>
    </div>
    <div class="product-grid" style="max-width:1100px; margin:0 auto;">
        <?php while ($item = $featured->fetch_assoc()): ?>
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
                <a href="listings.php">
                    <button class="btn-cart">View Item</button>
                </a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- BOTTOM BANNER -->
<div class="banner-section">
    <h2>Ready to Shop Sustainably?</h2>
    <p>Join thousands of South Africans buying and selling pre-loved fashion on Pastimes.</p>
    <?php if (!isset($_SESSION['userID'])): ?>
        <a href="register.php" class="cta-white" 
           style="display:inline-block; padding:15px 40px; background:white; 
                  color:#0077b6; border-radius:50px; font-weight:700; 
                  text-decoration:none; font-size:15px;">
            Get Started Free
        </a>
    <?php else: ?>
        <a href="listings.php" class="cta-white"
           style="display:inline-block; padding:15px 40px; background:white; 
                  color:#0077b6; border-radius:50px; font-weight:700; 
                  text-decoration:none; font-size:15px;">
            Browse All Items
        </a>
    <?php endif; ?>
</div>

<!-- FOOTER -->
<footer>
    <p>&copy; 2026 PASTIMES — Pre-Loved Fashion for Everyone</p>
</footer>

</body>
</html>