<?php
$activeListOwner = Apartment::getApartmentsStatusArray();
?>

<div class="rowold">
    <div class=""><?php echo tt('Status (owner)', 'apartments') ?>:</div>
    <?php
    echo CHtml::dropDownList('Apartment[owner_active]', $model->owner_active, $activeListOwner, array(
        'empty' => '',
        'id' => 'owner_active',
    ));
    ?>
</div>