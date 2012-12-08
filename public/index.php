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
		<link rel="stylesheet" href="styles/main.css" type="text/css" media="screen" title="no title" charset="utf-8">
		<link rel="stylesheet" href="styles/index.css" type="text/css" media="screen" title="no title" charset="utf-8">

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js"></script>
		<script src="scripts/pocorn.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="scripts/infiniteVideo.js" type="text/javascript" charset="utf-8"></script>
		
		<script type="text/javascript">
			var videos = [<?=$videoJson?>];
			$(function() {
				$("#infiniteVideo").infiniteVideo(videos);
			});
		</script>
	</head>
	<body>
		<div id="explanation">Q=Start, W=Stop, E=StopStart</div>
		<a href="https://github.com/slifty/infinite-video"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png" alt="Fork me on GitHub"></a>
		<div id="container">
			<div id="explanation">
				<h1>The Infinite Political Ad</h1>
				<p>If you're anything like me you can't shake the feeling that you've lost something special.  You can't put your finger on it. There is something that used to be in your life and is no longer there.</p>
				<p>Think about it.  You miss political ads!  You NEED political ads!</p>
				<p>Never fear.  This one will never end.</p>
				<p>(although you may need to wait a few moments for it to load)</p>
			</div>
			<div id="infiniteVideo"></div>
		</div>
	</body>
</html>