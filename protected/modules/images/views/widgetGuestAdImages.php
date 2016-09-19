<?php 
$countGuestAdImages = 0;
$guestAdMaxPhotos = Images::getGuestAdMaxPhotos();
?>

<!--<div class="flash-notice" id="more-uploads-photos" style="display: none;"><?php //echo tc('Upload more photos from your account');?></div>-->
<div class="images-apartment-area-guestad">
	<?php
	if ($guestAdImages):?>
		<?php $countGuestAdImages = count($guestAdImages);?>
		<?php
		$this->widget('application.modules.images.components.GuestAdViewImagesWidget', array(
			'sessionId' => $this->sessionId,
			'guestAdImages' => $guestAdImages,
		));
		?>
	<?php endif; ?>
</div>
<div class="clear"></div>

<?php
$this->widget('ext.EAjaxUpload.EAjaxUpload',
	array(
		'id'=>'uploadFile',
		'config'=>array(
			'action' => Yii::app()->createUrl('/images/main/uploadguestad', array('sessionId' => $this->sessionId)),
			'allowedExtensions'=> param('allowedImgExtensions', array('jpg', 'jpeg', 'gif', 'png')),
			//'sizeLimit' => param('maxImgFileSize', 8 * 1024 * 1024),
			'postParams' => array(Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken),
			'sizeLimit' => Images::getMaxSizeLimit(),
			'minSizeLimit' => param('minImgFileSize', 5*1024),
			'multiple' => false,

			'onComplete'=>"js:function(id, fileName, responseJSON){ reloadGuestAdImagesArea(); }",
			/*'onSubmit' => 'js:function(id, fileName){  }',*/
			'messages'=>array(
				'typeError'=>tc("{file} has invalid extension. Only {extensions} are allowed."),
				'sizeError'=>tc("{file} is too large, maximum file size is {sizeLimit}."),
				'minSizeError'=>tc("{file} is too small, minimum file size is {minSizeLimit}."),
				'emptyError'=>tc("{file} is empty, please select files again without it."),
				'onLeave'=>tc("The files are being uploaded, if you leave now the upload will be cancelled."),
			),
			'showMessage'=>"js:function(result){ alert(result); }"
		)
	)
);
?>

<?php
Yii::app()->clientScript->registerScript('images-reloader-guest-ad',
	'
	jQuery(function ($) {
		if (parseInt("'.$countGuestAdImages.'") >= parseInt("'.$guestAdMaxPhotos.'")) {
			$("#uploadFile").hide();
			$("#more-uploads-photos").show();
		}
			
		$(".images-apartment-area-guestad").on("click", "a.deleteImageLinkGuestAd", function(){
			var id = $(this).attr("link-id");
			var linkName = $(this).attr("link-name");
			$.ajax({
				url: "'.Yii::app()->controller->createUrl('/images/main/deleteimageguestad').'",
				data: {"id" : id, "'.Yii::app()->request->csrfTokenName.'" : "'.Yii::app()->request->csrfToken.'", "linkName" : linkName},
				success: function(result){
					$("#image_"+id).remove();

					if ($(".image-item-apartment-guestad").length <= parseInt("'.$guestAdMaxPhotos.'")) {
						$("#uploadFile").show();
						$("#more-uploads-photos").hide();
					}
					else {
						$("#uploadFile").hide();
						$("#more-uploads-photos").show();
					}
				},
				error: function(err){
					error("'.tc("Error loading. Try again later.").'");
				}
			});
			return false;
		});
	});

	function reloadGuestAdImagesArea(){
		$.ajax({
			type: "POST",
			url: "'.Yii::app()->controller->createUrl('/images/main/getimagesguestad', array('sessionId' => $this->sessionId)).'",
			data: {"'.Yii::app()->request->csrfTokenName.'" : "'.Yii::app()->request->csrfToken.'"},
			success: function(data){				
				var alrclear;
				alrclear = 0;

				$(".image-link-item-apartment-guestad", data).each(function(){
					var name;
					name = $(this).attr("name");

					var toAdd = $(this).closest(".image-item-apartment-guestad");
					if($(".images-area > .clear").length){
						if (alrclear == 0) {
							$(".images-area").empty();
							$(".images-area").append("<div class=\"clear\"></div>");
							alrclear = 1;
						}
						$(".images-area > .clear").before(toAdd);
					} else {
						$(".images-apartment-area-guestad").empty();
						$(".images-apartment-area-guestad").append("<div class=\"images-area\"></div>");
						$(".images-area").append(toAdd);
						$(".images-area").append("<div class=\"clear\"></div>");
					}
				});
				
				if ($(".image-link-item-apartment-guestad").length >= parseInt("'.$guestAdMaxPhotos.'")) {
					$("#uploadFile").hide();
					$("#more-uploads-photos").show();
				}
				else {
					$("#uploadFile").show();
					$("#more-uploads-photos").hide();
				}
			},
			error: function(err){
				error("'.tc("Error loading. Try again later.").'");
			}
		});
	}
	',
	CClientScript::POS_END);
?>