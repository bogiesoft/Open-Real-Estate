<?php
if(isset($guestAdImages) && $guestAdImages){
	?>
	<div class="images-area">
		<?php
		foreach($guestAdImages as $image){
			?>
			<div class="image-item-apartment-guestad" id="image_<?php echo $image['id']; ?>">
				<!--<div class="image-drag-area"></div>-->
				<div class="image-link-item-apartment-guestad">
					<?php
					$imgTag = CHtml::image(Images::getThumbUrlGuestAd($image, 100, 75, Images::KEEP_THUMB_PROPORTIONAL, $this->sessionId), '');
					echo $imgTag;?>
					<br/>
					<a class="deleteImageLinkGuestAd" link-id="<?php echo $image['id']; ?>" link-name="<?php echo $image['file_name']; ?>" href="javascript: void(0);">
						<?php echo tc('Delete');?>
					</a>
					<br/>
				</div>
			</div>
		<?php
		}
		?>
		<div class="clear"></div>
	</div>
<?php
}