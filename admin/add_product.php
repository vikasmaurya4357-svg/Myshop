<?php
session_start();
include "../db.php";
if(isset($_SESSION['user_id'])){
    if($_SESSION['user_role']=='admin'){
        $sql1 ="select * from categories";
        $result1 = mysqli_query($conn,$sql1);
        if(isset($_POST['submit'])){ 
          
            $name            = mysqli_real_escape_string($conn, $_POST['name']);
            $description     = mysqli_real_escape_string($conn, $_POST['description']);
            $price           = mysqli_real_escape_string($conn, $_POST['price']);
            $stock           = mysqli_real_escape_string($conn, $_POST['stock']);
            $category_name   = mysqli_real_escape_string($conn, $_POST['category_name']);

            $image = $_FILES['image']['name'];
            $tem_location = $_FILES['image']['tmp_name'];
            $upload_location = "../image/";
            
            
            $sql=" insert into products(name,description,price,stock,image,category_name)
                   values('$name','$description','$price','$stock','$image','$category_name') ";
           
            $result = mysqli_query($conn,$sql);
          if(!$result){
                 echo "Error!: {$conn->error}";
           }else{
              echo "product added successfully ";
              move_uploaded_file($tem_location,$upload_location.$image);
            }
        }

    }
    else{
        echo "go for user dashbord";
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
            <li><a href="displayproduct.php">View products</a></li>
            <li><a href="pro_cat.php">Product Categray</a></li>
            <li><a href="../logout.php">logout</a></li>
        </ul>
    </div>
    <div class="dash">
             <form action="add_product.php" method="post" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Enter your product Name" required>
                <textarea name="description"  placeholder="Enter your product Description" ></textarea>
                <input type="number" name="price" placeholder="Enter price here!" required>
                <input type="number" name="stock" placeholder="Enter stock number here!" required>
                <h2>Upload Image Here!</h2>
                <input type="file" name="image">
                <select name="category_name" >
                     <?php while($row=mysqli_fetch_assoc($result1)) 
                        {?>
                          
                    <option value="<?php echo $row['name']; ?>"><?php echo $row['name']; ?></option>
                 <?php }?>
                </select> 
                <input class="button" type="submit" name="submit" value="Add product">
             </form>
    </div>
</body>
</html>