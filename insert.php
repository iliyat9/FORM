
<?php
include 'conn.php';
$x=$_POST["first_name"];
$y=$_POST["last_name"];
$s=$_POST["father_name"];
$h=$_POST["user_name"];
$f=$_POST["pas"];

$servername="localhost";
$username="root";
$password="";
$database="beik";
$conn= connect("localhost","root","","beik");
function ql($x , $y , $s , $h , $f,$conn)
{

 $hash = password_hash($f, PASSWORD_DEFAULT);
 $q="INSERT INTO saved (first_name, last_name, father_name, user_name, pas)
 VALUES ('$x' , '$y' , '$s' , '$h' , '$hash')";
 mysqli_query($conn,$q);
}
 ql($x, $y, $s , $h, $f,$conn);
?>