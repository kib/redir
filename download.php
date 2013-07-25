<?php

$time_start = microtime(true);

$debug=false; //verbose output
$test=false;  //always true on dev system!

if($debug) {ini_set('error_reporting', E_ALL);}

include("connect.php");	
$link = mysql_connect ($server, $user, $password);
mysql_select_db($database, $link);

// enable explicit flush to prevent hickups
//@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
ob_implicit_flush(1);

//initial vars
$run=1;
$succes=false;
$failedmirrors=0;
$ipArr    = explode('.',$_SERVER['REMOTE_ADDR']);
$ip       = $ipArr[0] * 0x1000000 + $ipArr[1] * 0x10000 + $ipArr[2] * 0x100 + $ipArr[3];
          
$agent=strtolower($_SERVER['HTTP_USER_AGENT']);
ini_set ('user_agent', $_SERVER['HTTP_USER_AGENT']); 
$today = strtotime(date("Y-m-d")); 

if(isset($_GET['file']) && strpos($_GET['file'], ".") !== false){$reqfile = mysql_real_escape_string($_GET['file']);}else{$reqfile='bad.boy';}

//split requested filepath
$path_parts = pathinfo("$reqfile");
$parentpath = $path_parts['dirname'];
$addonid = basename($parentpath);
$filename = $path_parts['basename'];
$filetype = $path_parts['extension'];

if ($filetype != 'zip') die("Invalid download type.");

