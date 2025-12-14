<?php
session_start();
include "../db.php";
if(isset($_SESSION['user_id'])){
    if($_SESSION['user_role'] == "admin"){
       if(isset($_POST['save'])){
           $name = $_POST['name'];
           $email = $_POST['email'];
            $mobile = $_POST['mobile'];
           $address = $_POST['address'];
            $role = $_POST['role'];
           $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (name, email, mobile, address, role, password)
            VALUES ('$name', '$email', '$mobile', '$address', '$role', '$password')";

           mysqli_query($conn, $sql);

              echo "<script>alert('User Added Successfully'); window.location='dashboard.php';</script>";
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
<title>Add User</title>
 <link rel="stylesheet" href="dashbord.css">


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
<h2>Add New User</h2>

<form method="POST" action="">
    <input type="text" name="name" placeholder="Full Name" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="text" name="mobile" placeholder="Mobile" required><br><br>
    <input type="text" name="address" placeholder="Address"><br><br>
    <select name="role" required>
    <option value="">Select Role</option>
    <option value="user">User</option>
    <option value="admin">Admin</option>
</select>
<br><br>

    <input type="password" name="password" placeholder="Password" required><br><br>

    <button type="submit" name="save"  class="btn-add">Add User</button>
    <a href="dashboard.php" class="btn-back">Back</a>
</form>


</body>
</html>
