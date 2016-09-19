<?php
if(Yii::app()->user->id && $model->objType && $model->objType->parent_id){
    if(HApartment::checkIssetParentList($model->objType->parent_id)){		
        echo $form->labelEx($model, 'parent_id');
		$this->widget('CAutoComplete', array(
			'model' => $model,
			'attribute' => 'parent_id_autocomplete',
			'url' => array('/apartments/main/getParentObject', 'objTypeID' => $model->objType->parent_id),
			'multiple'=>true,
			'htmlOptions' => array('class'=>'span5', 'onblur' => 'checkFillParentId($(this));', 'onkeyup' => 'checkFillParentId($(this));', 'id' => 'Apartment_parent_id_autocomplete'),
			'minChars' => 0,
			'matchCase' => false,
			'methodChain'=>".result(function(event,item){\$(\"#Apartment_parent_id\").val(item[1]);})",
		));
		?>
		<div><span class="label label-info"><?php echo tc('enter initial letters');?></span></div>
        <?php echo $form->error($model, 'parent_id');
		
		echo CHtml::hiddenField(get_class($model).'[parent_id]', $model->parent_id, array('id' => 'Apartment_parent_id'));
    }
}
?>
<br />

<script>
	function checkFillParentId(elem) {
		if (elem.val().length < 1) {
			$("#Apartment_parent_id").val('');
			$("#Apartment_parent_id_autocomplete").val('');
		}
	}
</script>