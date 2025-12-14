<?php
session_start();
include "db.php";

// PRODUCT FETCH
if (!isset($_GET['id'])) { 
    header("Location: login.php");
    exit; 
} else {
    $product_id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT * FROM products WHERE id='$product_id'";
    $result = mysqli_query($conn, $sql);
}

$product = mysqli_fetch_assoc($result);

// CATEGORY LIST FOR HEADER / FILTER
$sql1 = "SELECT * FROM categories";
$result1 = mysqli_query($conn, $sql1);


//CART COUNT FROM DATABASE
$total_items = 0;
if(isset($_SESSION['user_id'])){
    $uid = $_SESSION['user_id'];
    $cart_count_q = mysqli_query($conn, "SELECT SUM(quantity) as total FROM addcard WHERE user_id='$uid'");
    $count_row = mysqli_fetch_assoc($cart_count_q);
    $total_items = $count_row['total'] ?? 0;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $product['name']; ?></title>
<link rel="stylesheet" href="product.css">
</head>
<body>
<header class="header">
    <a href="index.php" class="log">MyShop</a>

   
    <form class="search-box" action="all_category.php" method="GET">
        <input type="text" name="my_search" placeholder="Search products..." required>
        <button type="submit">Search</button>
    </form>

    <div class="user-container">
    
        <div class="user-icon" id="userIcon">
            <svg class="login-icon" viewBox="0 0 24 24">
                <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 
                2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
            </svg>
        </div>
        <a href="card.php" class="cart-icon" id="cartIcon">
            🛒 <span class="cart-count"><?php echo $total_items; ?></span>
        </a>
    </div>

    <!-- USER DROPDOWN -->
    <ul class="dropdown-menu" id="dropdownMenu">
        <li>
            <?php 
            if(isset($_SESSION['user_id'])) {
                echo '<a href="my_profile.php">My Profile</a>';
            } else {
                echo '<a href="login.php">My Profile</a>';
            }
            ?>
        </li>
        <li>
            <?php 
            if(isset($_SESSION['user_id'])) {
                echo '<a href="my_orders.php">My Orders</a>';
            } else {
                echo '<a href="login.php">My Orders</a>';
            }
            ?>
        </li>
        <li>
            <?php 
            if(!isset($_SESSION['user_id'])) {
                echo '<a href="login.php">login</a>';
            } 
            ?>
        </li>
        <li>
            <?php 
            if(!isset($_SESSION['user_id'])) {
                echo '<a href="register.php">Create Account</a>';
            } 
            ?>
        </li>
        <?php 
        if(isset($_SESSION['user_id']) && $_SESSION['user_role']=='admin') {
            echo '<li><a href="admin/dashboard.php">Go Admin</a></li>';
        } 
        ?>
        <?php 
        if(isset($_SESSION['user_id'])) {
            echo '<li><a href="logout.php">Logout</a></li>';
        } 
        ?>
    </ul>

    <!-- CART DROPDOWN -->
    <div class="cart-dropdown" id="cartDropdown">
        <?php if(empty($_SESSION['cart'])): ?>
            <p>Your cart is empty!</p>
        <?php else: ?>
            <?php foreach($_SESSION['cart'] as $id => $qty):
                $sql = "SELECT * FROM products WHERE id='$id'";
                $res = mysqli_query($conn, $sql);
                $prod = mysqli_fetch_assoc($res);
            ?>
                <div class="cart-item">
                    <img src="./image/<?php echo $prod['image']; ?>" width="40" alt="">
                    <p><?php echo $prod['name']; ?> x <?php echo $qty; ?></p>
                </div>
            <?php endforeach; ?>
            <a href="card.php">Go to Cart</a>
        <?php endif; ?>
    </div>
</header>
<div class="category-bar">
   <a href="index.php" class="logo">All</a>

    <?php while ($row_cat = mysqli_fetch_assoc($result1)) { ?>
        <a href="all_category.php?category_name=<?php echo $row_cat['name']; ?>">
            <?php echo $row_cat['name']; ?>
        </a>
    <?php } ?>
</div>

<!-- PRODUCT DETAILS -->
<div class="product-container">

    <!-- LEFT SIDE IMAGE SECTION -->
    <div>
        <div class="product-gallery">
            <div class="thumb-list">
                <img src="./image/<?php echo $product['image']; ?>" class="thumb active" onclick="changeImage(this)">
            </div>

            <div class="main-image-box">
                <img src="./image/<?php echo $product['image']; ?>" id="mainImage" class="main-image">
            </div>
        </div>

        <div class="image-buttons">
          <a href="card.php?id=<?php echo $product['id']; ?>" class="img-btn btn-cart">🛒 ADD TO CART</a>

            <a href="order.php?product_id=<?php echo $product['id']; ?>" class="img-btn btn-buy">⚡ BUY NOW</a>
        </div>
    </div>

    <!-- RIGHT SIDE PRODUCT INFO -->
    <div class="product-info">
        <h2><?php echo $product['name']; ?></h2>
        <p class="price">₹<?php echo $product['price']; ?></p>

        <div class="offer-box">
            <div class="offer-title">Available Offers</div>
            <p>• Cashback up to 5% on select cards</p>
            <p>• Additional festival season discount available</p>
        </div>

        <p><strong>Category:</strong> <?php echo $product['category_name']; ?></p>
       <h1> <p><strong>About this item</strong><br></h1>
        <?php echo $product['description']; ?></p>
    </div>

</div>

<footer class="footer">
<p>© MyShop | Created by Vikas</p>
</footer>

<script>
// IMAGE THUMB CLICK
function changeImage(thumb) {
    document.getElementById("mainImage").src = thumb.src;
    document.querySelectorAll(".thumb").forEach(t => t.classList.remove("active"));
    thumb.classList.add("active");
}

// USER DROPDOWN
let icon = document.getElementById("userIcon");
let menu = document.getElementById("dropdownMenu");
icon.addEventListener("click", () => {
    menu.style.display = menu.style.display === "block" ? "none" : "block";
});

// CART DROPDOWN
let cartIcon = document.getElementById("cartIcon");
let cartDropdown = document.getElementById("cartDropdown");
cartIcon.addEventListener("click", () => {
    cartDropdown.style.display = cartDropdown.style.display === "block" ? "none" : "block";
});

// Outside click close both
document.addEventListener("click", (e) => {
    if (!icon.contains(e.target) && !menu.contains(e.target)) menu.style.display = "none";
    if (!cartIcon.contains(e.target) && !cartDropdown.contains(e.target)) cartDropdown.style.display = "none";
});
</script>

</body>
</html>
