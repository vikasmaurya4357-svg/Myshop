<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if(isset($_POST['place_order'])){

    $products = [];

    if(isset($_POST['product_id'])){
        foreach($_POST['product_id'] as $i => $pid){
            $qty = (int)$_POST['quantity'][$i];
            $prod_q = mysqli_query($conn, "SELECT * FROM products WHERE id='$pid'");
            if($prod_q && mysqli_num_rows($prod_q) > 0){
                $prod = mysqli_fetch_assoc($prod_q);
                $prod['quantity'] = $qty;
                $products[] = $prod;
            }
        }
    } else {
        die("No products to order.");
    }

    $delivery_charge = 50;
    $packing_charge  = 5;

    $order_total = 0;
    foreach($products as $p){
        $order_total += $p['price'] * $p['quantity'];
    }
    $grand_total = $order_total + $delivery_charge + $packing_charge;

    mysqli_query($conn, "
        INSERT INTO orders (user_id, total_amount, created_at) 
        VALUES ('$user_id','$grand_total',NOW())
    ");
    $order_id = mysqli_insert_id($conn);

    foreach($products as $p){
        $name  = mysqli_real_escape_string($conn, $p['name']);
        $price = $p['price'];
        $qty   = $p['quantity'];
        $image = mysqli_real_escape_string($conn, $p['image'] ?? '');

        mysqli_query($conn, "
            INSERT INTO order_items 
            (order_id, product_id, product_name, price, qty, image)
            VALUES 
            ('$order_id','{$p['id']}','$name','$price','$qty','$image')
        ");
    }

    // CLEAR CART TABLE
    if(isset($_POST['from_cart']) && $_POST['from_cart'] == 1){
        mysqli_query($conn, "DELETE FROM addcard WHERE user_id='$user_id'");
    }

    echo "<script>
        alert('Order placed successfully!');
        window.location.href='my_orders.php';
    </script>";
}
?>
