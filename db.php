<?php
$conn=new mysqli('localhost','root','','online');
if(!$conn){
    echo "ERROR!:{$conn->connect_error}";
}
?>