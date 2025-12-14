<?php
session_start();
include "../db.php";
if(isset($_SESSION['user_id'])){
    if($_SESSION['user_role']=='admin'){
         
    }
    else{
        header("Location: ../dashbord.php");
    }

}else{
    header("Location: ../index.php");
}
$sql = "SELECT * FROM users";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="dashbords.css">
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

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Mobile</th>
        <th>Address</th>
        <th>Role</th>
        <th>Role_change</th>
        <th>Add</th>
        <th>Delete</th>
    </tr>

<?php
while($row = mysqli_fetch_assoc($result)){
    echo "<tr>
            <td>".$row['id']."</td>
            <td>".$row['name']."</td>
            <td>".$row['email']."</td>
            <td>".$row['mobile']."</td>
            <td>".$row['address']."</td>
            <td>".$row['role']."</td>

            <td><a class='change' href='role_change.php?id=".$row['id']."'>Change</a></td>

            <td><a class='add' href='add_user.php'>Add</a></td>

            <td><a class='delete' href='delete_user.php?id=".$row['id']."' 
                   onclick=\"return confirm('Delete this user?');\">Delete</a></td>
          </tr>";
}
?>
</table>

</body>
</html>