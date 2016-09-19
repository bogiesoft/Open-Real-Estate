<div class="">
    <div class=""><?php echo Yii::t('common', 'City') ?>:</div>
    <?php
    $cities = (isset($cities) && count($cities)) ? $cities : ApartmentCity::getAllCity();

    echo CHtml::dropDownList(
        'Apartment[city_id]',
        $model->city_id,
        $cities,
        array('class' => ' searchField', 'id' => 'ap_city', 'empty' => Yii::t('common', 'City')) //, 'multiple' => 'multiple'
    );
    ?>
</div>