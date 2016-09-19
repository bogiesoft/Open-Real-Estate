<h1><?php echo tt('Set the owner of the listing', 'apartments'). ' '.$modelApartment->id; ?></h1>
<?php

$this->pageTitle .= ' - '.tt('Set the owner of the listing', 'apartments'). ' '.$modelApartment->getStrByLang('title');


	$this->breadcrumbs = array(
		Yii::t('common', 'Control panel') => array('/usercpanel/main/index'),
		tt('Set the owner of the listing', 'apartments')
	);


$form=$this->beginWidget('CActiveForm', array(
	'id' => $this->modelName.'-form',
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('class' => 'form-disable-button-after-submit'),
));

?>

<?php
echo $form->errorSummary($model);


$columns = array(
	array(
		'class'=>'CCheckBoxColumn',
		'id'=>'itemsSelected',
		'selectableRows' => '1',
		'htmlOptions' => array(
			'class'=>'center',
		),
	),
	array(
		'name' => 'type',
		'value' => '$data->getTypeName()',
		'filter' => array(User::TYPE_AGENCY => tc('Company'), User::TYPE_AGENT => tc('Agent')),
	),
	array(
		'name' => 'username',
		'header' => tt('User name', 'users'),
	),
	'email',
);

$this->widget('NoBootstrapGridView', array(
	'id'=>'owner-grid',
	'afterAjaxUpdate' => 'function(){attachStickyTableHeader();}',
	'dataProvider'=> $modelUser->search(),
	'filter'=> $modelUser,
	'columns'=>$columns
));
?>

<div id="submit" class="row buttons">
	<?php echo CHtml::submitButton(tt('Change'), array('class' => 'submit-button')); ?>
</div>

<?php $this->endWidget(); ?>