<?php
include "db.php";
session_start();

$sql1 = "SELECT * FROM categories";
$result1 = mysqli_query($conn, $sql1);

//SEARCH FILTER
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

//ALL PRODUCTS 
else {
    $sql_product = "SELECT * FROM products";
    $results = mysqli_query($conn, $sql_product);
}

//CART COUNT FROM DATABASE 
$total_items = 0;
if(isset($_SESSION['user_id'])){
    $uid = $_SESSION['user_id'];
    $cart_count_q = mysqli_query($conn, "SELECT SUM(quantity) as total FROM addcard WHERE user_id='$uid'");
    $count_row = mysqli_fetch_assoc($cart_count_q);
    $total_items = $count_row['total'] ?? 0;
}

// CATEGORY LIST FOR INDEX PAGE
$cat_sql = "SELECT * FROM categories";
$cat_result = mysqli_query($conn, $cat_sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyShop - Home</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>



<div class="list-page">

<header class="header">
    <a class="log">MyShop</a>

    <!-- SEARCH -->
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

<!-- CATEGORY BAR -->
<div class="category-bar">
   <a href="index.php" class="logo">All</a>

    <?php while ($row_cat = mysqli_fetch_assoc($result1)) { ?>
        <a href="all_category.php?category_name=<?php echo $row_cat['name']; ?>">
            <?php echo $row_cat['name']; ?>
        </a>
    <?php } ?>
</div>

</div>



<!--BANNER SLIDER -->
<div class="banner-slider">

    <div class="slides">
        <img src="./image/banner1.jpg" class="slide active">
        <img src="./image/banner2.jpg" class="slide">
        <img src="./image/banner3.jpg" class="slide">
    </div>

    <div class="arrow left" onclick="prevSlide()">&#10094;</div>
    <div class="arrow right" onclick="nextSlide()">&#10095;</div>

    <div class="dots">
        <span class="dot active" onclick="goToSlide(0)"></span>
        <span class="dot" onclick="goToSlide(1)"></span>
        <span class="dot" onclick="goToSlide(2)"></span>
    </div>

</div>


<!--  CATEGORY BOXES-->
<main class="main">

<div class="category-boxes-container">

<?php while($cat = mysqli_fetch_assoc($cat_result)) { ?>
    <div class="category-box">
        <div class="product-box">
            <?php
            $cat_name = mysqli_real_escape_string($conn, $cat['name']);
            $prod_sql = "SELECT * FROM products WHERE category_name = '$cat_name' LIMIT 4";
            $prod_result = mysqli_query($conn, $prod_sql);

            if (!$prod_result) continue;

            if(mysqli_num_rows($prod_result) > 0){
                while($prod = mysqli_fetch_assoc($prod_result)){
            ?>
                    <div class="product-card">
                        <a href="all_category.php?category_name=<?php echo $prod['category_name']; ?>">
                            <img src="./image/<?php echo $prod['image']; ?>" alt="">
                        </a>
                        <h3>
                        <?php 
                            $short_name = mb_strimwidth($prod['name'], 0, 15, '...');
                            echo htmlspecialchars($short_name);
                        ?>
                        </h3>
                    </div>
            <?php
                }
            } else {
                echo "<p>No products</p>";
            }
            ?>
        </div>
    </div>
<?php } ?>

</div>

</main>



<footer class="footer">
    <p>© MyShop | Created by Vikas</p>
</footer>


<!-- JS -->
<script>
let icon = document.getElementById("userIcon");
let menu = document.getElementById("dropdownMenu");
icon.addEventListener("click", () => {
    menu.style.display = menu.style.display === "block" ? "none" : "block";
});

let cartIcon = document.getElementById("cartIcon");
let cartDropdown = document.getElementById("cartDropdown");
cartIcon.addEventListener("click", () => {
    cartDropdown.style.display = cartDropdown.style.display === "block" ? "none" : "block";
});

document.addEventListener("click", (e) => {
    if (!icon.contains(e.target) && !menu.contains(e.target)) menu.style.display = "none";
    if (!cartIcon.contains(e.target) && !cartDropdown.contains(e.target)) cartDropdown.style.display = "none";
});


let index = 0;

function showSlide(i) {
    const slides = document.querySelectorAll(".slide");
    const dots = document.querySelectorAll(".dot");

    slides.forEach(s => s.classList.remove("active"));
    dots.forEach(d => d.classList.remove("active"));

    if (i >= slides.length) index = 0;
    if (i < 0) index = slides.length - 1;

    slides[index].classList.add("active");
    dots[index].classList.add("active");
}

function nextSlide() { index++; showSlide(index); }
function prevSlide() { index--; showSlide(index); }
function goToSlide(i) { index = i; showSlide(index); }

setInterval(nextSlide, 4000);
</script>

</body>
</html>
