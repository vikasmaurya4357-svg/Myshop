<?php
include "db.php";
if(isset($_POST['submit'])){
    $name=$_POST['name'];
    $email=$_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone=$_POST['phone'];
    $address=$_POST['address'];
    $role="user";
     $sql="insert into users(name,email,password,phone,address,role)
            values('$name','$email','$password','$phone','$address','$role')";
      $result=mysqli_query($conn,$sql);
        if(!$result)
        {
            echo "ERROR! :{$conn->error}";
        } 
        else
        {
             header("Location:index.php");
        }     
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="registers.css">
</head>
<body>
    <div class="registerdiv">
            <form action="register.php" method="post">
                 <input type="text" name="name" placeholder="Enter you name " required>
                   <input type="email" name="email" placeholder="Enter you Email " required>
                  <input type="password" name="password" placeholder="Enter you password " required>
                   <input type="text" name="phone" placeholder="Enter you phone number " required>
                   <textarea name="address" placeholder="enter your Address"></textarea>
                    <input class="button" type="submit" name="submit" value="Sign Up" >
                    <p>go for login <a href="login.php">Login</a></p>
             </form>      
    </div>
</body>
</html>