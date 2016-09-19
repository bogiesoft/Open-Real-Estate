<?php
$ownersList = SearchHelper::getOwnerList();
?>

<div class="rowold">
    <div class=""><?php echo tc('Listing from') ?>:</div>
    <?php echo CHtml::dropDownList('Apartment[ot]', $model->ot, $ownersList, array(
        'id' => 'search_ot',
        //'empty' => tc('Listing from'),
    )); ?>
</div>