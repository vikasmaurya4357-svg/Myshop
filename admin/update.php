<?php
session_start();
include "../db.php";
if(isset($_SESSION['user_id'])){
    if($_SESSION['user_role']=='admin'){
        $sql1 ="select * from categories";
         $result1 = mysqli_query($conn,$sql1);
         if(isset($_GET['pro_id'])){
            $product_id=$_GET['pro_id'];
            $sql2="select * from products where id='$product_id' ";
            $result2=mysqli_query($conn,$sql2);
            $row2=mysqli_fetch_assoc($result2);
         }
        
       
        if(isset($_POST['submit'])){
            $product_id=$_GET['pro_id'];
            $name=$_POST['name'];
            $description=$_POST['description'];
            $price=$_POST['price'];
            $stock=$_POST['stock'];
             $sql3="Update products set name = '$name', description='$description', price='$price',stock='$stock' 
                     where id='$product_id'";
             $result3=mysqli_query($conn,$sql3);
             if($result3){
                header("Location: displayproduct.php");
             } else{
                echo "ERROR !:{$CONN->error}";
             }      

            $image = $_FILES['image']['name'];
            if($image){
             $tem_location =$_FILES['image']['tmp_name'];
             $upload_location="../image/";
             $sql4="Update products set name = '$name' , description='$description',price='$price',stock='$stock' ,image='$image' where id='$product_id'";
            $result4=mysqli_query($conn,$sql4);
             if($result4){
                move_uploaded_file($tem_location,$upload_location.$image);
                header("Location: displayproduct.php");
             } else{
                echo "ERROR !:{$CONN->error}";
             }       
            }
          
            $category_name=$_POST['category_name'];
              if($category_name){
             $sql5="update products set name = '$name' ,description='$description',price='$price',stock='$stock' 
                     ,image='$image', category_name='$category_name'  where id='$product_id'";
            $result5=mysqli_query($conn,$sql5);
             if($result5){
                header("Location: displayproduct.php");
             } else{
                echo "ERROR !:{$CONN->error}";
             }       
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
             <form action="update.php?pro_id=<?php echo  $product_id ; ?>" method="post" enctype="multipart/form-data">
                <input type="text" name="name" value="<?php echo $row2['name'];?>">
                <textarea name="description">
                  <?php echo $row2['description'];?>
                </textarea>
                <input type="number" name="price" value="<?php echo $row2['price'];?>" >
                <input type="number" name="stock" value="<?php echo $row2['stock'];?>">

                <img src="../image/<?php echo $row2['image'];?>" alt="">
                <input type="file" name="image">

                <h1>Category Name Is: <?php echo $row2['category_name'];?></h1>
                <select name="category_name" >
                     <?php while($row=mysqli_fetch_assoc($result1)) 
                        {?>
                    <option value="<?php echo $row['name']; ?>"><?php echo $row['name']; ?></option>
                 <?php }?>
                </select>
               
                <input class="button" type="submit" name="submit" value="Update product">
             </form>
    </div>
</body>
</html>