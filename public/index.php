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
		<title>Infinite Political Ad</title>
		
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
		<script src="scripts/pocorn.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="scripts/infiniteVideo.js" type="text/javascript" charset="utf-8"></script>
		
		<script type="text/javascript">
			var ads = [<?=$videoJson?>];
			$(function() {
				$("#infiniteVideo").infiniteVideo(ads);
			});
		</script>
		<link rel="stylesheet" href="styles/main.css" type="text/css" media="screen" title="no title" charset="utf-8">
	</head>
	<body>
		<div id="container">
			<h1>The Infinite Political Ad</h1>
			<p>If you're anything like me you can't shake the feeling that you've lost something special.  You can't put your finger on it. There is something that used to be in your life and is no longer there.</p>
			<p>Think about it.  You miss political ads!  You NEED political ads!</p>
			<p>Never fear.  This one will never end.</p>
			<div id="infiniteVideo"></div>
		</div>
	</body>
</html>