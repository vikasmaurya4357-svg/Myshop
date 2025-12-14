<?php
include "../db.php";

// Current date, week, month, year
$today = date('Y-m-d');
$weekStart = date('Y-m-d', strtotime('monday this week'));
$monthStart = date('Y-m-01');
$yearStart = date('Y-01-01');

// Helper function to safely get single value
function get_single_value($conn, $sql){
    $res = mysqli_query($conn, $sql);
    if($res){
        $row = mysqli_fetch_assoc($res);
        return $row[array_keys($row)[0]] ?? 0;
    } else {
        return 0;
    }
}

//  Orders & Sales Metrics
$today_sales = get_single_value($conn, "SELECT SUM(total_amount) AS total FROM orders WHERE DATE(created_at)='$today' AND order_status='completed'");
$today_orders = get_single_value($conn, "SELECT COUNT(*) AS total FROM orders WHERE DATE(created_at)='$today'");
$week_sales = get_single_value($conn, "SELECT SUM(total_amount) AS total FROM orders WHERE DATE(created_at)>='$weekStart' AND order_status='completed'");
$month_sales = get_single_value($conn, "SELECT SUM(total_amount) AS total FROM orders WHERE DATE(created_at)>='$monthStart' AND order_status='completed'");
$year_sales = get_single_value($conn, "SELECT SUM(total_amount) AS total FROM orders WHERE DATE(created_at)>='$yearStart' AND order_status='completed'");
$total_sales = get_single_value($conn, "SELECT SUM(total_amount) AS total FROM orders WHERE order_status='completed'");

$pending_orders = get_single_value($conn, "SELECT COUNT(*) AS total FROM orders WHERE order_status='pending'");
$completed_orders = get_single_value($conn, "SELECT COUNT(*) AS total FROM orders WHERE order_status='completed'");
$cancelled_orders = get_single_value($conn, "SELECT COUNT(*) AS total FROM orders WHERE order_status='cancelled'");

// Payment Metrics 
$payment_methods = [];
$res = mysqli_query($conn, "SELECT payment_method, COUNT(*) as total FROM orders GROUP BY payment_method");
if($res){
    while($row = mysqli_fetch_assoc($res)){
        $payment_methods[$row['payment_method']] = $row['total'];
    }
}
$pending_payments = get_single_value($conn, "SELECT COUNT(*) AS total FROM orders WHERE payment_status='pending'");
$failed_payments = get_single_value($conn, "SELECT COUNT(*) AS total FROM orders WHERE payment_status='failed'");

//Customer Metrics
$total_users = get_single_value($conn, "SELECT COUNT(*) AS total FROM users");
$new_users_today = get_single_value($conn, "SELECT COUNT(*) AS total FROM users WHERE DATE(created_at)='$today'");
$new_users_month = get_single_value($conn, "SELECT COUNT(*) AS total FROM users WHERE DATE(created_at)>='$monthStart'");

//Top Customers
$top_customers_res = mysqli_query($conn, "SELECT u.name, SUM(o.total_amount) as spent 
                                          FROM orders o 
                                          JOIN users u ON o.user_id=u.id 
                                          WHERE o.order_status='completed' 
                                          GROUP BY o.user_id 
                                          ORDER BY spent DESC 
                                          LIMIT 5");

//Product / Inventory Metrics
$total_products = get_single_value($conn, "SELECT COUNT(*) AS total FROM products");
$low_stock_res = mysqli_query($conn, "SELECT name, stock FROM products WHERE stock<10");

//Sales Trend Data (Chart.js)
$sales_trend_res = mysqli_query($conn, "SELECT DATE(created_at) as day, SUM(total_amount) as total 
                                        FROM orders 
                                        WHERE order_status='completed' 
                                        AND DATE(created_at) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
                                        GROUP BY DATE(created_at)");
$chart_labels = [];
$chart_data = [];
if($sales_trend_res){
    while($row = mysqli_fetch_assoc($sales_trend_res)){
        $chart_labels[] = $row['day'];
        $chart_data[] = $row['total'];
    }
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="data_show.css">
</head>
<body>

<div class="dashboard">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="dashboard.php">admin</a></li>
            <li><a href="data_show.php">Data show</a></li>
            <li><a href="add_product.php">Add Product</a></li>
            <li><a href="displayproduct.php">View products</a></li>
            <li><a href="pro_cat.php">Product Categray</a></li>
            <li><a href="../logout.php">logout</a></li>
            
        </ul>
    </div>


<div class="dash">
    <div class="card"><p>Today's Sales</p><h2>₹<?= number_format($today_sales,2) ?></h2></div>
    <div class="card"><p>Today's Orders</p><h2><?= $today_orders ?></h2></div>
    <div class="card"><p>This Week's Sales</p><h2>₹<?= number_format($week_sales,2) ?></h2></div>
    <div class="card"><p>This Month's Sales</p><h2>₹<?= number_format($month_sales,2) ?></h2></div>
    <div class="card"><p>This Year's Sales</p><h2>₹<?= number_format($year_sales,2) ?></h2></div>
    <div class="card"><p>Total Sales</p><h2>₹<?= number_format($total_sales,2) ?></h2></div>
    <div class="card"><p>Pending Orders</p><h2><?= $pending_orders ?></h2></div>
    <div class="card"><p>Completed Orders</p><h2><?= $completed_orders ?></h2></div>
    <div class="card"><p>Cancelled Orders</p><h2><?= $cancelled_orders ?></h2></div>
</div>

<h2>Payment Metrics</h2>
<table>
    <tr><th>Payment Method</th><th>Total Orders</th></tr>
    <?php foreach($payment_methods as $method=>$total){ ?>
        <tr><td><?= $method ?></td><td><?= $total ?></td></tr>
    <?php } ?>
    <tr><td>Pending Payments</td><td><?= $pending_payments ?></td></tr>
    <tr><td>Failed Payments</td><td><?= $failed_payments ?></td></tr>
</table>

<h2>Top Customers</h2>
<table>
<tr><th>Name</th><th>Total Spent</th></tr>
<?php while($row = mysqli_fetch_assoc($top_customers_res)){ ?>
<tr><td><?= $row['name'] ?></td><td>₹<?= number_format($row['spent'],2) ?></td></tr>
<?php } ?>
</table>

<h2>Product Inventory</h2>
<table>
<tr><th>Product Name</th><th>Stock</th></tr>
<?php while($row = mysqli_fetch_assoc($low_stock_res)){ ?>
<tr><td><?= $row['name'] ?></td><td><?= $row['stock'] ?></td></tr>
<?php } ?>
</table>

<<!-- Button to open chart page -->
<div style="text-align:center; margin: 40px 0;">
    <a href="sales_chart.php" class="btn-chart">View Sales Trend</a>
</div>

</body>
</html>
