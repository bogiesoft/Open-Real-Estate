<?php
$photoList = SearchHelper::getPhotoList()
?>
<div class="rowold">
    <div class=""><?php echo tc('Photo') ?>:</div>
    <?php echo CHtml::dropDownList('Apartment[photo]', $model->photo, $photoList, array(
        //'empty' => tc('Photo'),
        'id' => 'search_photo',
    )); ?>
</div>