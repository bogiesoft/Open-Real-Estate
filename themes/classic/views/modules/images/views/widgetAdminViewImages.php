<?php
	if($this->images){
		?>
		<div class="images-area">
			<?php
				foreach($this->images as $image){

					if($this->withMain && $image['is_main'] || !$this->withMain && !$image['is_main'] || !$image['is_main']){
						?>
						<div class="image-item" id="image_<?php echo $image['id']; ?>">
                            <div class="image-drag-area"></div>
                        	<div class="image-link-item">
								<?php
									$imgTag = CHtml::image(Images::getThumbUrl($image, 150, 100), Images::getAlt($image));
									echo CHtml::link($imgTag, Images::getFullSizeUrl($image), array(
										'class' => 'fancy',
										'rel' => 'gallery',
										'title' => Images::getAlt($image),
									));
								?><br/>
								
								<a class="rotateImageLink" link-id="<?php echo $image['id']; ?>" href="javascript: void(0);" title="<?php echo tc('Rotate');?>">
									<img src="<?php echo Yii::app()->theme->baseUrl; ?>/images/rotate.png" width="16" height="16" alt="<?php echo tc('Rotate');?>" title="<?php echo tc('Rotate');?>" />
								</a>
							</div>
                            <div class="image-comment-input">
								<span class="setAsMain" link-id="<?php echo $image['id']; ?>">
									<?php
										if($image['is_main']){
											echo tc('Main photo');
										} else {
											echo CHtml::image(Yii::app()->theme->baseUrl.'/images/set_main.png', tc('Set as main photo'), array('width' => 16, 'height' => 16, 'title' => tc('Set as main photo')));
											echo '<a class="setAsMainLink" href="#">'.tc('Set as main photo').'</a>';
										}
										?>
								</span>
								
								<?php if (issetModule('seo') && (param('allowUserSeo', 1) || Yii::app()->user->checkAccess('backend_access'))):?>
									<div class="image-seo-input">
										<?php
											$this->widget('application.modules.seo.components.SeoImageWidget', array(
												'model' => $image,
												'showLink' => true,
												'showForm' => true,
												'showJS' => false,
												'afterRefresh' => $this->afterRefresh,
											));
										?>
									</div>
								<?php endif;?>
								
								<a class="deleteImageLink" link-id="<?php echo $image['id']; ?>" href="#">
									<img src="<?php echo Yii::app()->theme->baseUrl; ?>/images/delete.png" width="16" height="16" alt="<?php echo tc('Delete');?>" title="<?php echo tc('Delete');?>" />&nbsp;
									<?php echo tc('Delete');?>
								</a>
								<br/>
                            </div>
						</div>
						<?php
					}
				}
			?>
			<div class="clear"></div>
			<?php if (issetModule('seo') && (param('allowUserSeo', 1) || Yii::app()->user->checkAccess('backend_access'))):?>
				<?php
					$this->widget('application.modules.seo.components.SeoImageWidget', array(
						'model' => $image,
						'showLink' => false,
						'showForm' => false,
						'showJS' => true,
						'afterRefresh' => $this->afterRefresh,
					));
				?>
			<?php endif;?>
		</div>
		<?php
	}
