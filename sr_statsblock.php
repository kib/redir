<?php
include("functions.php");

$today = strtotime(date("Y-m-d")); 
$amount = file_get_contents('http://www.mirrorservice.org/sites/addons.superrepo.org/Frodo/.metadata/amount');

$con  = db_connect();
$res = $con->query("SELECT COUNT(*) FROM userstats");
$row=$res->fetch_row();
$users=$row[0];
$res->close();

$res=$con->query("SELECT MIN(firstseen) FROM userstats");
$row=$res->fetch_row();
$userssince=gmdate("Y-m-d", $row[0]);
$res->close();

$res=$con->query("SELECT MIN(dldate) FROM download");
$row=$res->fetch_row();
$downloadssince=gmdate("Y-m-d", $row[0]);
$res->close();

$con->close();

echo 
"<table border='1' align='center' style='font-size:13px'>
<tr><td style='width:400px; font-weight:bold;'>Addons</td><td style='text-align:center'>" . $amount ."</td></tr>
<tr><td style='width:400px; font-weight:bold;'>Unique users since ".$userssince."</td><td style='text-align:center'>" . $users ."</td></tr>
<tr><td style=' font-weight:bold;'>Downloads last 7 days</td><td style='text-align:center'> ".get_this_week()." </td></tr>
<tr><td style=' font-weight:bold;'>Downloads since ".$downloadssince."</td><td style='text-align:center'> ".get_all_times()." </td></tr>
</table>";
?>
