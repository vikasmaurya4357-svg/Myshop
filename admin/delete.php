<?php
session_start();
include "../db.php";
if(isset($_SESSION['user_id'])){
    if($_SESSION['user_role'] == "admin"){
          if(isset($_GET['pro_id'])){
                 $pro_id=$_GET['pro_id'];
                 $sql="delete from products where id='$pro_id' ";
                 $result=mysqli_query($conn,$sql);
                 if(!$result){
                    echo "ERROR!: {$CONN->$error}";
                 }
                 else{
                    header("Location:displayproduct.php");
                 }
       }
    }else{
        echo header("Location:../index.php");
    }
   


}else{
    header("Location:../index.php");
}
?>