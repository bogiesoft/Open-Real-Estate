<div class="">
    <div class=""><?php echo tc('Region') ?>:</div>
    <?php
    echo CHtml::dropDownList(
        'Apartment[loc_region]',
        $model->loc_region,
        Region::getRegionsArray($model->loc_country, 2),
        array('class' => 'searchField', 'id' => 'region',
            'ajax' => array(
                'type' => 'GET',
                'url' => $this->createUrl('/location/main/getCities'),
                'data' => 'js:"region="+$("#region").val()+"&type=2"',
                'success' => 'function(result){
							$("#ap_city").html(result);' . ((issetModule('metroStations')) ? '$("#ap_city").change()' : '') .
                    '}'
            )
        )
    );

    ?>
</div>