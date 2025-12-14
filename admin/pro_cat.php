<?php
session_start();
include "../db.php";
if(isset($_SESSION['user_id'])){
    if($_SESSION['user_role']=='admin'){
        if(isset($_POST['submit'])){
            $name=$_POST['name'];
            $sql="INSERT INTO categories(name) VALUES('$name') ";
            $result = mysqli_query($conn,$sql);

          if(!$result){
                 echo "Error!: {$conn->error}";
           }else{
              echo "product added successfully ";
            }
        }

    }
    else{
        header("Location: ../index.php");
    }

}else{
    header("Location: ../index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="dashaA.css">
</head>
<body>
    <div class="dashboard">
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="dashboard.php">admin</a></li>
            <li><a href="data_show.php">Data show</a></li>
            <li><a href="add_product.php">Add Product</a></li>
            <li><a href="displayproduct.php">View Order</a></li>
            <li><a href="pro_cat.php">Product Categray</a></li>
            <li><a href="../logout.php">logout</a></li>
        </ul>
    </div>
    <div class="dash">
             <form action="pro_cat.php" method="post" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Enter your product Name" required>
                <input class="button" type="submit" name="submit" value="Add product">
             </form>
    </div>
</body>
</html>