<div class="rowold">
    <div class=""><?php echo tc('Paid services') ?>:</div>
    <?php
    echo CHtml::dropDownList('Apartment[searchPaidService]', $model->searchPaidService, $paidServicesArray, array(
        'empty' => '',
        'id' => 'searchPaidService',
    ));
    ?>
</div>