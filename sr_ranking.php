<?PHP

include("connect.php");
$con = new mysqli($server, $user, $password, $database);

if ($con->connect_error) {
    die('Connect Error (' . $con->connect_errno . ') '
            . $con->connect_error);
}

$today = strtotime(date("Y-m-d")); 

function time_query($startTime,$endTime){
	$sql="SELECT SUM(stats) FROM download WHERE dldate BETWEEN '".$startTime."' and '".$endTime."'";
	$result = $con->query($sql);
	$row = $result->fetch_row();
	$sum = number_format((int) $row[0], 0, ',', '.');
	echo "$sum";
	$result->close();
}

function get_last_24(){
	$startTime = time() - 86400; // number of seconds in a day
	$endTime = time();  
	time_query($startTime,$endTime);
}
 
function get_yesterday(){
$startTime = mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));     
$endTime = mktime(23, 59, 59, date('m'), date('d')-1, date('Y'));     
time_query($startTime,$endTime);
}

function get_this_week(){
$startTime = time() - 604800; // number of seconds in a week   
$endTime = time();   
time_query($startTime,$endTime);
}

function get_last_week(){
$startTime = mktime(0, 0, 0, date('n'), date('j')-6, date('Y')) - ((date('N'))*86400);     
$endTime = mktime(23, 59, 59, date('n'), date('j'), date('Y')) - ((date('N'))*86400);   
time_query($startTime,$endTime);
}



function get_last_month(){
$startTime = time() - 2592000; // number of seconds in 30 days      
$endTime = time();
time_query($startTime,$endTime);
}

function get_all_times(){
$startTime = 0;     
$endTime = time();
time_query($startTime,$endTime);
}

echo "<p>This is the top 100 downloaded XBMC addons using SuperRepo. To browse the addons use the search function or the categories above. For the full list of what SuperRepo offers (incl. versionnumbers) have a look at the <a href='http://addons.superrepo.org/Frodo/output_index.html'>index</a>.</p>

<table border='1' align='center' style='width:100%'>";

echo "<tr> <th> Ranking </th> <th> Addon </th> <th style='text-align:center'> Downl. </th></tr>";

$sql="SELECT addonid, sum(stats) AS 'total_dl' FROM download GROUP BY addonid ORDER BY total_dl DESC";
if($result = $con->query($sql)){
    //init loop
    $counter =0;
    while($counter!=100) {
   	$row=$result->fetch_row();
    	$downloads = number_format($row[1], 0, ',', '.');
	$addon = (strlen($row[0]) > 43) ? substr($row[0],0,40).'...' : $row[0];
	if (strpos($row[0], 'script.module.') === 0) {
        //do nothing
	}else{
    	echo "<tr><td>".($counter+1)."</td><td><a href='http://superrepo.org/addon/".$row[0]."/'>".$addon."</a></td> <td style='text-align:center'> ".$downloads." </td></tr>";
    	$counter++;
    	}
    }
    $result->close();
}

$sql="SELECT COUNT(DISTINCT iphash) FROM user";
if($result=$con->query($sql)){
    $row = $result->fetch_row();
    $users = number_format($row[0], 0, ',', '.');
    echo "<a name='stats'></a></table>";
    $result->close();
}

$con->close();
?>
