<?php
session_start();
include "../db.php";
if(isset($_SESSION['user_id'])){
    if($_SESSION['user_role']=='admin'){
        $sql="select * from products";
        $result= mysqli_query($conn,$sql);
          if(!$result){
                 echo "Error!: {$conn->error}";
           }else{
            }
        }else{
            header("Location:../index.php");
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
            <li><a href="dashbord.php">admin</a></li>
            <li><a href="data_show.php">Data show</a></li>
            <li><a href="add_product.php">Add Product</a></li>
            <li><a href="displayproduct.php">View products</a></li>
            <li><a href="pro_cat.php">Product Categray</a></li>
            <li><a href="../logout.php">logout</a></li>
        </ul>
    </div>
    <div class="dashs">
           <table>
        <thead>
            <tr>
                 <th>Product title</th>
                 <th>Prodect description</th>
                 <th>Price</th>
                 <th>Stock</th>
                 <th>Image</th>
                 <th>Category Name</th>
                 <th>Action</th>
                 <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row=mysqli_fetch_assoc($result)){
            ?>
            <tr>

                <td><?php echo $row['name'] ?></td>
                <td><?php echo $row['description'] ?></td>
                <td><?php echo $row['price'] ?></td>
                <td><?php echo $row['stock'] ?></td>
                <td><img src="../image/<?php echo $row['image'] ?>" alt=""></td>
                <td><?php echo $row['category_name'] ?></td>

               <td><a class="update" href="update.php?
                pro_id=<?php echo $row['id'] ?>" >Update</a></td>
                <td><a class="delete" href="delete.php?
                pro_id=<?php echo $row['id'] ?>" >Delete</a></td>
            </tr>
            <?php
               }
               ?>
        </tbody>
    </table>
    </div>
</body>
</html>