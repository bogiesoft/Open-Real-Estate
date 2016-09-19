<?php
$objTypes = CArray::merge(array(0 => ''), ApartmentObjType::getList());
?>

<div class="rowold">
    <div class=""><?php echo tc('Property type') ?>:</div>
    <?php echo CHtml::dropDownList('Apartment[obj_type_id]', $model->obj_type_id, $objTypes, array('id' => 'obj_type')); ?>
</div>