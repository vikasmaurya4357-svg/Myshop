<?php
session_start();
include "../db.php";

if(isset($_SESSION['user_id'])){
    if($_SESSION['user_role'] == "admin"){
           $id = $_GET['id'];

           $sql = "DELETE FROM users WHERE id='$id'";
           mysqli_query($conn, $sql);

           header("Location: dashboard.php");
           exit();
    }else{
        echo header("Location:../index.php");
    }

}else{
    header("Location:../index.php");
}
?>
