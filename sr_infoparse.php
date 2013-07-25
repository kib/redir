
<?php

include('officialaddons.php');
include("connect.php");
$con=new mysqli($server,$user,$password,$database);

//init defaults
$noget=0;
$today = strtotime(date("Y-m-d")); 

//addon
$addon="";
if (isset($_GET['addon']) && !empty($_GET['addon'])){
    $addon = $con->real_escape_string($_GET['addon']);
    }
	
if ($addon==""){
    echo "error";
    exit(1);
}

if ($addon==""){ $addon=basename($_SERVER['REQUEST_URI'], '?'.$_SERVER['QUERY_STRING']); $noget=1;}

$addonmetadata = 'http://www.mirrorservice.org/sites/addons.superrepo.org/Frodo/.metadata/'.$addon;

//exparse
$exparse=0;
 if (isset($_GET['exparse']) && !empty($_GET['exparse'])){
    $exparse = $con->real_escape_string($_GET['exparse']);
    }

//debug
$debug=0;
if (isset($_GET['debug']) && !empty($_GET['debug'])){
	$debug=$con->real_escape_string($_GET['debug']);
	}

//lets define the different type of blocks
$codeblock_XRG=
'<div style="width:100%; sloat:left;" class="emc2-alert-box warning normal visible top wp-bar ">
	<div class="emc2-alert-wrap">
		<h3 style="text-shadow:0px 2px 2px #000">XRG Addon: This addon is in its original form not compatible with XBMC Frodo</h3>
		<p style="font-weight:700; text-shadow:0px 1px 1px #000"">Some attributes have been overridden to make it installable in XBMC Frodo. 
		THIS PATCHED ADDON IS NOT SUPPORTED BY ITS MAINTAINER! Please report on the forum if the addon does not work (anymore)</p>
	</div>
</div>';

$codeblock_official=
'<div style="width:100%; sloat:left;" class="emc2-alert-box warning normal visible top wp-bar ">
	<div class="emc2-alert-wrap">
		<h3 style="text-shadow:0px 2px 2px #000">Offial XBMC Addon</h3>
		<p style="font-weight:700; text-shadow:0px 1px 1px #000"">This addon is included in the official XBMC repository. Therefore your download will be redirected to the official mirrors of XBMC</p>
	</div>
</div>';

function startswith($haystack, $needle){ 
    return strpos($haystack, $needle) === 0;
}

function BBCode ($string) {
    $search = array(
        '@\[(?i)b\](.*?)\[/(?i)b\]@si',
        '@\[(?i)i\](.*?)\[/(?i)i\]@si',
        '@\[(?i)u\](.*?)\[/(?i)u\]@si',
        '@\[(?i)img\](.*?)\[/(?i)img\]@si',
        '@\[(?i)url=(.*?)\](.*?)\[/(?i)url\]@si',
        '@\[(?i)code\](.*?)\[/(?i)code\]@si',
        '@\[(?i)cr\]@',
        '@\[(?i)color[^\]]*\](.*?)\[/(?i)color\]@',
    );
    $replace = array(
        '<b>\\1</b>',
        '<i>\\1</i>',
        '<u>\\1</u>',
        '<img src="\\1">',
        '<a href="\\1">\\2</a>',
        '<code>\\1</code>',
        '<br />',
        '\\1'
   );
   return preg_replace($search , $replace, $string);
}

$sql = "SELECT sum(stats) FROM download WHERE addonid='".$addon."'";
$res = $con->query($sql);
$row = $res->fetch_row();
$downloads=round($row[0]);
$res->close();

if ($debug=='1'){
    echo "-> DEBUGGING <br />";
    echo "-> addon -> ".$addon."<br />";
    echo "-> downloads -> ".$downloads."<br />";
    }

if (in_array($addon, $official)) {
	$official_addon=true; 
	$prelink="http://mirrors.xbmc.org/addons/frodo";
	} else {
	$official_addon=false; 
	$prelink="http://addons.superrepo.org/Frodo/All";
	}

