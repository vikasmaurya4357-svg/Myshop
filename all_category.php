<?php
session_start();
include "db.php";

$sql1 = "SELECT * FROM categories";
$result1 = mysqli_query($conn, $sql1);

// SEARCH FILTER
if (isset($_GET['my_search']) && $_GET['my_search'] != "") {
    $search = mysqli_real_escape_string($conn, $_GET['my_search']);
    $sql_product = "SELECT * FROM products 
                    WHERE name LIKE '%$search%' 
                    OR description LIKE '%$search%'
                    OR category_name LIKE '%$search%'";
    $results = mysqli_query($conn, $sql_product);
}

//CATEGORY FILTER
else if (isset($_GET['category_name'])) {
    $category_name = mysqli_real_escape_string($conn, $_GET['category_name']);
    $sql_product = "SELECT * FROM products WHERE category_name='$category_name'";
    $results = mysqli_query($conn, $sql_product);
}

// ALL PRODUCTS
else {
    $sql_product = "SELECT * FROM products";
    $results = mysqli_query($conn, $sql_product);
}

//INIT CART
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
<title>All Products</title>
<link rel="stylesheet" href="all_categor.css">
</head>
<body>

<div class="list-page">

<header class="header">
    <a  class="log">MyShop</a>


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
        <a href="card.php" class="cart-icon">
            🛒 <span class="cart-count"><?php echo $total_items; ?></span>
        </a>
    </div>

  
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
<div class="all-page">
<h2>
<?php 
if(isset($_GET['my_search'])) echo "Search results for '$search'";
else if(isset($_GET['category_name'])) echo "Category: $category_name";
else echo "All Products"; 
?>
</h2>

<main class="main">
    <div class="product-grid">
        <?php if(mysqli_num_rows($results) > 0) { ?>
            <?php while($prod = mysqli_fetch_assoc($results)) { ?>
                <div class="product-card">
                    <a href="product-details.php?id=<?php echo $prod['id']; ?>">
                        <img src="./image/<?php echo $prod['image']; ?>" alt="<?php echo $prod['name']; ?>">
                    </a>
                    <p><?php echo $prod['name']; ?></p>
                    <p>₹<?php echo $prod['price']; ?></p>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p style="margin:20px; color:red;">No products found.</p>
        <?php } ?>
    </div>
</main>
</div>


<footer class="footer">
    <p>© MyShop | Created by Vikas</p>
</footer>

<script>
let icon = document.getElementById("userIcon");
let menu = document.getElementById("dropdownMenu");
icon.addEventListener("click", () => {
    menu.style.display = menu.style.display === "block" ? "none" : "block";
});

let cartIcon = document.querySelector(".cart-icon");
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
