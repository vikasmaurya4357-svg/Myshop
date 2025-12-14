<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Fetch user info
$user_res = mysqli_query($conn,"SELECT * FROM users WHERE id='$user_id' LIMIT 1");
$user = mysqli_fetch_assoc($user_res);

// Get order id from query
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
if(!$order_id){
    die("Invalid Order ID.");
}

// Fetch order
$order_res = mysqli_query($conn, "SELECT * FROM orders WHERE id='$order_id' AND user_id='$user_id' LIMIT 1");
if(mysqli_num_rows($order_res)==0){
    die("Order not found.");
}
$order = mysqli_fetch_assoc($order_res);

// Fetch order items
$items_res = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id='$order_id'");

// Handle cancel order
if(isset($_POST['cancel_order'])){
    if(in_array($order['order_status'], ['Ordered','Packed'])){
        $now = date('Y-m-d H:i:s');
        mysqli_query($conn, "UPDATE orders SET order_status='Cancelled', refund_status='Pending', cancel_requested_at='$now' WHERE id='$order_id'");
        $_SESSION['order_msg'] = "Order #$order_id cancelled successfully.";
        header("Location: order_detail.php?order_id=$order_id");
        exit;
    } else {
        $_SESSION['order_msg'] = "Order cannot be cancelled.";
        header("Location: order_detail.php?order_id=$order_id");
        exit;
    }
}

// Tracking events dynamic
$tracking_events = [];
$status_order = ['Ordered','Packed','Shipped','Out for Delivery','Delivered','Cancelled'];

foreach($status_order as $status){
    if($status == 'Ordered' && !empty($order['created_at'])){
        $tracking_events[] = ['status'=>$status,'datetime'=>$order['created_at'],'message'=>'Your order has been placed.'];
    }
    if($status == 'Packed' && !empty($order['packed_at'])){
        $tracking_events[] = ['status'=>$status,'datetime'=>$order['packed_at'],'message'=>'Your order has been packed.'];
    }
    if($status == 'Shipped' && !empty($order['shipped_at'])){
        $tracking_events[] = ['status'=>$status,'datetime'=>$order['shipped_at'],'message'=>'Your order is on the way.','courier'=>$order['courier_name']??'','tracking_id'=>$order['tracking_id']??''];
    }
    if($status == 'Out for Delivery' && !empty($order['out_for_delivery_at'])){
        $tracking_events[] = ['status'=>$status,'datetime'=>$order['out_for_delivery_at'],'message'=>'Your order is out for delivery.'];
    }
    if($status == 'Delivered' && !empty($order['delivered_at'])){
        $tracking_events[] = ['status'=>$status,'datetime'=>$order['delivered_at'],'message'=>'Your order has been delivered.'];
    }
    if($status == 'Cancelled' && $order['order_status']=='Cancelled'){
        $tracking_events[] = ['status'=>$status,'datetime'=>$order['cancel_requested_at'],'message'=>'Your order has been cancelled.'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order #<?php echo $order_id; ?> - MyShop</title>
<link rel="stylesheet" href="order_detail.css">
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="user-box">
        <?php if(isset($user['image']) && $user['image'] != ''): ?>
            <img src="./image/<?php echo $user['image']; ?>" alt="User Image">
        <?php else: ?>
            <svg viewBox="0 0 24 24">
                <path d="M12 12c2.7 0 5-2.3 5-5s-2.3-5-5-5-5 
                2.3-5 5 2.3 5 5 5zm0 2c-3.3 0-10 1.7-10 5v3h20v-3c0-3.3-6.7-5-10-5z"/>
            </svg>
        <?php endif; ?>
        <p class="user-name"><?php echo $user['name']; ?></p>
    </div>
    <div class="menu-box">
        <h2>User Menu</h2>
        <ul>
            <li><a href="index.php">Continue Shopping</a></li>
            <li><a href="my_profile.php">My Profile</a></li>
            <li><a href="my_orders.php">My Orders</a></li>
            <li><a href="my_address.php">My Address</a></li>
            <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                <li><a href="admin/dashboard.php">Go Admin</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
</aside>

<!-- Main content -->
<div class="main-content">

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

<div class="container">
<h2>Order #<?php echo $order_id; ?></h2>

<?php if(!empty($_SESSION['order_msg'])): ?>
<div style="padding:12px;background:#fff3cd;border:1px solid #ffeeba;margin-bottom:15px;border-radius:8px;color:#856404;"><?php echo $_SESSION['order_msg']; unset($_SESSION['order_msg']); ?></div>
<?php endif; ?>

<div class="status-pill status-<?php echo $order['order_status']; ?>"><?php echo $order['order_status']; ?></div>
<p><strong>Order Date:</strong> <?php echo date('d M Y H:i', strtotime($order['created_at'])); ?></p>
<p><strong>Delivery Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>

<h3>Products:</h3>
<?php while($item=mysqli_fetch_assoc($items_res)):
$total = $item['price']*$item['qty'];
?>
<div class="product-item">
    <?php if(!empty($item['image']) && file_exists('image/'.$item['image'])): ?>
        <img src="image/<?php echo $item['image']; ?>" alt="<?php echo $item['product_name']; ?>">
    <?php else: ?>
        <div style="width:130px;height:130px;background:#eee;display:flex;align-items:center;justify-content:center;border-radius:15px;">No Image</div>
    <?php endif; ?>
    <div>
        <div class="product-name"><?php echo $item['product_name']; ?></div>
        <div class="product-meta">Qty: <?php echo intval($item['qty']); ?> • ₹<?php echo number_format($item['price'],2); ?> • Total: ₹<?php echo number_format($total,2); ?></div>
    </div>
</div>
<?php endwhile; ?>

<p><strong>Grand Total:</strong> ₹<?php echo number_format($order['total_amount'],2); ?></p>

<div class="actions">
    <a class="btn invoice" href="invoice.php?order_id=<?php echo $order_id; ?>" target="_blank">Download Invoice</a>
    <?php if(in_array($order['order_status'], ['Ordered','Packed'])): ?>
    <form method="POST" style="display:inline;">
        <button type="submit" name="cancel_order" class="btn cancel">Cancel Order</button>
    </form>
    <?php endif; ?>
</div>

<h3>Order Tracking:</h3>
<div class="tracking-timeline">
<?php foreach($tracking_events as $event): ?>
    <div class="tracking-event">
        <div class="tracking-date"><?php echo date('D, d M Y H:i', strtotime($event['datetime'])); ?></div>
        <div class="tracking-status"><?php echo $event['status']; ?></div>
        <?php if(!empty($event['courier'])): ?>
            <div class="tracking-courier"><?php echo $event['courier']; ?> - <?php echo $event['tracking_id']; ?></div>
        <?php endif; ?>
        <div class="tracking-message"><?php echo $event['message']; ?></div>
    </div>
<?php endforeach; ?>
</div>

</div> <!-- main container -->
</div> <!-- main-content -->
</body>
</html>
