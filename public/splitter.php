<?php
	set_include_path('../');
	require_once("models/Video.php");
	
	$videos = Video::getAllObjects();
	$videoJsons = array();
	foreach($videos as $video)
		$videoJsons[] = $video->toJson();
		
	$videoJson = implode(",",$videoJsons);
?>

<html>
	<head>
		<title>Infinite Political Ad Splitter</title>

		<link rel="stylesheet" href="styles/main.css" type="text/css" media="screen" title="no title" charset="utf-8">
		<link rel="stylesheet" href="styles/splitter.css" type="text/css" media="screen" title="no title" charset="utf-8">
		
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
		<script src="scripts/pocorn.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="scripts/infiniteVideoSlicer.js" type="text/javascript" charset="utf-8"></script>
		
		<script type="text/javascript">
			var videos = [<?=$videoJson?>];
			$(function() {
				$("#slicer").infiniteVideoSlicer(videos);
			});
		</script>
	</head>
	<body>
		<div id="explanation">q/w = start/stop || e=start/stopstart || p=restart || [=prev || ]=next</div>
		<div id="slicer"></div>
	</body>
</html>