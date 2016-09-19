<link href="//vjs.zencdn.net/5.8/video-js.min.css" rel="stylesheet">
<script src="//vjs.zencdn.net/5.8/video.min.js"></script>

<div class="video-block">
	<?php
		if($data->video){
			$videoHtml = array();
			$count = 0;

			foreach($data->video as $video){
				if($video->isFile()){
					if($video->isFileExists()) { ?>
						<div class="video-file-block">
							<video id="realty-video-<?php echo $video->id;?>" class="video-js vjs-default-skin" controls preload="auto" width="640" height="264">
								<source src="<?php echo $video->getFileUrl();?>" type="video/mp4">
								<p class="vjs-no-js">
								  To view this video please enable JavaScript, and consider upgrading to a web browser
								  that <a href="http://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
								</p>
							</video>
						</div>

						<?php
						Yii::app()->clientScript->registerScript('player-'.$video->id.'', '							
							var realtyPlayer'.$video->id.' = videojs("realty-video-'.$video->id.'", { /* Options */ }, function() {
							});
						', CClientScript::POS_END);
						?>
					<?php }
				}
				if($video->isHtml()){
					echo '<div class="video-html-block" id="video-block-html-'.$count.'"></div>';
					$videoHtml[$count] = CHtml::decode($video->video_html);
					$count++;
				}
			}

			$script = '';
			if($videoHtml){
				foreach($videoHtml as $key => $value){
					$script .= '$("#video-block-html-'.$key.'").html("'.CJavaScript::quote($value).'");';
				}
			}
			if($script){
				Yii::app()->clientScript->registerScript('chrome-xss-alert-preventer', $script, CClientScript::POS_READY);
			}
		}
	?>
    <div class="clear"></div>
</div>

<?php
	$script = '';
	if($videoHtml){
		foreach($videoHtml as $key => $value){
			$script .= '$("#video-block-html-'.$key.'").html("'.CJavaScript::quote($value).'");';
		}
	}

	if($script){
		Yii::app()->clientScript->registerScript('chrome-xss-alert-preventer', $script, CClientScript::POS_READY);
	}