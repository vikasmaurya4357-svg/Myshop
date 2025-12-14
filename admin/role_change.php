<?php
session_start();
include "../db.php";

if(isset($_SESSION['user_id'])){
    if($_SESSION['user_role'] == "admin"){
         $id = $_GET['id'];

// STEP 1: Fetch user details
$sql = "SELECT * FROM users WHERE id='$id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// STEP 2: If admin selects new role
if(isset($_POST['change_role'])){
    $new_role = $_POST['role'];

    mysqli_query($conn, "UPDATE users SET role='$new_role' WHERE id='$id'");

     header("Location: dashboard.php");
    exit;
}
    }else{
        echo header("Location:../index.php");
    }
   


}else{
    header("Location:../index.php");
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Role Change</title>
<link rel="stylesheet" href="dashaA.css">
<style>
h2 {
    width: 400px;
    margin: 30px auto;
    padding: 25px;
    text-align: center;
    margin-top: 30px;
    color: #333;
    box-shadow: 5px 5px 5px ;
    background: #c2fef9ff;
    
}h3 {
    
    margin: 30px auto;
    padding: 25px;
    text-align: center;
    margin-top: 30px;
      
}
.box{
    width: 400px;
    margin: 80px auto;
    background: white;
    padding: 100px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
button{
    padding: 12px 20px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    margin:10px;
    font-size:16px;
    color:white;
}
.user-btn{ background:#1976D2; }
.admin-btn{ background:#43A047; }
.back-btn{ background:#555; }
</style>

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
    <h2>Change Roler</h2>
    
<div class="box">
    
    <h3>Name : <?= $user['name'] ?></h3>
    <p>Current Role: <b><?= $user['role'] ?></b></p>

    <!-- STEP 3: Buttons to choose new role -->
    <form method="POST">

        <input type="hidden" name="role" value="">

        <button type="submit" name="change_role"
                class="user-btn"
                onclick="this.form.role.value='user'">
            Make User
        </button>

        <button type="submit" name="change_role"
                class="admin-btn"
                onclick="this.form.role.value='admin'">
            Make Admin
        </button>

    </form>

    <br>
    <a href="dashboard.php">
        <button class="back-btn">Back</button>
    </a>
</div>

</body>
</html>




    