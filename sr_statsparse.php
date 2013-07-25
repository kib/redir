<?
include("functions.php");

$addon="";
if (isset($_GET['addonId']) && !empty($_GET['addonId'])){
    $con= db_connect($server,$user,$password,$database);
    $addon = $con->real_escape_string($_GET['addonId']);
    $con->close();
}

if ($addon==""){
   echo "error, no addon specified.";
   exit(1);
}

if ($xml = @simplexml_load_file('http://www.mirrorservice.org/sites/addons.superrepo.org/Frodo/.metadata/'.$addon.'.xml')){
    $last_id=$xml->extension[count($xml->extension) - 1];
    //name
	$name=$xml->attributes()->name;
    $nodes=$last_id->xpath('//description[@lang="en"]'); 
    //description
	$description = (isset($nodes[0]) ? $nodes[0] : "");
    $nodes=$last_id->xpath('//summary[@lang="en"]'); 
	if ($description == ""){ 
  	$description=$last_id->description[0];
	}
    //summary 
	$summary = (isset($nodes[0]) ? $nodes[0] : "");
	if ($summary == ""){ 
	$summary=$last_id->summary[0];
	}
    //disclaimer
	$nodes=$last_id->xpath('//disclaimer[@lang="en"]'); 
    $disclaimer = (isset($nodes[0]) ? $nodes[0] : "");
    if ($disclaimer == ""){ 
	$disclaimer=$last_id->disclaimer[0]; 
	}
	//version
	$version = $xml->attributes()->version;
	//provider
	$provider = $xml->attributes()->{'provider-name'};
	}
else {
	$name = "";
	$description="";
	$summary="";
	$version="";
	$provider="";
}
	
echo
'<div style="width:100%;" >
<table>
<h2>'.$name.'</h2>
<tr><th>Permalink</th><td><a href="http://addons.superrepo.org/Frodo/All/'.$addon.'/">Link to publish</a></td></tr>
<tr><th>Id</th><td>'.$addon.'</td></tr>
<tr><th>Version</th><td>'.$version.'</td></tr>
<tr><th>Provider</th><td>'.$provider.'</td></tr>
<tr><th>Page</th><td><a href="http://www.superrepo.org/'.$addon.'/">'.$name.'</a></td></tr>
</table>
</div>
<table>
<tr><th>Last 24 hours</th><td>'.get_last_24().'</td></tr>
<tr><th>Yesterday</th><td>'.get_yesterday().'</td></tr>
<tr><th>This week</th><td>'.get_this_week().'</td></tr>
<tr><th>Last week</th><td>'.get_last_week().'</td></tr>
<tr><th>All times</th><td>'.get_all_times().'</td></tr>
</table>
<div style="clear:both"></div>';

?>