// official addons array
$official = array('webinterface.awxi', 'plugin.video.orftvthek', 'plugin.video.nolife', 'skin.quartz', 'plugin.video.synopsi', 'service.xbmc.versioncheck', 'metadata.filmaffinity.com', 'metadata.album.universal', 'script.cu.lrclyrics', 'plugin.video.mediathek', 'plugin.video.mlslive', 'plugin.video.couchpotato_manager', 'plugin.video.vine', 'plugin.video.nrk', 'plugin.video.irishtv', 'plugin.video.4od', 'plugin.video.metacafe', 'plugin.video.4players', 'script.videolanguage', 'skin.back-row', 'skin.ace', 'plugin.video.youtube', 'plugin.video.bliptv', 'plugin.video.tv3play.dk', 'plugin.video.drnu', 'plugin.video.dr.dk.live', 'plugin.video.glwiz', 'script.tvguide', 'script.screensaver.bigpictures', 'plugin.program.rtorrent', 'plugin.video.tv2regionerne.dk', 'plugin.video.flw.outdoors', 'plugin.video.dmi.dk', 'plugin.audio.abradio.cz', 'plugin.video.tested', 'plugin.video.screened', 'plugin.video.photocasts', 'plugin.video.comicvine', 'plugin.video.animevice', 'plugin.audio.relive', 'plugin.audio.modland', '
plugin.video.tv2.dk', 'plugin.video.giantbomb', 'plugin.video.gametest.dk', 'plugin.video.gamereactor.dk', 'plugin.video.gaffa.tv', 'plugin.video.radbox', 'plugin.image.flickr', 'plugin.video.news.tv2.dk', 'plugin.image.picasa', 'plugin.image.google', 'plugin.audio.mozart', 'plugin.video.zapiks', 'plugin.video.mmafighting', 'plugin.video.joeroganexperience', 'plugin.video.hgtv', 'plugin.video.fightcasts', 'plugin.video.spiegelonline', 'plugin.image.500px', 'plugin.audio.npr', 'plugin.video.national.geographic', 'plugin.video.deathsquad', 'plugin.audio.sverigesradio', 'plugin.video.hwclips', 'plugin.video.hockeystreams', 'plugin.audio.soundcloud', 'plugin.audio.radioma', 'plugin.video.dtm_tv', 'plugin.video.bild_de', 'plugin.video.aljazeera', 'plugin.audio.podcatcher', 'plugin.program.jdownloader', 'plugin.program.akinator_com', 'plugin.video.stofa.dk', 'plugin.video.nos', 'plugin.video.liveleak', 'plugin.video.gronkh_de', 'plugin.video.time_com', 'plugin.video.thisweekin', 'plugin.video.redbull_tv', 'plugin.
video.freshmilk_tv', 'plugin.audio.einslive_de', 'plugin.audio.dradio', 'plugin.video.roosterteeth', 'plugin.video.pyvideo', 'plugin.video.giga_de', 'plugin.video.chip_de', 'plugin.audio.hvsc', 'plugin.audio.dr.dk.netradio', 'plugin.video.thenewboston', 'plugin.video.tas', 'plugin.video.mpora_com', 'plugin.video.howstuffworks_com', 'plugin.video.engadget', 'plugin.video.vimcasts', 'plugin.video.hollywoodreporter', 'plugin.video.gamestar', 'plugin.video.documentary.net', 'plugin.video.diy', 'plugin.video.animeftw', 'plugin.audio.mp3search', 'plugin.video.retrowaretv', 'plugin.video.pakee', 'plugin.video.myvideo_de', 'plugin.program.mceremote', 'plugin.audio.icecast', 'plugin.video.offene_kanaele', 'plugin.video.nz.ondemand', 'plugin.video.lachschon_de', 'plugin.video.visir', 'plugin.video.twitch', 'plugin.video.thatguywiththeglasses_com', 'plugin.video.tageswebschau', 'plugin.video.neterratv', 'plugin.video.myzen_tv', 'plugin.video.eevblog', 'plugin.video.borsentv.dk', 'plugin.video.wired_com', 'plugin.video.
sarpur', 'plugin.video.redux_com', 'plugin.video.railscasts', 'plugin.video.goldpagemedia', 'plugin.video.dr.dk.bonanza', 'plugin.video.tvvn', 'plugin.video.servustv', 'plugin.video.screwattack', 'plugin.video.kidsplace', 'plugin.video.dmax', 'plugin.video.circuitboard', 'plugin.video.netzkino_de', 'plugin.video.cnet.podcasts', 'plugin.video.techcrunch', 'plugin.video.previewnetworks', 'plugin.program.utorrent', 'plugin.video.tagesschau', 'plugin.program.isybrowse', 'plugin.video.tvkaista', 'plugin.video.on_aol', 'plugin.audio.sky.fm', 'plugin.audio.jazzradio.com', 'plugin.audio.di.fm', 'plugin.video.jupiterbroadcasting', 'plugin.video.southpark_de', 'plugin.video.foodnetwork', 'plugin.video.earth.touch', 'plugin.video.drdish-tv_de', 'plugin.video.dokumonster', 'plugin.video.s04tv', 'plugin.video.medi1tv', 'plugin.video.funny.or.die', 'plugin.video.wdrrockpalast', 'plugin.video.vimeo', 'plugin.video.revision3', 'plugin.video.newyorktimes', 'plugin.video.classiccinema', 'plugin.video.sagetv', 'plugin.video.
nederland24', 'plugin.video.nba.video', 'plugin.video.dr.dk.podcast', 'plugin.video.tmz', 'plugin.video.redditmusic', 'plugin.video.manoto', 'plugin.video.filmarkivet', 'plugin.video.arretsurimages', 'plugin.video.ted.talks', 'plugin.video.g4tv', 'plugin.video.academicearth', 'plugin.video.the.trailers', 'plugin.video.khanacademy', 'plugin.video.cinemassacre', 'plugin.video.yousee.tv', 'plugin.video.videovideo.dk', 'plugin.video.m6groupe', 'plugin.video.espn.video', 'plugin.video.zdf_de_lite', 'plugin.video.sueddeutsche_de', 'plugin.video.n24_de', 'plugin.video.eredivisie-live', 'plugin.video.ebaumsworld_com', 'plugin.video.eyetv.parser', 'plugin.video.chefkoch_de', 'plugin.video.bestofyoutube_com', 'plugin.audio.vorleser_net', 'plugin.video.ign_com', 'plugin.video.filmstarts_de', 'plugin.video.attactv', 'plugin.audio.booksshouldbefree_com', 'plugin.video.trailer.addict', 'plugin.audio.qobuz', 'plugin.audio.internet.archive', 'plugin.video.theonion_com', 'plugin.video.rbk.no', 'plugin.video.elisa.viihde', '
plugin.audio.mixcloud', 'plugin.audio.listenliveeu', 'plugin.video.vgtv', 'plugin.video.mtv_de', 'plugin.video.atv_at', 'plugin.video.ardmediathek_de', 'plugin.video.fernsehkritik_tv', 'plugin.video.disclose_tv', 'plugin.video.arte_tv', 'plugin.video.fox.news', 'plugin.video.euronews_com', 'plugin.video.cbsnews_com', 'plugin.video.bilmagasinettv.dk', 'plugin.video.tv5monde', 'plugin.video.topdocumentaryfilms_com', 'plugin.video.pbs', 'plugin.video.onside.tv', 'plugin.video.nascar', 'plugin.video.mlbmc', 'plugin.video.leafstv', 'plugin.video.itunes_podcasts', 'plugin.video.hdtrailers_net', 'plugin.audio.shoutcast', 'plugin.video.wimp', 'plugin.video.rofl_to', 'plugin.video.pennyarcadetv', 'plugin.image.cheezburger_network', 'plugin.video.sevenload_de', 'plugin.video.nasa', 'plugin.video.myspass_de', 'plugin.video.gfq', 'plugin.video.dailymotion_com', 'plugin.video.break_com', 'plugin.video.collegehumor', 'plugin.audio.radio_de', 'plugin.video.videobash_com', 'plugin.video.day9', 'plugin.image.iphoto', 'plugin.
video.thegeekgroup', 'plugin.video.pinkbike', 'plugin.video.dump', 'plugin.image.xzen', 'plugin.video.twit', 'plugin.video.itbn_org', 'plugin.audio.groove', 'plugin.video.svtplay', 'plugin.video.youtube.channels', 'plugin.video.vidstatsx_com', 'plugin.image.mypicsdb', 'plugin.audio.lastfm', 'script.tvtunes', 'script.playlists', 'script.favourites', 'script.artworkorganizer', 'script.tv.show.next.aired', 'screensaver.xbmc.slideshow', 'skin.transparency', 'script.categories', 'script.trakt', 'skin.xeebo', 'script.image.bigpictures', 'metadata.douban.com', 'metadata.common.port.hu', 'service.watchdog', 'service.skin.widgets', 'script.randomandlastitems', 'weather.ozweather', 'script.ace.extrapack', 'skin.re-touched', 'script.game.whatthemovie', 'metadata.common.ofdb.de', 'script.xbmc.lcdproc', 'skin.xtv-saf', 'metadata.tvdb.com', 'script.module.bigpictures', 'script.game.netwalk', 'metadata.common.themoviedb.org', 'script.cdartmanager', 'service.scrobbler.librefm', 'skin.xperience-more', 'script.speedfaninfo', '
skin.pm3-hd', 'script.xbmc.subtitles', 'metadata.thexem.de', 'metadata.artists.universal', 'script.module.requests', 'metadata.universal', 'metadata.port.hu', 'script.module.xbmcswift2', 'script.module.validictory', 'skin.diffuse', 'skin.hybrid', 'metadata.common.imdb.com', 'skin.carmichael', 'script.xbmc.checkpreviousepisode', 'metadata.movieplayer.it', 'script.module.socksipy', 'script.artwork.downloader', 'metadata.filmweb.pl', 'metadata.common.rt.com', 'metadata.common.allmusic.com', 'metadata.common.theaudiodb.com', 'metadata.common.last.fm', 'metadata.common.musicbrainz.org', 'service.libraryautoupdate', 'script.linphone', 'script.module.urlresolver', 'metadata.musicvideos.theaudiodb.com', 'webinterface.home-row', 'script.lrclyrics', 'metadata.common.fanart.tv', 'script.module.metahandler', 'skin.metropolis', 'metadata.sratim.co.il', 'weather.wunderground', 'script.sharethetv', 'script.image.lastfm.slideshow', 'script.games.rom.collection.browser', 'script.xbmc.unpausejumpback', 'script.xbmc.boblight', 
'screensaver.qlock', 'skin.aeon.nox', 'metadata.common.trakt.tv', 'pvr', 'script.xbmc.audio.mixer', 'metadata.mtime.com', 'script.xbmc.debug.log', 'script.common.plugin.cache', 'script.module.simple.downloader', 'script.module.parsedom', 'script.cu.lyrics', 'script.xbmcbackup', 'metadata.artists.theaudiodb.com', 'metadata.albums.theaudiodb.com', 'metadata.themoviedb.org', 'script.artistslideshow', 'metadata.filmdelta.se', 'visualization.itunes', 'screensaver.xbmc.builtin.slideshow', 'script.globalsearch', 'script.playalbum', 'metadata.videobuster.de', 'script.module.gmusicapi', 'script.watchlist', 'script.randomitems', 'service.script.isyevents', 'metadata.cinemarx.ro', 'metadata.cinemagia.ro', 'metadata.worldart.ru', 'metadata.tvshows.animenewsnetwork.com', 'metadata.tv.movieplayer.it', 'metadata.tv.daum.net', 'metadata.serialzone.cz', 'metadata.ptgate.pt', 'metadata.mymovies.it', 'metadata.mymovies.dk', 'metadata.moviemeter.nl', 'metadata.moviemaze.de', 'metadata.movie.animenewsnetwork.com', 'metadata.
m1905.com', 'metadata.kinopoisk.ru', 'metadata.he.israel-music.co.il', 'metadata.filmbasen.dagbladet.no', 'metadata.disneyinfo.nl', 'metadata.common.youtubetrailers', 'metadata.common.rottentomatoes.com', 'metadata.common.movieposterdb.com', 'metadata.common.movie.daum.net', 'metadata.common.impa.com', 'metadata.common.htbackdrops.com', 'metadata.common.hdtrailers.net', 'metadata.common.amazon.de', 'metadata.cine.passion-xbmc.org', 'metadata.bestanime.co.kr', 'metadata.asiandb.com', 'metadata.artists.top100.cn', 'metadata.artists.metal-archives.com', 'metadata.artists.freebase.com', 'metadata.artists.1ting.com', 'metadata.anidb.net', 'metadata.albums.top100.cn', 'metadata.albums.metal-archives.com', 'metadata.albums.merlin.pl', 'metadata.albums.freebase.com', 'metadata.albums.1ting.com', 'metadata.7176.com', 'service.rom.collection.browser', 'service.qlock', 'service.mpris.soundmenu', 'service.dbus.notify', 'weather.worldweatheronline', 'script.xbmc-pbx-addon', 'script.web.viewer', 'script.transmission', '
script.simpleplaylists', 'script.rss.editor', 'script.njoy', 'script.mythbox', 'script.mpdc', 'script.moviequiz', 'script.module.xmltodict', 'script.module.xbmcswift', 'script.module.t0mm0.common', 'script.module.simplejson', 'script.module.pyamf', 'script.module.protobuf', 'script.module.playbackengine', 'script.module.myconnpy', 'script.module.mutagen', 'script.module.mechanize', 'script.module.feedparser', 'script.module.elementtree', 'script.module.dialogaddonscan', 'script.module.decorator', 'script.module.chardet', 'script.module.buggalo', 'script.module.brightcove', 'script.module.beautifulsoup', 'script.linux.nm', 'script.ibelight', 'script.gomiso', 'script.gmail.checker', 'script.forum.browser', 'script.facebook.media', 'script.commands', 'script.advanced.wol');

$mirrors = array(
    'http://www.mirrorservice.org/sites/addons.superrepo.org' => 20,
    'http://ftp.snt.utwente.nl/pub/software/superrepo' => 10,
    'http://mirrors.xmission.com/superrepo' => 10,
    'http://ftp.bit.nl/mirror/superrepo' => 10,
    'http://ftp.acc.umu.se/mirror/addons.superrepo.org' => 4,
    'http://superrepo.brantje.com' =>1
);
$mirrortotal=count($mirrors);


// create a mirror mix using their weight
function createMix($mirrors){
    foreach($mirrors AS $mirror => $weight) {   
	for ($i = 1; $i <= $weight; $i++) {
	  $mirrormix[]="$mirror";
	}	
    }
    return $mirrormix;
} 

function urlPing($pingurl){  
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $pingurl);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    if (isset($_SERVER['HTTP_REFERER'])) {   
	curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_REFERER']);
	}
 
    //curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, true);
    // Only calling the head
    curl_setopt($ch, CURLOPT_HEADER, true); // header will be at output
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, 156, 2000);

    $curlresult = curl_exec ($ch);
    curl_close ($ch);

    if (preg_match("/OK/i", $curlresult)) {return true;}else{ return false;}
}

