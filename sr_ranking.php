<?PHP
$today = strtotime(date("Y-m-d"));

include("functions.php");
$con = db_connect();


echo "<p>These are the top 100 XBMC addons downloaded using SuperRepo. To browse the addons use the search function or the categories above. For the full list of what SuperRepo offers (incl. versionnumbers) have a look at the <a href='http://addons.superrepo.org/Frodo/output_index.html'>index</a>.</p>
<table border='1' align='center' style='width:100%'>
<tr> <th> Ranking </th> <th> Addon </th> <th style='text-align:center'> Downl. </th></tr>";

$sql="SELECT addonid, sum(stats) AS 'total_dl' FROM download GROUP BY addonid ORDER BY total_dl DESC";
if($res = $con->query($sql)){
    //init loop
    $counter =0;
    while($counter!=100) {
   	$row=$res->fetch_row();
    	$downloads = number_format($row[1], 0, ',', '.');
	$addon = (strlen($row[0]) > 43) ? substr($row[0],0,40).'...' : $row[0];
	if (strpos($row[0], 'script.module.') === 0) {
        //do nothing
	}else{
    	echo "<tr><td>".($counter+1)."</td><td><a href='http://superrepo.org/addon/".$row[0]."/'>".$addon."</a></td> <td style='text-align:center'> ".$downloads." </td></tr>";
    	$counter++;
    	}
    }
    $res->close();
}

echo "</table>";

$con->close();
?>
