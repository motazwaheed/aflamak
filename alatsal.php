<?php
$con = mysqli_connect('localhost','root','','film');
if(!$con){
    die('خطأ في الاتصال بقاعدة البيانات: ' . mysqli_connect_error());
}
mysqli_set_charset($con, "utf8");
?>