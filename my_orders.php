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

// Handle Cancel Order
if(isset($_POST['cancel_order']) && !empty($_POST['order_id'])) {
    $order_id = intval($_POST['order_id']);
    $reason = mysqli_real_escape_string($conn, trim($_POST['cancel_reason'] ?? ''));
    $check = mysqli_query($conn, "SELECT order_status FROM orders WHERE id='$order_id' AND user_id='$user_id' LIMIT 1");
    if($check && mysqli_num_rows($check) > 0){
        $r = mysqli_fetch_assoc($check);
        $st = $r['order_status'];
        if(in_array($st, ['Ordered','Packed'])){
            $now = date('Y-m-d H:i:s');
            $reason_sql = $reason ? ", cancel_reason='{$reason}'" : "";
            mysqli_query($conn, "UPDATE orders SET order_status='Cancelled', refund_status='Pending', cancel_requested_at='$now' $reason_sql WHERE id='$order_id' AND user_id='$user_id'");
            $_SESSION['order_msg'] = "Order #$order_id cancelled. Refund will be processed per policy.";
        } else {
            $_SESSION['order_msg'] = "Order #$order_id cannot be cancelled (current status: $st).";
        }
    } else {
        $_SESSION['order_msg'] = "Order not found.";
    }
    header("Location: my_orders.php");
    exit;
}

// Filter
$filter = $_GET['filter'] ?? 'all';
$filter_sql = "";
if($filter==='delivered') $filter_sql = " AND order_status='Delivered'";
elseif($filter==='cancelled') $filter_sql = " AND order_status='Cancelled'";
elseif($filter==='inprogress') $filter_sql = " AND order_status IN ('Ordered','Packed','Shipped','Out for Delivery')";

$orders_res = mysqli_query($conn, "SELECT * FROM orders WHERE user_id='$user_id' $filter_sql ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>My Orders - MyShop</title>
<link rel="stylesheet" href="my_order.css">
</head>
<body>

<?php include 'share.php'; ?>


<div class="main-content">
<?php if(!empty($_SESSION['order_msg'])): ?>
  <div class="msg"><?php echo $_SESSION['order_msg']; unset($_SESSION['order_msg']); ?></div>
<?php endif; ?>

<?php if(mysqli_num_rows($orders_res)==0): ?>
  <div class="empty">No orders found.</div>
<?php else: ?>
  <?php while($order=mysqli_fetch_assoc($orders_res)):
        $oid=$order['id'];
        $items_q=mysqli_query($conn,"SELECT * FROM order_items WHERE order_id='$oid'");
        $status=$order['order_status'];
        $d_delivered = isset($order['delivery_date']) && !empty($order['delivery_date']) 
                        ? date('d M Y', strtotime($order['delivery_date'])) 
                        : date('d M Y', strtotime('+4 day',strtotime($order['created_at']))); 
  ?>
  <div class="order-card">
      <?php mysqli_data_seek($items_q,0); while($it=mysqli_fetch_assoc($items_q)):
          $total_price = $it['price']*$it['qty'];
      ?>
     <div class="product-item">
    <?php if(!empty($it['image']) && file_exists('image/'.$it['image'])): ?>
        <a href="order_detail.php?order_id=<?php echo $oid; ?>">
            <img src="image/<?php echo htmlspecialchars($it['image']); ?>" alt="<?php echo htmlspecialchars($it['product_name']); ?>">
        </a>
    <?php else: ?>
        <div style="width:130px;height:130px;background:#eee;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#666;font-size:14px;">No Image</div>
    <?php endif; ?>
    <div>
        <a href="order_detail.php?order_id=<?php echo $oid; ?>" style="text-decoration:none;color:inherit;">
            <div class="product-name"><?php echo htmlspecialchars($it['product_name']); ?></div>
        </a>
        <div class="product-meta">Qty: <?php echo intval($it['qty']); ?> • ₹<?php echo number_format($it['price'],2); ?> • Total: ₹<?php echo number_format($total_price,2); ?></div>
    </div>
</div>

      <?php endwhile; ?>
  </div>
  <?php endwhile; ?>
<?php endif; ?>

</div>
</body>
</html>
