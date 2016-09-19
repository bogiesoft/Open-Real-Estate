<?php
$roomItems = SearchHelper::getRoomsList();
?>

<div class="rowold">
    <div class=""><?php echo tc('Number of rooms') ?>:</div>
    <?php
    echo CHtml::dropDownList('Apartment[rooms]', $model->rooms, $roomItems, array(
        //'empty' => tc('Number of rooms'),
        'id' => 'rooms',
    ));
    ?>
</div>