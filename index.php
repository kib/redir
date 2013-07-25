<?php
$filename="";
if (isset($_GET['file']) && !empty($_GET['file'])){
    $filename=$_GET['file'];
    }
echo '<iframe src="http://superrepo.brantje.com/' . $filename . '" frameborder="0" marginheight="0" marginwidth="0" width="100%" height="100%" scrolling="auto"></iframe>';
?>

