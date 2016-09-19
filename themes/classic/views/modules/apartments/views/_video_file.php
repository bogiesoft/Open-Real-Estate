<?php if ($video) :?>
	<link href="//vjs.zencdn.net/5.8/video-js.min.css" rel="stylesheet">
	<script src="//vjs.zencdn.net/5.8/video.min.js"></script>

	<?php
		$filePath = Yii::app()->request->baseUrl.'/uploads/video/'.$apartment_id.'/'.$video;
		$fileFolder = Yii::getPathOfAlias('webroot.uploads.video').DIRECTORY_SEPARATOR.$apartment_id.DIRECTORY_SEPARATOR.$video;
	?>

	<?php if (file_exists($fileFolder)) : ?>
		<video id="really-cool-video-<?php echo $id;?>" class="video-js vjs-default-skin" controls preload="auto" width="640" height="264">
			<source src="<?php echo $filePath;?>" type="video/mp4">
			<p class="vjs-no-js">
			  To view this video please enable JavaScript, and consider upgrading to a web browser
			  that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
			</p>
		</video>
		<?php
			Yii::app()->clientScript->registerScript('player-'.$id.'', '
				var realtyPlayer'.$id.' = videojs("realty-video-'.$id.'", { /* Options */ }, function() {
				});
			', CClientScript::POS_END);
		?>
	<?php endif; ?>
<?php endif; ?>
