<?php
include("connect.php");
// reusable functions
function db_connect($host,$user,$pass,$db) {
   $mysqli = new mysqli($host, $user, $pass, $db);
   if($mysqli->connect_error) 
     die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
   return $mysqli;
}
function time_query($startTime,$endTime){
    global $addon;
    global $server;
    global $user;
    global $password;
    global $database;
    $con=db_connect($server,$user,$password,$database);
    $res = $con->query("SELECT SUM(stats) FROM download WHERE addonid='".$addon."' AND dldate BETWEEN '".$startTime."' and '".$endTime."'") or die(mysql_error());
    $row = $res->fetch_row(); 
    $sum = (int) $row[0]; 
    $res->close();
    $con->close();
    return $sum;
}
function get_last_24(){
$startTime = time() - 24*3600;     
$endTime = time();  
return time_query($startTime,$endTime);
}
function get_yesterday(){
$startTime = mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));     
$endTime = mktime(23, 59, 59, date('m'), date('d')-1, date('Y'));     
return time_query($startTime,$endTime);
}
function get_this_week(){
$startTime = time() - 7*24*3600;   
$endTime = time();   
return time_query($startTime,$endTime);
}
function get_last_week(){
$startTime = mktime(0, 0, 0, date('n'), date('j')-6, date('Y')) - ((date('N'))*3600*24);     
$endTime = mktime(23, 59, 59, date('n'), date('j'), date('Y')) - ((date('N'))*3600*24);   
return time_query($startTime,$endTime);
}
function get_last_month(){
$startTime = time() - 30*3600*24;     
$endTime = time();
return time_query($startTime,$endTime);
}
function get_all_times(){
$startTime = 0;     
$endTime = time();
return time_query($startTime,$endTime);
}
?>
