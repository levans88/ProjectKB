<?php

session_start();

require("../includes/config.php");

$source_links = "";
$download_links = array();

// If "clear" is present in $_POST, put it in $_SESSION and redirect.
if (postHas("clear")) {
	session_unset();
	session_destroy();
	
	header ('HTTP/1.1 303 See Other');
	header ('Location: ./youtube.php');
}

// If there is a source_link in $_POST, put it in $_SESSION and redirect.
if (postHas("source_links")) {
	giveSession("source_links", postHas("source_links"));
	
	header ('HTTP/1.1 303 See Other');
	header ('Location: ./youtube.php');
}

// If there is a source_link in $_SESSION, let's process it and get some links.
if (sessionHas("source_links")) {
	$source_links = sessionHas("source_links");

	$youtube = new Youtube();
	//***** DEAL WITH MULTIPLE SOURCE LINKS HERE *****
	$urlid = stristr($source_links, 'v=', FALSE);
	$urlid = substr($urlid, 2);
	
	$download_links = $youtube->getDownloadLinks($source_links);
}
?>

<!DOCTYPE html>
	<html>

<?php
// Show $_POST and $_SESSION arrays if debug is enabled.
if (DEBUG === 1) {
	echo '<pre>';
	echo "POST" . "<br>";
	print_r($_POST);
	echo "<br>";
	echo "SESSION" . "<br>";
	print_r($_SESSION);
	echo "<br>";
	echo '</pre>';
}

