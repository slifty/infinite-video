<?php
	set_include_path('../');
	require_once("models/Video.php");
	
	$videos = Video::getObjectsWithClips();
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
				if(/Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent))
					$("body").text("Sorry, this doesn't work on mobile devices");
				else if(/Safari/i.test(navigator.userAgent) && !/Chrome/i.test(navigator.userAgent))
					$("body").text("Safari hates infinite videos, apparently.  Try using Chrome or Firefox.");
				else if($.browser.msie)
					$("body").text("Internet Explorer hates infinite videos, apparently.  Try using Chrome or Firefox.");
				else
					$("#infiniteVideo").infiniteVideo(videos, {clip_selection:'random'});
			});
		</script>
	</head>
	<body>
		<a id="git" href="https://github.com/slifty/infinite-video"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://s3.amazonaws.com/github/ribbons/forkme_right_darkblue_121621.png" alt="Fork me on GitHub"></a>
		<div id="container">
			<div id="explanation">
				<p>You can't shake the feeling that you've lost something special.</p>
				<p>Think about it.  <strong>You miss political ads!</strong></p>
				<p>Never fear.  This one will never end.</p>
				<p><em><strong>Note: </strong>you may need to wait a few moments for it to load.</em></p>
			</div>
			<div id="infiniteVideo"></div>
		</div>
	</body>
</html>