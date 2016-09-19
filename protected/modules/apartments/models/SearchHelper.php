<?php
/* * ********************************************************************************************
 *								Open Real Estate
 *								----------------
 * 	version				:	V1.17.2
 * 	copyright			:	(c) 2015 Monoray
 * 							http://monoray.net
 *							http://monoray.ru
 *
 * 	website				:	http://open-real-estate.info/en
 *
 * 	contact us			:	http://open-real-estate.info/en/contact-us
 *
 * 	license:			:	http://open-real-estate.info/en/license
 * 							http://open-real-estate.info/ru/license
 *
 * This file is part of Open Real Estate
 *
 * ********************************************************************************************* */

class SearchHelper
{
    public static function genCriteria($model, CDbCriteria $criteria)
    {
        //$criteria = new CDbCriteria();

        if($model->floor_min || $model->floor_max) {
            if ($model->floor_min) {
                $criteria->addCondition('floor >= :floorMin');
                $criteria->params[':floorMin'] = $model->floor_min;
            }
            if ($model->floor_max) {
                $criteria->addCondition('floor <= :floorMax');
                $criteria->params[':floorMax'] = $model->floor_max;
            }
        }

        if($model->square_min || $model->square_max) {
            if ($model->square_min) {
                $criteria->addCondition('square >= :squareMin');
                $criteria->params[':squareMin'] = $model->square_min;
            }
            if ($model->square_max) {
                $criteria->addCondition('square <= :squareMax');
                $criteria->params[':squareMax'] = $model->square_max;
            }
        }

        if($model->ot){
            $criteria->join = 'INNER JOIN {{users}} AS u ON u.id = t.owner_id';
            if($model->ot == User::TYPE_PRIVATE_PERSON){
                $ownerTypes = array(
                    User::TYPE_PRIVATE_PERSON,
                    User::TYPE_ADMIN
                );
            }
            if($model->ot == User::TYPE_AGENCY){
                $ownerTypes = array(
                    User::TYPE_AGENT,
                    User::TYPE_AGENCY
                );
            }
            if (isset($ownerTypes) && $ownerTypes)
                $criteria->compare('u.type', $ownerTypes);
        }

        if (issetModule('metroStations')) {
            $selectedMetroStations = $model->metroSrc;

            if(isset($selectedMetroStations[0]) && $selectedMetroStations[0] == 0){
                $selectedMetroStations = array();
            }

            if (!empty($selectedMetroStations)) {
                if (is_array($selectedMetroStations))
                    $selectedMetroStations = array_map("intval", $selectedMetroStations);
                else
                    $selectedMetroStations = (int) $selectedMetroStations;

                if ($selectedMetroStations) {
                    if (!is_array($selectedMetroStations))
                        $selectedMetroStations = array($selectedMetroStations);

                    $sqlMetro = 'SELECT DISTINCT apartment_id FROM {{apartment_metro_stations}} WHERE metro_id IN ('.implode(',', $selectedMetroStations).')';
                    $criteria->addCondition('(t.id IN('.$sqlMetro.'))');
                }
            }
        }

        $useSeasonalPrices = issetModule('seasonalprices');

        if($model->price_min || $model->price_max) {

            if(issetModule('currency')){
                $model->price_min = floor(Currency::convertToDefault($model->price_min));
                $model->price_max = ceil(Currency::convertToDefault($model->price_max));
            }
            else {
                $model->price_min = (int) $model->price_min;
                $model->price_max = (int) $model->price_max;
            }

            if($model->price_min && $model->price_max){
                if ($useSeasonalPrices) {
                    // for non rent items
                    $or = '
				(
					t.price_type NOT IN('.Apartment::PRICE_PER_HOUR.', '.Apartment::PRICE_PER_DAY.', '.Apartment::PRICE_PER_WEEK.', '.Apartment::PRICE_PER_MONTH.')
					AND t.price >= :priceMin AND t.price <= :priceMax
				)';

                    $criteria->addCondition(
                        '
					(t.id IN(SELECT apartment_id FROM {{seasonal_prices}} WHERE price >= '.$model->price_min.' AND price <= '.$model->price_max.')
					OR (is_price_poa = 1)
					OR '.$or.'
					)
					'
                    );
                    unset($or);
                }
                else {
                    $criteria->addCondition('(price >= :priceMin AND price <= :priceMax) OR (is_price_poa = 1)');
                }

                $criteria->params[':priceMin'] = $model->price_min;
                $criteria->params[':priceMax'] = $model->price_max;

            }elseif($model->price_min){
                if ($useSeasonalPrices) {
                    // for non rent items
                    $or = '
					(
						t.price_type NOT IN('.Apartment::PRICE_PER_HOUR.', '.Apartment::PRICE_PER_DAY.', '.Apartment::PRICE_PER_WEEK.', '.Apartment::PRICE_PER_MONTH.')
						AND t.price >= :priceMin
					)';

                    $criteria->addCondition(
                        '
					(t.id IN (SELECT apartment_id FROM {{seasonal_prices}} WHERE price >= :priceMin)
					OR (is_price_poa = 1)
					OR '.$or.'
					)
					'
                    );
                    unset($or);
                }
                else {
                    $criteria->addCondition('price >= :priceMin OR is_price_poa = 1');
                }
                $criteria->params[':priceMin'] = $model->price_min;
            }elseif($model->price_max){
                if ($useSeasonalPrices) {
                    // for non rent items
                    $or = '
					(
						t.price_type NOT IN('.Apartment::PRICE_PER_HOUR.', '.Apartment::PRICE_PER_DAY.', '.Apartment::PRICE_PER_WEEK.', '.Apartment::PRICE_PER_MONTH.')
						AND t.price <= :priceMax
					)';

                    $criteria->addCondition(
                        '
					(t.id IN (SELECT apartment_id FROM {{seasonal_prices}} WHERE price <= :priceMax)
					OR (is_price_poa = 1)
					OR '.$or.'
					)
					'
                    );
                    unset($or);
                }
                else {
                    $criteria->addCondition('price <= :priceMax OR is_price_poa = 1');
                }
                $criteria->params[':priceMax'] = $model->price_max;
            }
        }

        if($model->apType){
            if ($useSeasonalPrices &&
                in_array($model->apType, array(
                        Apartment::PRICE_PER_HOUR,
                        Apartment::PRICE_PER_DAY,
                        Apartment::PRICE_PER_WEEK,
                        Apartment::PRICE_PER_MONTH)
                )) {

                $criteria->addCondition('(t.id IN(SELECT DISTINCT(apartment_id) FROM {{seasonal_prices}} WHERE price_type = '.$model->apType.'))');
            }
            else {
                $criteria->addCondition('t.price_type = :apType');
                $criteria->params[':apType'] = $model->apType;
            }
        }

        //booking
        if($model->bStart){
            $dateStart = Yii::app()->dateFormatter->format('yyyy-MM-dd', CDateTimeParser::parse($model->bStart, Booking::getYiiDateFormat()));
            if($model->bEnd){
                $dateEnd = Yii::app()->dateFormatter->format('yyyy-MM-dd', CDateTimeParser::parse($model->bEnd, Booking::getYiiDateFormat()));
            }else{
                $dateEnd = $dateStart;
            }

            if($dateStart && $dateEnd){
                $criteria->addCondition('t.id NOT IN (
                    SELECT DISTINCT b.apartment_id
                        FROM {{booking_calendar}} AS b
                        WHERE b.date_start BETWEEN :b_start AND :b_end
                            OR :b_start BETWEEN b.date_start AND b.date_end
                )');
                $criteria->params['b_start'] = $dateStart;
                $criteria->params['b_end'] = $dateEnd;
            }
        }

        //with photo
        if($model->wp){
            $criteria->addCondition('count_img > 0');
        }

        if($model->photo){
            if($model->photo == 1){
                $criteria->addCondition('count_img > 0');
            } elseif($model->photo == 2) {
                $criteria->addCondition('count_img = 0');
            }
        }

        //$doTermSearch = Yii::app()->request->getParam('do-term-search');
        $term = $model->term;
        if ($term /*&& $doTermSearch */) {
            $term = utf8_substr($term, 0, 50);
            $term = cleanPostData($term);

            if ($term && utf8_strlen($term) >= param('minLengthSearch', 4)) {
                Yii::app()->controller->term = $term;

                $words = explode(' ', $term);
                foreach($words as $key=>$value){
                    if(mb_strlen($value, "UTF-8") < param('minLengthSearch', 4) ){
                        unset($words[$key]);
                    }
                }

                if (count($words) > 1) {
                    $cleanWords = array();
                    foreach($words as $word){
                        if(utf8_strlen($word) >= param('minLengthSearch', 4)){
                            $cleanWords[] = $word;
                        }
                    }

                    $searchString = '+'.implode('* +', $cleanWords).'* '; # https://dev.mysql.com/doc/refman/5.5/en/fulltext-boolean.html

                    $sql = 'SELECT id
					FROM {{apartment}}
					WHERE MATCH
						(title_'.Yii::app()->language.', description_'.Yii::app()->language.', description_near_'.Yii::app()->language.', address_'.Yii::app()->language.')
						AGAINST ("'.$searchString.'" IN BOOLEAN MODE)';
                }
                else {
                    $sql = 'SELECT id
					FROM {{apartment}}
					WHERE MATCH
						(title_'.Yii::app()->language.', description_'.Yii::app()->language.', description_near_'.Yii::app()->language.', address_'.Yii::app()->language.')
						AGAINST ("*'.$term.'*" IN BOOLEAN MODE)';
                }

                $criteria->addCondition('(t.id IN('.$sql.'))');
            }
        }


        if(issetModule('formeditor')){
            $newFieldsAll = FormDesigner::getNewFields();
            $apps = $appsLike = array();
            foreach($newFieldsAll as $field){
                if($field->type == FormDesigner::TYPE_MULTY) {
                    $value = Yii::app()->request->getParam($field->field);
                    if(!$value || !is_array($value))
                        continue;

                    $fieldString = $field->field;
                    Yii::app()->controller->newFields[$fieldString] = $value;
                    foreach($value as $val) {
                        if ($field->compare_type == FormDesigner::COMPARE_LIKE) {
                            $appsLike[] =  CHtml::listData(Reference::model()->findAllByAttributes(array('reference_value_id'=>$val), array('select'=>'apartment_id')),  'apartment_id', 'apartment_id');
                        }
                        else {
                            $apps[] = CHtml::listData(Reference::model()->findAllByAttributes(array('reference_value_id'=>$val), array('select'=>'apartment_id')),  'apartment_id', 'apartment_id');
                        }
                    }

                    if($appsLike) {
                        $appsLike = (count($appsLike) > 1) ? call_user_func_array('array_merge', $appsLike) : $appsLike[0];
                        $criteria->addInCondition('t.id', $appsLike);
                    }
                }
                else {
                    $value = CHtml::encode(Yii::app()->request->getParam($field->field));
                    if(!$value){
                        continue;
                    }
                    $fieldString = $field->field;

                    Yii::app()->controller->newFields[$fieldString] = $value;

                    switch($field->compare_type){
                        case FormDesigner::COMPARE_EQUAL:
                            $criteria->compare($fieldString, $value);
                            break;

                        case FormDesigner::COMPARE_LIKE:
                            $criteria->compare($fieldString, $value, true);
                            break;

                        case FormDesigner::COMPARE_FROM:
                            $value = intval($value);
                            $criteria->compare($fieldString, ">={$value}");
                            break;

                        case FormDesigner::COMPARE_TO:
                            $value = intval($value);
                            $criteria->compare($fieldString, "<={$value}");
                            break;
                    }
                }
            }
            if($apps) {
                $apps = (count($apps) > 1) ? call_user_func_array('array_intersect', $apps) : $apps[0];
                $criteria->addInCondition('t.id', $apps);
            }
        }

        if($model->rooms) {
            if($model->rooms == 4) {
                $criteria->addCondition('num_of_rooms >= :rooms');
            } else {
                $criteria->addCondition('num_of_rooms = :rooms');
            }
            $criteria->params[':rooms'] = (int) $model->rooms;
        }

        return $criteria;
    }

    public static function badge($label, $onclick = '')
    {
        return '<div class="badge">'.$label.'&nbsp;&nbsp;<i class="close-filter icon-remove" onclick="'.$onclick.'"></i></div>';
    }

    public static function getBageForMetro($metroSel, $city)
    {
        $metros = MetroStations::getMetrosArray($city, 0);
        $label = array();
        foreach($metroSel as $id){
            if(isset($metros[$id])) $label[] = $metros[$id];
        }
        return $label ? self::badge(implode(', ', $label), 'filter.clearMetro();') : '';
    }

    public static function getRoomsList()
    {
        return array(
            '0' => '',
            '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => Yii::t('common', '4 and more'),
        );
    }

    public static function getOwnerList()
    {
        return array(
            0 => '',
            User::TYPE_PRIVATE_PERSON => tc('Private person'),
            User::TYPE_AGENCY => tc('Company'),
        );
    }

    public static function getPhotoList()
    {
        return array(
            0 => '',
            1 => tc('With photo'),
            2 => tc('Without photo'),
        );
    }
}