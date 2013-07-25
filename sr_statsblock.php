<?php

include("connect.php");
$link = mysql_connect ($server, $user, $password);
mysql_select_db($database, $link);

$today = strtotime(date("Y-m-d")); 

$amount = file_get_contents('http://www.mirrorservice.org/sites/addons.superrepo.org/Frodo/.metadata/amount');

function time_query($startTime,$endTime){
$result = mysql_query("SELECT SUM(stats) FROM download WHERE dldate BETWEEN '".$startTime."' and '".$endTime."'") or die(mysql_error());
$row = mysql_fetch_row($result); // get an array containing the value of the first row of the above query
$sum = round((int) $row[0]); // get an integer containing the value of the first (and, here, only) item in that row
$sum = number_format($sum, 0, ',', '.');


echo "$sum";
}

function get_last_24(){
$startTime = time() - 24*3600;     
$endTime = time();  
time_query($startTime,$endTime);
}

 
function get_yesterday(){
$startTime = time(0, 0, 0, date('m'), date('d')-1, date('Y'));     
$endTime = time(23, 59, 59, date('m'), date('d')-1, date('Y'));     
time_query($startTime,$endTime);
}


function get_this_week(){
$startTime = time() - 7*24*3600;   
$endTime = time();   
time_query($startTime,$endTime);
}

function get_last_week(){
$startTime = time(0, 0, 0, date('n'), date('j')-6, date('Y')) - ((date('N'))*3600*24);     
$endTime = time(23, 59, 59, date('n'), date('j'), date('Y')) - ((date('N'))*3600*24);   
time_query($startTime,$endTime);
}



function get_last_month(){
$startTime = time() - 30*3600*24;     
$endTime = time();
time_query($startTime,$endTime);
}

function get_all_times(){
$startTime = 0;     
$endTime = time();
time_query($startTime,$endTime);
}


$result=mysql_query("SELECT * FROM userstats");
$users=mysql_num_rows($result);

$result=mysql_query("SELECT MIN(firstseen) FROM userstats");
$rows=mysql_fetch_row($result);
$userssince=gmdate("Y-m-d", $rows[0]);

echo "<table border='1' align='center' style='font-size:13px'>";
echo "<tr><td style='width:400px; font-weight:bold;'>Addons</td><td style='text-align:center'>" . $amount ."</td></tr>";
echo "<tr><td style='width:400px; font-weight:bold;'>Unique users since ".$userssince."</td><td style='text-align:center'>" . $users ."</td></tr>";
echo "<tr><td style=' font-weight:bold;'>Downloads last 7 days</td><td style='text-align:center'>"; echo get_this_week(); echo "</td></tr>";
echo "<tr><td style=' font-weight:bold;'>Downloads since 2013-07-22</td><td style='text-align:center'>"; echo get_all_times(); echo "</td></tr>";
echo "</table>"

?>





 
