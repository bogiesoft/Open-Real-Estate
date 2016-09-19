<?php if ($this->showLink):?>
	<p>
		<img src="<?php echo Yii::app()->theme->baseUrl; ?>/images/set_alt.png" width="16" height="16" alt="<?php echo tc('Image SEO: alt tag');?>" title="<?php echo tc('Image SEO: alt tag');?>" />&nbsp;
		<?php
		echo CHtml::link(tc('Image SEO: alt tag'), 'javascript:void(0);', array(
			'onclick' => 'js:openSeoImage('.$friendlyUrl->id.');'
		));
		?>
	</p>
<?php endif;?>

<?php if ($this->showForm):?>
	<?php
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
		'id' => 'seo_image_dialog_'.$friendlyUrl->id,
		'options' => array(
			'autoOpen' => false,
			'width' => '700px',
			'modal' => true,
			'resizable'=> true,
			'closeOnEscape' => true,
		),
	));
	?>

	<div class="form seo_image_html" id="seo_image_html_<?php echo $friendlyUrl->id;?>">
		<?php $this->render('_form_image', array('friendlyUrl' => $friendlyUrl)); ?>
	</div><!-- form -->


	<?php $this->endWidget('zii.widgets.jui.CJuiDialog'); ?>

	<?php if ($this->afterRefresh):?>
		<script>
			jQuery('#seo_image_dialog_<?php echo $friendlyUrl->id;?>').dialog({'autoOpen':false,'width':'700px','modal':true,'resizable':true,'closeOnEscape':true});
			jQuery('#seo_image_dialog_<?php echo $friendlyUrl->id;?>').find('div.yiiTab').yiitab();
		</script>
	<?php endif;?>
<?php endif;?>

<?php if ($this->showJS):?>
	<?php $languages = (!isFree()) ? Lang::getActiveLangs(true) : null; ?>
	<div class="seoImageScript">
		<script>
			function saveSeoImage(id){
				id = id || <?php echo $friendlyUrl->id;?>;

				var dataPost = $('#seo_image_form_'+id+' :input').serialize();

				$.ajax({
					url : '<?php echo Yii::app()->createUrl('/seo/main/ajaxSave'); ?>',
					dataType : 'json',
					type: 'post',
					data: dataPost,
					success : function(data){
						if(data.status == 'ok'){
							closeSeoImage(id);
							message('<?php echo tt("success_saved", "service"); ?>');
							$('#seo_image_html_'+id).html(data.html);
							<?php if ($languages && count($languages)):?>
								$(".yiiTab").yiitab();
							<?php endif;?>
							return;
						} else {
							error('<?php echo tc("Error"); ?>');
							$('#seo_image_html_'+id).html(data.html);
							<?php if ($languages && count($languages)):?>
								$(".yiiTab").yiitab();
							<?php endif;?>
							return;
						}
					},
					error: function(data){
						error('<?php echo tc("Error. Repeat attempt later"); ?>');
					}
				});
			}

			function closeSeoImage(id) {
				id = id || <?php echo $friendlyUrl->id;?>;

				$("#seo_image_dialog_"+id).dialog("close");
			}

			function openSeoImage(id) {
				id = id || <?php echo $friendlyUrl->id;?>;

				$("#seo_image_dialog_"+id).dialog("open");
			}
		</script>
	</div>
<?php endif;?>