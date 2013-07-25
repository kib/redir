<?php
$addon=$_GET['addon'];
//$addon="skin.neon";
if ($addon==""){
    echo "error"; 
}else{
    include("connect.php");
    $con=new mysqli($server,$user,$password,$database);
    if ($res = $con->query("SELECT SUM(stats) FROM download WHERE addonid='$addon'")){
        $row = $res->fetch_row();
        printf(number_format((float)$row[0],0,",","."));
        }
    $con->close();
}
?>
