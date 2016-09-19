<div class="rowold">
    <div class=""><?php echo Yii::t('common', 'City') ?>:</div>
    <?php
    $cities = City::getCitiesArray($model->loc_region, 2);
    $cities = (isset($cities) && count($cities)) ? $cities : CArray::merge(array(0 => tc('select city')), ApartmentCity::getAllCity());

    echo CHtml::dropDownList(
        'Apartment[loc_city]',
        $model->loc_city,
        $cities,
        array(
            'class' => ' searchField',
            'id' => 'ap_city',
            'ajax' => array(
                'type' => 'GET',
                'url' => $this->createUrl('/metroStations/main/getMetroStations'),
                'data' => 'js:"city="+$("#ap_city").val()+"&type=0"',
                'dataType' => 'json',
                'success' => 'function(result){
							if (result.dropdownMetro) {
								//$("#metro-block").show();
								$("#metro").html(result.dropdownMetro);
								$("#metro").trigger("chosen:updated");
								//$("#metro")[0].sumo.reload();
							}
							else {
								//$("#metro-block").hide();
								$("#metro").html("");
								$("#metro").trigger("chosen:updated");
								//$("#metro")[0].sumo.reload();
							}
						}'
            ),
        )
    );
    ?>
</div>