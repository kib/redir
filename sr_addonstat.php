<?php
include("functions.php");
$addon="";
if (isset($_GET['addon']) && !empty($_GET['addon'])){
    $con= db_connect();
    $addon = $con->real_escape_string($_GET['addon']);
    $con->close();
}
if ($addon==""){
    echo "no addon specified"; 
}
else{
    $con=new mysqli($server,$user,$password,$database);
    if ($res = $con->query("SELECT SUM(stats) FROM download WHERE addonid='".$addon."'")){
        $row = $res->fetch_row();
	$res->close();
        printf(number_format((float)$row[0],0,",","."));
        }
    $con->close();
}
?>
