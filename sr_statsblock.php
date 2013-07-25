<?php
include("functions.php");

$today = strtotime(date("Y-m-d")); 
$amount = file_get_contents('http://www.mirrorservice.org/sites/addons.superrepo.org/Frodo/.metadata/amount');

$con  = db_connect();
$res = $con->query("SELECT * FROM userstats");
$users=$res->num_rows;
$res->close();

$res=$con->query("SELECT MIN(firstseen) FROM userstats");
$row=$res->fetch_row();
$userssince=gmdate("Y-m-d", $row[0]);

$thisweek = get_this_week();
$alltime = get_all_times();

echo 
"<table border='1' align='center' style='font-size:13px'>
<tr><td style='width:400px; font-weight:bold;'>Addons</td><td style='text-align:center'>" . $amount ."</td></tr>
<tr><td style='width:400px; font-weight:bold;'>Unique users since ".$userssince."</td><td style='text-align:center'>" . $users ."</td></tr>
<tr><td style=' font-weight:bold;'>Downloads last 7 days</td><td style='text-align:center'> ".$thisweek." </td></tr>
<tr><td style=' font-weight:bold;'>Downloads since 2013-07-22</td><td style='text-align:center'> ".$alltime." </td></tr>
</table>";
?>
