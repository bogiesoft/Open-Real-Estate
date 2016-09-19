<div class="search well" style="display: none;" id="search_ap">
    <h4><?php echo tt('Filter for listings\' list', 'infopages') ?></h4>

    <form action="<?php echo Yii::app()->createUrl('/apartments/backend/main/admin') ?>" method="get" id="ap_filter">
        <div class="row-fluid">
        <?php
        $searchFields = array(
            'id', 'ownerEmail', 'status', 'status_owner', 'type', 'obj_type_id', 'price', 'floor', 'square', 'rooms', 'ot', 'photo'
        );
        if(issetModule('location')){
            $searchFields = CArray::merge(array('country', 'region', 'city', 'metro'), $searchFields);
        }else{
            $searchFields = CArray::merge(array('city_one'), $searchFields);
        }

        if(issetModule('paidservices')){
            $searchFields[] = 'searchPaidService';
        }

        $countColumn = 3;
        $countAllFields = count($searchFields) + (isset($addedFields) ? count($addedFields) : 0);
        $fieldsInColumn = round($countAllFields / $countColumn);

        $i = 0;
        $s = 0;
        $column = 0;
        $divOpen = true;
        echo '<div class="span4">';
        foreach($searchFields as $field){
            require 'search/_'.$field.'.php';
            $i++;
            $s++;
            if($i>=$fieldsInColumn){
                $i = 0;
                $column++;
                if($column < $countColumn){
                    echo '</div><div class="span4">';
                }elseif($s >= $countAllFields){
                    $divOpen = false;
                    echo '</div>';
                }
            }
        }

        if (isset($addedFields) && $addedFields && count($addedFields)) {
            foreach ($addedFields as $adField) {
                ?>
                <div class="rowold">
                    <div class=""><?php echo $adField['label']; ?>:</div>
                    <?php if (isset($adField['listData']) && $adField['listData']): ?>
                        <?php echo CHtml::dropDownList('Apartment[' . $adField['field'] . ']', $model->{$adField['field']}, CMap::mergeArray(array("" => ""), $adField['listData'])); ?>
                    <?php else: ?>
                        <?php echo CHtml::textField('Apartment[' . $adField['field'] . ']', $model->{$adField['field']}); ?>
                    <?php endif; ?>
                </div>
                <?php

                if($i>=$fieldsInColumn){
                    $i = 0;
                    $column++;
                    if($column < $countColumn){
                        echo '</div><div class="span4">';
                    }elseif($s >= $countAllFields){
                        $divOpen = false;
                        echo '</div>';
                    }
                }
            }
        }
        if($divOpen){
            echo '</div>';
        }

        ?>

        <div class="clear"></div>

        <input type="submit" value="<?php echo tc('Apply') ?>" class="btn btn-primary">

        </div>
    </form>

</div>

<?php
$badges = array();
if($model->id){
    $badges[] = SearchHelper::badge(tt('ID', 'apartments').': '.$model->id, 'filter.clearInput(\'#ap_find_id\');');
}
if($model->ownerEmail){
    $badges[] = SearchHelper::badge(tt('Owner email', 'apartments').': '.$model->ownerEmail, 'filter.clearInput(\'#ap_find_ownerEmail\');');
}
if(isset($_GET['Apartment']['active']) && isset($activeList[$model->active])){
    $badges[] = SearchHelper::badge(tt('Status', 'apartments').': '.$activeList[$model->active], 'filter.clearSelect(\'#active\', true);');
}
if(isset($_GET['Apartment']['owner_active']) && isset($activeListOwner[$model->owner_active])){
    $badges[] = SearchHelper::badge(tt('Status (owner)', 'apartments').': '.$activeListOwner[$model->owner_active], 'filter.clearSelect(\'#owner_active\', true);');
}
if($model->loc_country){
    $county = Country::model()->findByPk($model->loc_country);
    if($county){
        $badges[] = SearchHelper::badge($county->getName(), 'filter.clearSelect(\'#ap_city\', false); filter.clearSelect(\'#region\', false); filter.clearSelect(\'#county_f\', true);');
    }
}
if($model->loc_region){
    $region = Region::model()->findByPk($model->loc_region);
    if($region){
        $badges[] = SearchHelper::badge($region->getName(), 'filter.clearSelect(\'#ap_city\', false); filter.clearSelect(\'#region\', true);');
    }
}
if($model->loc_city){
    $city = City::model()->findByPk($model->loc_city);
    if($city){
        $badges[] = SearchHelper::badge($city->getName(), 'filter.clearSelect(\'#ap_city\', true);');
    }
}