// download from official mirror if the addon is included in the official repo
if (in_array("$addonid", $official)) {
    $dl="http://mirrors.xbmc.org/addons/frodo/$addonid/$filename";
    $succes=true;  //better would be if we can ping for existance first (just like we do with our own mirrors) before we set the green flag
}

// try mirrors
if ($succes!=true) { 

 $mirrormix=createMix($mirrors);
    
    // loop until we found a mirror with the file on it, but not more than [$mirrortotal] times
    while ($succes != true && $mirrortotal > 0 && $run < 10){
	
	$chosenmirror=$mirrormix[array_rand($mirrormix)];
	$pingurl=$chosenmirror.$reqfile;
	
	if($debug) {
		echo 'Run:' . $run . '<br />Ping:' . $pingurl . '<br /><pre>'; 
		print_r ($mirrormix); 
		echo '</pre><br />'; 
		flush();
		}
	 
	if (urlPing($pingurl) == true) {
	    $dl="$pingurl";
	    $succes=true;	    
	    }   
	else {
	    // could not find the file at that download location. Remove the mirror from the list and mix again    
	    unset($mirrors["$chosenmirror"]);
	    $mirrortotal=$mirrortotal-1;
	    if ($mirrortotal > 0) { $mirrormix=createMix($mirrors);} else {break;}
	    $run++;
	    continue;
	}	 
    }		    
}

