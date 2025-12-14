<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch latest user address
$address_q = mysqli_query($conn, "SELECT * FROM user_addresses WHERE user_id='$user_id' ORDER BY id DESC LIMIT 1");
$address = mysqli_fetch_assoc($address_q);

// Charges
$delivery_charge = 50;
$packing_charge = 20;

// Products array
$products = [];
$is_cart_order = false;

//  BUY NOW 
if(isset($_GET['product_id'])){
    $product_id = (int)$_GET['product_id'];
    $prod_q = mysqli_query($conn, "SELECT * FROM products WHERE id='$product_id'");
    if($prod = mysqli_fetch_assoc($prod_q)){
        $prod['quantity'] = 1;
        $products[] = $prod;
    }
}

// CART ORDER 
else {
    $cart_q = mysqli_query($conn,"
        SELECT a.*, p.name, p.price, p.image
        FROM addcard a
        JOIN products p ON a.product_id = p.id
        WHERE a.user_id='$user_id'
    ");

    while($row = mysqli_fetch_assoc($cart_q)){
        $products[] = $row;
    }

    if(!empty($products)){
        $is_cart_order = true;
    }
}

if(empty($products)){
    die("<h2>No products to order.</h2>");
}

// Calculate total
$total_amount = 0;
foreach($products as $p){
    $total_amount += $p['price'] * $p['quantity'];
}
$grand_total = $total_amount + $delivery_charge + $packing_charge;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Confirm Your Order</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="orderss.css">
</head>
<body>
    <header class="header">
        <a href="index.php" class="logo">MyShop</a>

        <form class="search-box" action="all_category.php" method="GET">
            <input type="text" name="my_search" placeholder="Search products..." required>
            <button type="submit">Search</button>
        </form>

        <div class="user-container">
            <a href="card.php" class="cart-icon">
                🛒 <span class="cart-count"><?php echo isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; ?></span>
            </a>
        </div>
    </header>
<div class="order-container">
    <h2>Confirm Your Order</h2>

    <div class="address-box">
        <h3>Shipping Address</h3>
        <p><strong>Name:</strong> <?php echo $address['name'] ?? ''; ?></p>
        <p><strong>Mobile:</strong> <?php echo $address['mobile'] ?? ''; ?></p>
        <p><strong>PIN:</strong> <?php echo $address['pin'] ?? ''; ?></p>
        <p><strong>Locality:</strong> <?php echo $address['locality'] ?? ''; ?></p>
        <p><strong>Address:</strong> <?php echo $address['address'] ?? ''; ?></p>
        <p><strong>City:</strong> <?php echo $address['city'] ?? ''; ?></p>
        <p><strong>State:</strong> <?php echo $address['state'] ?? ''; ?></p>
        <p><strong>Landmark:</strong> <?php echo $address['landmark'] ?? ''; ?></p>
        <a href="my_address.php" class="edit-address">Edit Address</a>
    </div>

    <h3>Products</h3>
    <form method="POST" action="place_order.php" id="orderForm">
        <?php foreach($products as $index => $p){ ?>
        <div class="product-card">
            <img src="image/<?php echo $p['image']; ?>" alt="<?php echo $p['name']; ?>">
            <div class="product-details">
                <h3><?php echo $p['name']; ?></h3>
                <div class="quantity-control">
                    <span class="qty"><?php echo $p['quantity']; ?></span>
                </div>
                <div class="price">Price: ₹<span class="product-price"><?php echo $p['price']; ?></span></div>
            </div>
            <input type="hidden" name="product_id[]" value="<?php echo $p['product_id'] ?? $p['id']; ?>">
            <input type="hidden" name="quantity[]" value="<?php echo $p['quantity']; ?>">
        </div>
        <?php } ?>

        <div class="charges">
            <p>Delivery Charge <span>₹<?php echo $delivery_charge; ?></span></p>
            <p>Packing Charge <span>₹<?php echo $packing_charge; ?></span></p>
            <p class="total">Total Amount <span>₹<?php echo $grand_total; ?></span></p>
        </div>

        <?php if($is_cart_order): ?>
            <input type="hidden" name="from_cart" value="1">
        <?php endif; ?>

        <button type="submit" name="place_order" class="confirm-order">Confirm Order</button>
    </form>
</div>

</body>
</html>
