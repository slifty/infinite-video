<?php
require_once("models/Video.php");

$row = 1;
if (($handle = fopen("ads.csv", "r")) !== FALSE) {
	while (($data = fgetcsv($handle)) !== FALSE) {
		foreach($data as $url) {
			echo(strpos($url,'&'));
			$url = substr($url, 0, strpos($url, '&'));
			$video = new Video();
			$video->setUrl($url);
			$video->save();
		}
	}
	fclose($handle);
}

?>