// if still no success, fallback on the first synced server as the file might not have been mirrored yet (no use if fallback is in mirrorlist too)
if ($succes!=true) { 
    $pingurl='http://superrepo.brantje.com'.$reqfile;
  	
if (urlPing($pingurl) == true) {
	  $succes=true;
	  $dl="$pingurl";}
}

// we just can't find that package. Don't kill the messenger!
if ($succes!=true) { 
    if($debug!=true){ header("HTTP/1.0 404 Not Found"); }
    echo ("<div style=' margin-top:150px; padding:20px; box-shadow:0px 5px 500px 20px #077AE7; 
			text-align:center;'>
		<h1>We are sad too :'(</h1><br /><br />$filename could not be found. Please wait 5 minutes and <a href='http://addons.superrepo.org/$reqfile'>try again</a>. <br /><br /><br />Contact the webmaster at webmaster@[our domain] if the problem persists.
	   </div>");}

// we have the package. Hug the messenger!
else{
    $time_end = microtime(true);
   $time = $time_end - $time_start;

    if($debug) {echo "<br />Location chosen: $dl<br />Time: $time<br />";}
    if($test) {exit;}

    // redirect client to the download location
    header('Location: '.$dl);

    //download counters 
    $result = mysql_query("INSERT INTO addonstats (addonname, counter) VALUES ('".$addonid."',1) ON DUPLICATE KEY UPDATE counter=counter+1");
    $result2 = mysql_query("INSERT IGNORE INTO userstats (userip, firstseen) VALUES ('".$ip."','".$time_start."')");
    exit (0);
}

exit (1);
?>
