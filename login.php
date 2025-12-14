<?php
include "db.php";
session_start();
if(isset($_POST['submit'])){
    $email=$_POST['email'];
    $password=$_POST['password'];
    $sql="select * from users where email='$email'";
    $result=mysqli_query($conn,$sql);
    if($result->num_rows > 0){
        $row = mysqli_fetch_assoc($result);
        if(password_verify($password, $row['password'])){
            $_SESSION['user_id']=$row['id'];
            $_SESSION['user_name']=$row['name'];
            $_SESSION['user_role']=$row['role'];
            if($_SESSION['user_role']=='admin'){
                header("Location: admin/dashboard.php");
            }else{
                
                header("Location: index.php");
            }
        }
        else{
            echo "wrong password";
        }
       
    }else{
        
        echo 'Please! Go for Sign Up';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="loginss.css">
</head>
<body>
    <div class="login">
    <form action="login.php" method="post">
        <input type="email" name="email" placeholder="Enter your email" required>
        <input type="password" name="password" placeholder="Enter your password" required>
        <input class="button "type="submit" name="submit" value="login">
        <a href="forgot_password.php">Forgot Password?</a>
        <p>Don't Register Yet!  <a href="register.php">Sign Up</a></p>
    </form>
   </div>
</body>
</html>