if ($xml = @simplexml_load_file($addonmetadata.'.xml')){
    $last_id=$xml->extension[count($xml->extension) - 1];
    $name=$xml->attributes()->name;
    $nodes=$last_id->xpath('//description[@lang="en"]'); 
    $description = (isset($nodes[0]) ? $nodes[0] : "");
    $nodes=$last_id->xpath('//summary[@lang="en"]'); 
    $summary = (isset($nodes[0]) ? $nodes[0] : "");
    $nodes=$last_id->xpath('//disclaimer[@lang="en"]'); 
    $disclaimer = (isset($nodes[0]) ? $nodes[0] : "");
    if ($description == ""){ 
  	$description=$last_id->description[0];
	}
    if ($summary == ""){ 
	$summary=$last_id->summary[0];
	}
    if ($disclaimer == ""){ 
	$disclaimer=$last_id->disclaimer[0]; 
	}
    $description=BBCode($description);
    
    if ($noget==1 OR $exparse==1){  
	if (fopen($addonmetadata.".jpg", "r")){ 
		echo '<div class="entry-thumbnail"><img width="623" height="200" alt="Statistics" src="'.$addonmetadata.'.jpg"></div>'; 
		}
	}
	
	echo 
	'<div style="width:100%;" >
	  <img style="float:right; margin-bottom:20px; margin-left:15px;  box-shadow:0px 2px 5px #000; width:40%;" src="'.$addonmetadata.'.png" alt=""/>
	  <h2>'.$name.'<br />'.$summary.'</h2>
	  <p>'.$description.'</p>
	</div>';

	if ($noget==1 OR $exparse==1 ){
		echo
		'<div style="clear:both"></div>
		<div id="addon-div-right" style="float:left; width:100%" >
		<center></center>
		<table style="max-width:100%!important; overflow:hidden;
		white-space:nowrap;';
	}

	//als je h:et niet gebruikt, dan comment ik het uit
	/*
	if (startswith($addon, 'repository.superrepo.org.frodo')) {
		  $id = (strlen($id) > 30) ? substr($id,0,31).'<br />'.substr($id,31,100) : $id;
	}else{
		  $id = (strlen($id) > 50) ? substr($id,0,47).'...<br />'.substr($id,47,100) : $id; 
	}
	*/

	echo
	'<table><tr><th>Install and auto update</th><td><a href="http://www.superrepo.org/get-started">Download XBMC addon '.$name.'</a></td></tr>
	<tr><th>Id</th><td>'.$xml->attributes()->id.'</td></tr>
	<tr><th>Version</th><td>'.$xml->attributes()->version.'</td></tr>
	<tr><th>Provider</th><td>'.$xml->attributes()->{'provider-name'}.'</td></tr>
	<tr><th>Downloads</th><td>'.$downloads.'</td></tr>
	<tr><th>Categories</th><td>'; 

	if($noget==1){
		the_tags( '', ', ', ''); 
	}

	echo
	'</td></tr>
	<tr><th>Link</th><td><a href="'.$prelink.'/'.$addon.'/">Permanent download link</a></td></tr>
	</table>
	</div>';

	/* <tr><th>Categories</th><td><?php the_tags( '<p class="taggy">', ', ', '</p>'); ?></td></tr> */

	if ($changelog = @file_get_contents ($addonmetadata.'.txt')){
		$changelog=nl2br(BBCode($changelog)); 
		$changelog=BBcode($changelog);
		echo '<div style="clear:both"></div><div style="margin-top:20px; margin-bottom:40px; border: 1px solid #EEEEEE; padding: 9px 24px; font-size:16px; overflow-y: auto; max-height:200px;"><div style="font-weight:700; margin-bottom:10px;">Changelog</div>'.$changelog.'</div>';
	}

	if ($disclaimer != ""){
		$disclaimer=BBcode($disclaimer);
		echo 
		'<script src="http://code.jquery.com/jquery-latest.js"></script><p style="margin-top:45px; width:100%; text-align:center;">
		<button>Disclaimer</button><br />
		<small class="disclaimer">'.$disclaimer.'</small>
		</p>
		
		<script>
		$("button").click(function () {
		$("small").slideToggle("slow");
		});
		</script>
		'; 
	}
} else {
    if (strpos($addon,'XRG') === true) {
	echo $codeblock_XRG;
	}
    if ($official_addon==true) { 
	echo $codeblock_official;
	}
}
 
echo '<div style="clear:both"></div>';
$con->close();
?>

