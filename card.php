<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
//ADD TO CART 
if(isset($_GET['id'])){
    $pid = intval($_GET['id']);

    // Check if product already in DB cart
    $check = mysqli_query($conn, "SELECT * FROM addcard WHERE user_id='$user_id' AND product_id='$pid'");
    if(mysqli_num_rows($check) > 0){
        mysqli_query($conn, "UPDATE addcard SET quantity = quantity + 1 WHERE user_id='$user_id' AND product_id='$pid'");
    } else {
        mysqli_query($conn, "INSERT INTO addcard (user_id, product_id, quantity) VALUES ('$user_id','$pid',1)");
    }

    header("Location: card.php");
    exit;
}
//REMOVE ITEM
if(isset($_GET['remove'])){
    $pid = intval($_GET['remove']);
    mysqli_query($conn, "DELETE FROM addcard WHERE user_id='$user_id' AND product_id='$pid'");
    header("Location: card.php");
    exit;
}
//UPDATE QUANTITY
if(isset($_POST['update_qty'])){
    $pid = intval($_POST['pid']);
    $qty = max(1,intval($_POST['qty']));
    mysqli_query($conn, "UPDATE addcard SET quantity='$qty' WHERE user_id='$user_id' AND product_id='$pid'");
    header("Location: card.php");
    exit;
}

// FETCH CART ITEMS 
$cart_q = mysqli_query($conn,"
    SELECT a.*, p.name, p.price, p.image
    FROM addcard a
    JOIN products p ON a.product_id = p.id
    WHERE a.user_id='$user_id'
");

$total_amount = 0;
$cart_items = [];
while($row = mysqli_fetch_assoc($cart_q)){
    $total_amount += $row['price'] * $row['quantity'];
    $cart_items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart</title>
<link rel="stylesheet" href="cards.css">
</head>
<body>

<?php
$sql1 = "SELECT * FROM categories";
$result1 = mysqli_query($conn, $sql1);
?>

<div class="list-page">
<header class="header">
    <a  class="log">MyShop</a>
    <form class="search-box" action="card.php" method="GET">
        <input type="text" name="my_search" placeholder="Search products..." required>
        <button type="submit">Search</button>
    </form>
    <div class="user-container">
       
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

<!-- CART ITEMS -->
<div class="cart-wrapper">

    <div class="cart-left">
        <h2>Your Cart</h2>
        <?php if(empty($cart_items)) { echo "<h3>No items in cart</h3>"; } else {
            foreach($cart_items as $item) { ?>
        <div class="cart-item">

            <div class="left-box">
                <img src="image/<?php echo $item['image']; ?>" alt="">
                <form method="POST" class="qty-box">
                    <input type="hidden" name="pid" value="<?php echo $item['product_id']; ?>">
                    <button name="update_qty" type="submit" onclick="this.nextElementSibling.value--;">−</button>
                    <input type="number" name="qty" value="<?php echo $item['quantity']; ?>">
                    <button name="update_qty" type="submit" onclick="this.previousElementSibling.value++;">+</button>
                </form>
            </div>

            <div class="item-details">
                <h3><?php echo $item['name']; ?></h3>
                <p class="big-price">₹<?php echo $item['price']; ?></p>
                <div class="item-actions">
                    <a href="#">SAVE FOR LATER</a>
                    <a href="card.php?remove=<?php echo $item['product_id']; ?>" class="remove-btn">REMOVE</a>
                </div>
            </div>

        </div>
        <?php } } ?>
    </div>

    <div class="cart-right">
        <h3>PRICE DETAILS</h3>
        <div class="price-box">
            <div><span>Price</span><span>₹<?php echo $total_amount; ?></span></div>
            <div><span>Discount</span><span style="color:green;">− ₹0</span></div>
            <div><span>Delivery</span><span style="color:green;">FREE</span></div>
            <div class="total"><span>Total Amount</span><span>₹<?php echo $total_amount; ?></span></div>
        </div>

        <form method="POST" action="order.php">
            <?php foreach ($cart_items as $item): ?>
                <input type="hidden" name="product_id[]" value="<?php echo $item['product_id']; ?>">
                <input type="hidden" name="quantity[]" value="<?php echo $item['quantity']; ?>">
            <?php endforeach; ?>
            <button type="submit" name="place_order" class="place-order">PLACE ORDER</button>
        </form>
    </div>

</div>

</body>
</html>
