<?php

$time_start = microtime(true);
$today = strtotime(date("Y-m-d"));

include("functions.php");	
$con = db_connect();

if ($res = $con->query("SELECT * FROM addonstats WHERE `date` = '".$today."'")) {
    while ($row = $res->fetch_row()) {
    	$addonid = $row[0];
    	$addonstats = $row[2];
	$sql="INSERT INTO download (addonid, dldate, stats) VALUES ('".$addonid."',".$today.",'".$addonstats."') ON DUPLICATE KEY UPDATE stats = '".$addonstats."'";
	$res2=$con->query($sql);
    }
    $res->close();
}    
$con->close();

$time_taken = microtime(true) - $time_start;
$resstring = date("Y-m-d H:i")." - saving stats took ". $time_taken . " \n";
file_put_contents(dirname(__FILE__)."/cronstatslog.txt", $resstring, FILE_APPEND | LOCK_EX);

?>
