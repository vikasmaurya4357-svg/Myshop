<?php
include "../db.php";

// Get sales trend data (last 30 days)
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
<title>Sales Trend - Last 30 Days</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link rel="stylesheet" href="data_show.css">
<style>
    h1 {
    width: 900px;
    margin: 30px auto;
    padding: 25px;
    text-align: center;
    margin-top: 30px;
    color: #333;
    box-shadow: 5px 5px 5px ;
    background: #c2fef9ff;
    
}
</style>
</head>
<body>

<div class="dashboard">
    <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="dashboard.php">Back to Dashboard</a></li>
            <li><a href="dashboard.php">admin</a></li>
            <li><a href="data_show.php">Data show</a></li>
            <li><a href="add_product.php">Add Product</a></li>
            <li><a href="displayproduct.php">View products</a></li>
            <li><a href="pro_cat.php">Product Categray</a></li>
            <li><a href="../logout.php">logout</a></li>
    </ul>
</div>

<div style="margin-left: 280px; padding: 40px;">
    <h1>Sales Trend (Last 30 Days)</h1>
    <div class="chart-container-full">
        <canvas id="salesChart" height="400"></canvas>
    </div>
</div>

<script>
const ctx = document.getElementById('salesChart').getContext('2d');
const salesChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chart_labels) ?>,
        datasets: [{
            label: 'Daily Sales (₹)',
            data: <?= json_encode($chart_data) ?>,
            borderColor: 'rgba(75, 192, 192, 1)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            fill:true,
            tension:0.3
        }]
    },
    options: {
        responsive:true,
        plugins: {
            legend:{ display:true, position:'top' }
        },
        scales: { y:{ beginAtZero:true } }
    }
});
</script>

</body>
</html>