// Show the array of download links if debug is enabled.
if (DEBUG === 1) {
	echo "<pre>";
	if (isset($download_links)) {
		echo "DOWNLOAD_LINKS" . "<br>";
		print_r($download_links);
	}
	echo "</pre>";
}
?>

	<head>
		<title>YouTube Video Download</title>
		<link href='http://fonts.googleapis.com/css?family=Droid+Sans|Just+Another+Hand|Sue+Ellen+Francisco|Give+You+Glory|Neucha|Shadows+Into+Light|Gloria+Hallelujah|Just+Me+Again+Down+Here|Indie+Flower|Yanone+Kaffeesatz|Nothing+You+Could+Do|Rancho|Englebert|Covered+By+Your+Grace' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
		<link rel="stylesheet" type="text/css" href="css/youtube.css" />
		<link rel="stylesheet" type="text/css" href="css/font-awesome.css" />
		<link rel="stylesheet" type="text/css" href="css/fontello.css" />
		<script type="text/javascript" src="js/scripts.js"></script>
		<link rel="shortcut icon" href="img/database-16.ico">
	</head>
	<body>
		<form id="links_input" action="" method="POST">
			Paste YouTube video URL below &mdash; <br>
			<i>(example format: https://www.youtube.com/watch?v=ss--_CEGozY)</i><br>
			<br>
			<textarea rows="4" cols='50' name='source_links'></textarea><br><br>
			<input type='submit' value='Get Links' class='button'><br>
		</form>
		<form id="clear_form" action="" method="POST">
			<input id='clear_button' type='submit' value='Clear' class='button'><br>
			<input type='hidden' name='clear' value='TRUE'>
		</form>
		<br>
		<br>
<?php

// Output links for downloading the videos.
if (isset($download_links)) {
	foreach($download_links as $video) {
		foreach($video as $quality => $link) {
			//echo "<a href='" . $download_links['MP4']['High Quality - 1280x720'] . "' target='_blank' download>Download High Quality</a>";
			echo "<a href='" . $link . "' target='_blank' download>Download " . $quality . "</a>" . "<br>";
		}
	}
}

if (isset($urlid)) {
	echo "<br><br>" . "<img src='http://img.youtube.com/vi/" . $urlid . "/mqdefault.jpg'>";
}

class Youtube{

	// The video map for the results. New types are added by YouTube periodically.
	// Here are two lists of extra types that could be added later:
	//
	// https://github.com/rg3/youtube-dl/issues/1687
	// http://www.genyoutube.net/formats-resolution-youtube-videos.html
	//
	private $videoMap = array(
		"0" => array("Unknown Type", "Unknown Quality - ? x ?"),
		"13" => array("3GP", "3GP - Low Quality - 176x144"),
		"17" => array("3GP", "3GP - Medium Quality - 176x144"),
		"36" => array("3GP", "3GP - High Quality - 320x240"),
		"5" => array("FLV", "FLV - Low Quality - 400x226"),
		"6" => array("FLV", "FLV - Medium Quality - 640x360"),
		"34" => array("FLV", "FLV - Medium Quality - 640x360"),
		"35" => array("FLV", "FLV - High Quality - 854x480"),
		"43" => array("WEBM", "WEBM - Low Quality - 640x360"),
		"44" => array("WEBM", "WEBM - Medium Quality - 854x480"),
		"45" => array("WEBM", "WEBM - High Quality - 1280x720"),
		"18" => array("MP4", "MP4 - Medium Quality - 480x360"),
		"22" => array("MP4", "MP4 - High Quality - 1280x720"),
		"37" => array("MP4", "MP4 - High Quality - 1920x1080"),
		"38" => array("MP4", "MP4 - High Quality - 4096x230"),
		"171"  => array("WEBM", "WEBM - Audio Only - 128 Kbps"),
		"172"  => array("WEBM", "WEBM - Audio Only - 256 Kbps"),
		"139"  => array("MP4", "MP4 - Audio Only - 48Kbps"),
		"140"  => array("MP4", "MP4 - Audio Only - 128Kbps"),
		"141"  => array("MP4", "MP4 - Audio Only - 256Kbps")
	);

	
	// Gets the entire page content as a string and passes 
	// it to findMatches() to search for download links.
	function getDownloadLinks($source_links){
		$content = file_get_contents($source_links);
		$download_links = $this->findMatches($content);
		return $download_links;
	}

	// Finds, builds, and returns download links. 
	function findMatches($content){

		$videos = array();
		$data = "";
		$cdata = "";
		$xdata = "";
		$sig = "";
		$url = "";
		$type = "";

		// Break page into pieces and run regex's to find and build download links.
		// Note: $results is an array.
		if(preg_match('/url_encoded_fmt_stream_map(.*?);/', $content, $results)){

			$data = "";

			// $results[0]=the text that matched the full pattern
			// $results[1]=the text matching the first subpattern
			// $results[2]=the text matching the second subpattern (n/a)
			$data = $results[1];

			// Literally replace the characters '\u0026' with an '&'.
			$data = str_replace('\u0026', '&', $data);
			
			// Convert $data to an array by splitting on ','.
			$data = explode(',', $data);

			foreach($data As $cdata){

				// Break each element in the $data array into an array.
				$cdata = explode('&', $cdata);

				// The $cdata array should have exactly 5 elements if it is an applicable URL.
				if (sizeof($cdata) === 5) {

					if (DEBUG === 1) {
						echo "<b>";
						echo "<br><br>";
						echo "CDATA" . "<br>";
						echo "<pre>";
						print_r($cdata);
						echo "</pre>";
						echo "</b>";
					}

					// For each element in the $cdata array...
					foreach($cdata As $xdata){
						
						// Sometimes this is found as the first three characters of an element:  ":"
						// Check for it by seeing if first character is '"'.
						// If so, remove those three characters.
						if ($xdata[0] === '"') {
							$xdata = substr($xdata, 3);
						}

						// Look for signature.
						if(preg_match('/^sig/', $xdata)){
							$sig = substr($xdata, 4);
						}

						// Look for URL.
						if(preg_match('/^url/', $xdata)){
							$url = substr($xdata, 4);
							$url = urldecode($url);
							
							if (DEBUG === 1) {
								echo "<b>" . "URL: " . $url . "<br>" . "</b>";
							}
						}

						// Look for video type.
						if(preg_match('/^itag/', $xdata)){
							$type = substr($xdata, 5);
							
							// If $type is not a known video type, change it to type '0' (unknown)
							if (!array_key_exists($type, $this->videoMap)) {
								$type = "0";										
							}
							if (DEBUG === 1) {
								echo "<b>" . "<br>" . "TYPE: " . $type . "<br>" . "</b>";
								echo "<br><br>";
								//echo "<hr>";
							}
						}
					}
					//$url = $url . '&signature=' . $sig;
					$videos[$this->videoMap[$type][0]][$this->videoMap[$type][1]] = $url;
				}
			}
		}
		return $videos;
	}
}
?>
	</body>
</html>