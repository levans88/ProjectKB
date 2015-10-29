<?php

session_start();

//require("../includes/config.php");

$debug = FALSE;
if ($debug === TRUE) {
	echo '<pre>';
	echo "POST";
	print_r($_POST);
	echo "<br>";
	echo "SESSION";
	print_r($_SESSION);
	echo "<br>";
	//echo "HEADERS_LIST";
	//print_r(headers_list());
	//echo "<br>";
	//echo "HEADERS_SENT";
	//print_r(headers_sent());
	echo '</pre>';
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>YouTube Video Downloader</title>
	</head>
	<body>
		<br>
		<form action="" method="POST">
			Paste YouTube video URL below &mdash; <br>
			<i>(example format: https://www.youtube.com/watch?v=ss--_CEGozY)</i><br>
			<br>
			<input type='text' size='50' name='source_link'><br><br>
			<input type='submit' value='Get Download Link'><br>
		</form>
		<br>
		<form action="" method="POST">
			<input type='submit' value='Clear'><br>
			<input type='hidden' name='clear' value='TRUE'>
		</form>
	<br>
	</body>
</html>

<?php

$source_link = "";
$urlid = "";

// If "clear" is in $_SESSION, destroy the session and redirect.
if (isset($_SESSION['clear'])) {
	session_destroy();

	header ('HTTP/1.1 303 See Other');
	header ('Location: ./youtube.php');
}

// If "clear" is present in $_POST, put it in $_SESSION and redirect.
if (isset($_POST['clear'])) {
	$_SESSION['clear'] = $_POST['clear'];
	
	header ('HTTP/1.1 303 See Other');
	header ('Location: ./youtube.php');
}

// If there is a source_link in $_POST, put it in $_SESSION and redirect.
if (isset($_POST['source_link'])) {
	$_SESSION['source_link'] = $_POST['source_link'];
	
	header ('HTTP/1.1 303 See Other');
	header ('Location: ./youtube.php');
}

// If there is a source_link in $_SESSION, let's process it and get some links.
if (isset($_SESSION['source_link'])) {
	$source_link = $_SESSION['source_link'];
	
	// stristr($haystack, $needle, FALSE)
	// (FALSE returns all of $haystack starting from and including first occurence of $needle)
	$urlid = stristr($source_link, 'v=', FALSE);
	$urlid = substr($urlid, 2);

	$youtube = new ZarkielYoutube();
	$download_links = $youtube->getDownloadLinks($urlid);
}

// Show the array of download links (if debug is enabled)
if ($debug === TRUE) {
	echo "<pre>";
	if (isset($download_links)) {
		print_r($download_links);
	}
	echo "</pre>";
}

// Show a single link for downloading the video, High Quality only at the moment.
if (isset($download_links)) {
	echo "<a href='" . $download_links['MP4']['High Quality - 1280x720'] . "' target='_blank' download>Download High Quality</a>";
}
 

/*
Copyright (c) <2013> <Zarkiel>

Permission is hereby granted, free of charge, to any
person obtaining a copy of this software and associated
documentation files (the "Software"), to deal in the
Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the
Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice
shall be included in all copies or substantial portions of
the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY
KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

// This class allows you to get the download links from any youtube video
// @author Zarkiel

class ZarkielYoutube{

	// The video map for the results
	private $videoMap = array(
		"13" => array("3GP", "Low Quality - 176x144"),
		"17" => array("3GP", "Medium Quality - 176x144"),
		"36" => array("3GP", "High Quality - 320x240"),
		"5" => array("FLV", "Low Quality - 400x226"),
		"6" => array("FLV", "Medium Quality - 640x360"),
		"34" => array("FLV", "Medium Quality - 640x360"),
		"35" => array("FLV", "High Quality - 854x480"),
		"43" => array("WEBM", "Low Quality - 640x360"),
		"44" => array("WEBM", "Medium Quality - 854x480"),
		"45" => array("WEBM", "High Quality - 1280x720"),
		"18" => array("MP4", "Medium Quality - 480x360"),
		"22" => array("MP4", "High Quality - 1280x720"),
		"37" => array("MP4", "High Quality - 1920x1080"),
		"38" => array("MP4", "High Quality - 4096x230")
	);
	
	private $videoPageUrl = 'http://www.youtube.com/watch?v=';
	
	// Returns the entire YouTube page's content as a string
	protected function getPageContent($id){
		$page = $this->videoPageUrl.$id;
		$content = file_get_contents($page);
		return $content;
	}
	
	/**
	 * Return the download links
	 * 
	 * @param string The video id
	 * @return array The download links
	 */ 
	function getDownloadLinks($id){
		$content = $this->getPageContent($id);

		$videos = array('MP4' => array(), 'FLV' => array(), '3GP' => array(), 'WEBM' => array());

	//if(preg_match('/\"url_encoded_fmt_stream_map\": \"(.*)\"/iUm', $content, $r)){
		if(preg_match('/url_encoded_fmt_stream_map(.*?);/', $content, $r)){
			
			//echo "<pre>";
			//print_r($r);
			//echo "</pre>";

			$data = "";
			$cdata = "";
			$xdata = "";
			$sig = "";
			$url = "";
			$type = "";

			$data = $r[1];
			$data = explode(',', $data);
			
			foreach($data As $cdata){
				$cdata = str_replace('\u0026', '&', $cdata);
				$cdata = explode('&', $cdata);

				foreach($cdata As $xdata){
					
					//echo "<br>";
					//echo "xdata is: " . $xdata;
					//echo "<br>";
					
					if(preg_match('/^sig/', $xdata)){
						$sig = substr($xdata, 4);
					}
					
					if(preg_match('/^url/', $xdata)){
						$url = substr($xdata, 4);
					}
					
					if(preg_match('/^itag/', $xdata)){
						$type = substr($xdata, 5);
					}
				}
				$url = urldecode($url).'&signature='.$sig;
				$videos[$this->videoMap[$type][0]][$this->videoMap[$type][1]] = $url;
			}
		}
		
		return $videos;
	}
}
?>