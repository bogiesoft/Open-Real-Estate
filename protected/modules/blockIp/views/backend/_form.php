<div class="form">
	<?php $form=$this->beginWidget('CustomForm', array(
		'id'=>$this->modelName.'-form',
		'enableClientValidation'=>false,
		'htmlOptions' => array('class' => 'well form-disable-button-after-submit'),
	)); ?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

	<div class="rowold">
		<?php echo $form->labelEx($model, 'ip'); ?>
		<?php echo $form->textField($model, 'ip', array('size' => 10)); ?>
		<?php echo $form->error($model, 'ip'); ?>
	</div>

	<div class="rowold buttons">
		<?php $this->widget('bootstrap.widgets.TbButton',
			array('buttonType'=>'submit',
				'type'=>'primary',
				'icon'=>'ok white',
				'label'=> tc('Save'),
				'htmlOptions' => array(
					'class' => 'submit-button',
				),
			)
		); ?>
	</div>
	<?php $this->endWidget(); ?>
</div><!-- form -->