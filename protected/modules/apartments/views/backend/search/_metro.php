<?php $metros = MetroStations::getMetrosArray($model->loc_city, 0); ?>

<div class="" id="metro-block"
     style="display: block; <?php /* echo ($metros && count($metros) > 1) ? 'block;' : 'none;'; */ ?>">
    <div class=""><?php echo Yii::t('common', 'Subway stations') ?>:</div>
    <?php
    echo Chosen::multiSelect('Apartment[metroSrc][]', $model->metroSrc, $metros,
        array('id' => 'metro', 'class' => ' searchField', 'data-placeholder' => tt('Select metro stations', 'metroStations'))
    );
    echo "<script>$('#metro').chosen();</script>";
    ?>
</div>
<br/>