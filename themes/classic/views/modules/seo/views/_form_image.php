<div id="seo_image_form_<?php echo $friendlyUrl->id;?>" class="seo_image_form">
	<?php echo CHtml::errorSummary($friendlyUrl); ?>

	<?php
	$this->widget('application.modules.lang.components.langFieldWidget', array(
		'model' => $friendlyUrl,
		'field' => 'alt',
		'type' => 'string',
	));
	?>

	<?php echo CHtml::hiddenField(Yii::app()->request->csrfTokenName, Yii::app()->request->csrfToken);?>
	<?php echo CHtml::hiddenField('scenario', 'image'); ?>
	<?php echo CHtml::hiddenField('fromSeoImageWidget', true); ?>
	<?php echo CHtml::hiddenField('SeoFriendlyUrl[model_name]', $friendlyUrl->model_name); ?>
	<?php echo CHtml::hiddenField('SeoFriendlyUrl[model_id]', $friendlyUrl->model_id); ?>
	<?php echo CHtml::hiddenField('SeoFriendlyUrl[id]', $friendlyUrl->id); ?>

	<?php echo CHtml::submitButton(tc('Save'), array('onclick' => 'js:saveSeoImage('.$friendlyUrl->id.'); return false;', 'class' => 'button-blue')); ?>
	&nbsp;<?php echo CHtml::button(tc('Close'), array('onclick' => 'js:closeSeoImage('.$friendlyUrl->id.'); return false;', 'class' => 'button-blue button-gray')); ?>
</div>