if($model->metroSrc && $model->loc_city){
    $badgeMetro = SearchHelper::getBageForMetro($model->metroSrc, $model->loc_city);
    if($badgeMetro){
        $badges[] = $badgeMetro;
    }
}
if($model->type && isset($types[$model->type])){
    $badges[] = SearchHelper::badge($types[$model->type], 'filter.clearSelect(\'#type_f\', true);');
}
if($model->obj_type_id && isset($objTypes[$model->obj_type_id])){
    $badges[] = SearchHelper::badge($objTypes[$model->obj_type_id], 'filter.clearSelect(\'#obj_type\', true);');
}
if($model->price_min){
    $badges[] = SearchHelper::badge(tc('Price from').': '.$model->price_min, 'filter.clearInput(\'#price_min\');');
}
if($model->price_max){
    $badges[] = SearchHelper::badge(tc('Price to').': '.$model->price_max, 'filter.clearInput(\'#price_max\');');
}
if($model->floor_min){
    $badges[] = SearchHelper::badge(tc('Floor from').': '.$model->floor_min, 'filter.clearInput(\'#floor_min\');');
}
if($model->floor_max){
    $badges[] = SearchHelper::badge(tc('Floor to').': '.$model->floor_max, 'filter.clearInput(\'#floor_max\');');
}
if($model->square_min){
    $badges[] = SearchHelper::badge(tc('Square from').': '.$model->square_min, 'filter.clearInput(\'#square_min\');');
}
if($model->square_max){
    $badges[] = SearchHelper::badge(tc('Square to').': '.$model->square_max, 'filter.clearInput(\'#square_max\');');
}
if($model->searchPaidService && isset($paidServicesArray[$model->searchPaidService])){
    $badges[] = SearchHelper::badge(tc('Paid services').': '.$paidServicesArray[$model->searchPaidService], 'filter.clearSelect(\'#searchPaidService\', true);');
}
$roomItems = SearchHelper::getRoomsList();
if($model->rooms && isset($roomItems[$model->rooms])){
    $badges[] = SearchHelper::badge(tc('Number of rooms').': '.$roomItems[$model->rooms], 'filter.clearInput(\'#rooms\');');
}
$ownerList = SearchHelper::getOwnerList();
if($model->ot && isset($ownerList[$model->ot])){
    $badges[] = SearchHelper::badge(tc('Listing from').': '.$ownerList[$model->ot], 'filter.clearInput(\'#search_ot\');');
}
$photoList = SearchHelper::getPhotoList();
if($model->photo && isset($photoList[$model->photo])){
    $badges[] = SearchHelper::badge(tc('Photo').': '.$photoList[$model->photo], 'filter.clearInput(\'#search_photo\');');
}
echo '<div class="inline" id="search_badge">';
if($badges){
    echo implode(' ', $badges);
    echo '  ' .CHtml::link(tc('Clear all filter'), Yii::app()->createUrl('/apartments/backend/main/admin', array('resetFilters' => 1)), array(
        'class' => 'btn btn-info',
    ));
}
echo '</div>';

if(!Yii::app()->request->isAjaxRequest){
?>
<script>
    var filter = {
        clearSelect: function(id, submit){
            $(id+" option[selected]").removeAttr("selected");
            $(id+" option:first").attr("selected", "selected");
            $(id).val("");
            if(submit === true){
                filter.submit();
            }
        },
        clearInput: function(id){
            $(id).val('').change();
            filter.submit();
        },
        submit: function() {
//            $('#ap_filter').submit();
//            return false;
            $.fn.yiiGridView.update('apartments-grid', {
                data: $($('#ap_filter')).serialize(),

                complete: function(jqXHR, status) {
                    if (status=='success'){
                        var $data = $('<div>' + jqXHR.responseText + '</div>');
                        var $badges = $($data).find('#search_badge');
                        $('#search_badge').html($badges.html());
                        //admin.afterGridUpdate();
                    }
                }
            });
        },
        clearMetro: function(){
            $('#metro').val('').trigger("chosen:updated");
            filter.submit();
        }
    }

    $('#ap_filter').submit(function(){
        filter.submit();
        return false;
    });

</script>
<?php } ?>

<div class="clear">&nbsp;&nbsp;</div>
