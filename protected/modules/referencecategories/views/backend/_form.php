<div class="form">

<?php $form=$this->beginWidget('CustomForm', array(
	'id'=>$this->modelName.'-form',
	'enableAjaxValidation'=>true,
	'htmlOptions' => array('class' => 'well form-disable-button-after-submit'),
)); 
echo CHtml::hiddenField('addValues', 0, array('id' => 'addValues'));
?>

	<p class="note"><?php echo Yii::t('common', 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo $form->errorSummary($model); ?>

    <?php
    $this->widget('application.modules.lang.components.langFieldWidget', array(
    		'model' => $model,
    		'field' => 'title',
            'type' => 'string'
    	));

    if(issetModule('formeditor')){
        echo $form->dropDownListRow($model, 'type', ReferenceCategories::getTypeList());
    }
    ?>

	<div class="clear"></div>
	<div class="rowold">
		<?php echo $form->labelEx($model,'style'); ?>
		<?php echo $form->dropDownList($model,'style', $model->getStyles(), array('class' => 'width150')); ?>
		<?php echo $form->error($model,'style'); ?>
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
				)); ?>
		<?php $this->widget('bootstrap.widgets.TbButton',
			array('buttonType'=>'submit',
				'type'=>'primary',
				'icon'=>'ok white',
				'htmlOptions'=>array('name'=>'addValues', 'onclick' => '$("#addValues").val(1)', 'class' => 'submit-button'),
				'label'=> tt('Save and add values'),
			)); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->