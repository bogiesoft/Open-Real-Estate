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

class MainController extends ModuleUserController {
	public $roomsCountMin;
	public $roomsCountMax;
	public $price;
	public $priceSlider = array();
	public $sApId;
    public $landSquare;
	public $term;
    public $bStart;
    public $bEnd;

	public function actionIndex(){
        $href = Yii::app()->getBaseUrl(true).'/'.Yii::app()->request->getPathInfo();	
		$page = (int) Yii::app()->request->getParam('page');	
		if (isset($page) && $page > 1) {
			$href .= '?page='.$page;
		}
        Yii::app()->clientScript->registerLinkTag('canonical', null, $href);
        unset($href);

		$criteria = new CDbCriteria;
		$criteria->addCondition('active = ' . Apartment::STATUS_ACTIVE);
		if(param('useUserads')) {
			$criteria->addCondition('owner_active = ' . Apartment::STATUS_ACTIVE);
		}

		if(Yii::app()->request->isAjaxRequest) {
			$this->excludeJs();
			$this->renderPartial('index', array(
				'criteria' => $criteria,
				'apCount' => null,
			), false, true);
		} else {
			$this->render('index', array(
				'criteria' => $criteria,
				'apCount' => null,
			));
		}
	}

	public function getExistRooms(){
		return Apartment::getExistsRooms();
	}

	public function actionMainsearch($rss = null){
        $countAjax = Yii::app()->request->getParam('countAjax');

        $href = Yii::app()->getBaseUrl(true).'/'.Yii::app()->request->getPathInfo();
		$page = (int) Yii::app()->request->getParam('page');	
		if (isset($page) && $page > 1) {
			$href .= '?page='.$page;
		}
        Yii::app()->clientScript->registerLinkTag('canonical', null, $href);
        unset($href);

		if(Yii::app()->request->getParam('currency')) {
			setCurrency();
			$this->redirect(array('mainsearch'));
		}

		$model = Apartment::model();

		$criteria = new CDbCriteria;
		$criteria->addCondition('t.active = ' . Apartment::STATUS_ACTIVE);
		$criteria->addCondition('t.deleted = 0');
		if(param('useUserads')) {
			$criteria->addCondition('t.owner_active = ' . Apartment::STATUS_ACTIVE);
		}

		$criteria->addInCondition('t.type', HApartment::availableApTypesIds());
		$criteria->addInCondition('t.price_type', array_keys(HApartment::getPriceArray(Apartment::PRICE_SALE, true)));

		$this->sApId = (int) Yii::app()->request->getParam('sApId');
		if ($this->sApId) {
			$criteria->addCondition('id = :sApId');
			$criteria->params[':sApId'] = $this->sApId;

			$apCount = Apartment::model()->count($criteria);
            if($countAjax && Yii::app()->request->isAjaxRequest){
                $this->echoAjaxCount($apCount);
            }

			if ($apCount) {
				$apartmentModel = Apartment::model()->findByPk($this->sApId);
				Yii::app()->controller->redirect($apartmentModel->getUrl());
				Yii::app()->end();
			}
		}

		$landSquare = Yii::app()->request->getParam('land_square');
		if($landSquare) {
			$criteria->addCondition('land_square <= :land_square');
			$criteria->params[':land_square'] = $landSquare;

			$this->landSquare = $landSquare;
		}

		$this->selectedCity = Yii::app()->request->getParam('city', array());
		if(isset($this->selectedCity[0]) && $this->selectedCity[0] == 0){
			$this->selectedCity = array();
		}

		if (is_array($this->selectedCity) && !empty($this->selectedCity))
			$this->selectedCity = array_map("intval", $this->selectedCity);
		elseif (is_numeric($this->selectedCity) && !empty($this->selectedCity))
			$this->selectedCity = (int) $this->selectedCity;
		
        if (issetModule('location')) {
			$country = (int) Yii::app()->request->getParam('country');
			if($country) {
				$this->selectedCountry = $country;
				$criteria->compare('loc_country', $country);
			}

			$region = (int) Yii::app()->request->getParam('region');
			if($region) {
				$this->selectedRegion = $region;
				$criteria->compare('loc_region', $region);
			}

            if($this->selectedCity) {
                $criteria->compare('t.loc_city', $this->selectedCity);
            }
		}
		else {
			if($this->selectedCity) {
				$criteria->compare('t.city_id', $this->selectedCity);
			}
		}

		$this->objType = (int) Yii::app()->request->getParam('objType');
		if($this->objType) {
			$criteria->compare('obj_type_id', $this->objType);
		}

		// rooms
		if(issetModule('selecttoslider') && param('useRoomSlider') == 1) {
			$roomsMin = Yii::app()->request->getParam('room_min');
			$roomsMax = Yii::app()->request->getParam('room_max');

			if($roomsMin || $roomsMax) {
				$criteria->addCondition('num_of_rooms >= :roomsMin AND num_of_rooms <= :roomsMax');
				$criteria->params[':roomsMin'] = $roomsMin;
				$criteria->params[':roomsMax'] = $roomsMax;

				$this->roomsCountMin = $roomsMin;
				$this->roomsCountMax = $roomsMax;
			}
		} else {
			$rooms = Yii::app()->request->getParam('rooms');
			if($rooms) {
				if($rooms == 4) {
					$criteria->addCondition('num_of_rooms >= :rooms');
				} else {
					$criteria->addCondition('num_of_rooms = :rooms');
				}
				$criteria->params[':rooms'] = $rooms;

				$this->roomsCount = $rooms;
			}
		}

		// поиск объявлений владельца
		$this->userListingId = Yii::app()->request->getParam('userListingId');
		if($this->userListingId) {
			$criteria->addCondition('owner_id = :userListingId');
			$criteria->params[':userListingId'] = $this->userListingId;
		}

		$filterName = null;
		// Поиск по справочникам - клик в просмотре профиля анкеты
		if(param('useReferenceLinkInView')) {
			if(Yii::app()->request->getQuery('serviceId', false)) {
				$serviceId = Yii::app()->request->getQuery('serviceId', false);
				if($serviceId) {
					$serviceIdArray = explode('-', $serviceId);
					if(is_array($serviceIdArray) && count($serviceIdArray) > 0) {
						Yii::app()->getModule('referencevalues');
						$value = (int) $serviceIdArray[0];

						$sql = 'SELECT DISTINCT apartment_id FROM {{apartment_reference}} WHERE reference_value_id = ' . $value;						
						$criteria->addCondition('(t.id IN('.$sql.'))');						

						$sql = 'SELECT title_' . Yii::app()->language . ' FROM {{apartment_reference_values}} WHERE id = ' . $value;
						$filterName = Yii::app()->db->cache(param('cachingTime', 1209600), ReferenceValues::getDependency())->createCommand($sql)->queryScalar();

						if($filterName) {
							$filterName = CHtml::encode($filterName);
						}
					}
				}
			}
		}

		// param for SearchHelper
		$this->bStart = Yii::app()->request->getParam('b_start');
		$this->bEnd = Yii::app()->request->getParam('b_end');
		if($this->bStart){
			$model->bStart = $this->bStart;
			$model->bEnd = $this->bEnd;
		}

		// type
		$model->apType = $this->apType = (int) Yii::app()->request->getParam('apType');

		$model->type = (int) Yii::app()->request->getParam('type');

		// price
		$model->price_min = $this->priceSlider['min'] = Yii::app()->request->getParam("price_min");
		$model->price_max = $this->priceSlider['max'] = Yii::app()->request->getParam("price_max");

		// ключевые слова
		$model->term = Yii::app()->request->getParam('term');

		// floor
		$this->floorCountMin = Yii::app()->request->getParam('floor_min');
		$this->floorCountMax = Yii::app()->request->getParam('floor_max');

		if($this->floorCountMin || $this->floorCountMax) {
			$model->floor_min = $this->floorCountMin;
			$model->floor_max = $this->floorCountMax;
		}

		$this->squareCountMin = Yii::app()->request->getParam('square_min');
		$this->squareCountMax = Yii::app()->request->getParam('square_max');

		if($this->squareCountMin || $this->squareCountMax) {
			$model->square_min = $this->squareCountMin;
			$model->square_max = $this->squareCountMax;
		}

		$this->wp = $model->wp = Yii::app()->request->getParam('wp');
		$this->ot = $model->ot = Yii::app()->request->getParam('ot');

		if (issetModule('metroStations')) {
			$model->metroSrc = $this->selectedMetroStations = Yii::app()->request->getParam('metro', array());
		}

		$criteria = SearchHelper::genCriteria($model, $criteria);

		if($rss && issetModule('rss')) {
			$this->widget('application.modules.rss.components.RssWidget', array(
				'criteria' => $criteria,
			));
		}

		// find count
		$apCount = Apartment::model()->count($criteria);

        if($countAjax && Yii::app()->request->isAjaxRequest){
            $this->echoAjaxCount($apCount);
        }

        $searchParams = $_GET;
        if(isset($searchParams['is_ajax'])){
            unset($searchParams['is_ajax']);
        }
        Yii::app()->user->setState('searchUrl', Yii::app()->createUrl('/search', $searchParams));
        unset($searchParams);

		if(Yii::app()->request->isAjaxRequest) {
				$this->renderPartial('index', array(
					'criteria' => $criteria,
					'apCount' => $apCount,
					'filterName' => $filterName,
				));
		} else {
			$this->render('index', array(
				'criteria' => $criteria,
				'apCount' => $apCount,
				'filterName' => $filterName,
			));
		}
	}

    public function echoAjaxCount($apCount){
//        if($apCount > 0){
//            $buttonLabel = Yii::t('common', '{n} listings', array($apCount, '{n}' => $apCount));
//        } else {
//            $buttonLabel = tc('Search');
//        }
        echo CJSON::encode(array(
            'count' => $apCount,
            'string' => Yii::t('common', '{n} listings', array($apCount, '{n}' => $apCount)),
        ));
        Yii::app()->end();
    }

    public function actionLoadForm(){
        if(!Yii::app()->request->isAjaxRequest){
            throw404();
        }

        $this->objType = CHtml::encode(Yii::app()->request->getParam('obj_type_id'));
        $isInner = CHtml::encode(Yii::app()->request->getParam('is_inner'));

        $roomsMin = CHtml::encode(Yii::app()->request->getParam('room_min'));
        $roomsMax = CHtml::encode(Yii::app()->request->getParam('room_max'));
        if($roomsMin || $roomsMax) {
            $this->roomsCountMin = $roomsMin;
            $this->roomsCountMax = $roomsMax;
        }

        $this->sApId = CHtml::encode(Yii::app()->request->getParam('sApId'));

        $this->bStart = CHtml::encode(Yii::app()->request->getParam('b_start'));
        $this->bEnd = CHtml::encode(Yii::app()->request->getParam('b_end'));

        $floorMin = CHtml::encode(Yii::app()->request->getParam('floor_min'));
        $floorMax = CHtml::encode(Yii::app()->request->getParam('floor_max'));
        if($floorMin || $floorMax) {
            $this->floorCountMin = $floorMin;
            $this->floorCountMax = $floorMax;
        }

        $this->wp = CHtml::encode(Yii::app()->request->getParam('wp'));
        $this->ot = CHtml::encode(Yii::app()->request->getParam('ot'));

		$squareMin = CHtml::encode(Yii::app()->request->getParam('square_min'));
		$squareMax = CHtml::encode(Yii::app()->request->getParam('square_max'));
		if($squareMin || $squareMax) {
			$this->squareCountMin = $squareMin;
			$this->squareCountMax = $squareMax;
		}

        $this->selectedCity = Yii::app()->request->getParam('city', array());
        if(isset($this->selectedCity[0]) && $this->selectedCity[0] == 0){
            $this->selectedCity = array();
        }

        if (issetModule('location')) {
            $country = CHtml::encode(Yii::app()->request->getParam('country'));
            if($country) {
                $this->selectedCountry = $country;
            }

            $region = CHtml::encode(Yii::app()->request->getParam('region'));
            if($region) {
                $this->selectedRegion = $region;
            }
        }

		if (issetModule('metroStations')) {
			$this->selectedMetroStations = Yii::app()->request->getParam('metro', array());
			if(isset($this->selectedMetroStations[0]) && $this->selectedMetroStations[0] == 0){
				$this->selectedMetroStations = array();
			}
		}

        $this->objType = CHtml::encode(Yii::app()->request->getParam('objType'));
        $this->apType = CHtml::encode(Yii::app()->request->getParam('apType'));


		$this->term = CHtml::encode(Yii::app()->request->getParam('term'));

        if(issetModule('formeditor')){
            $newFieldsAll = FormDesigner::getNewFields();
            foreach($newFieldsAll as $field){
                $value = CHtml::encode(Yii::app()->request->getParam($field->field));
                if(!$value){
                    continue;
                }
                $fieldString = $field->field;
                $this->newFields[$fieldString] = $value;
            }
        }

        $compact = CHtml::encode(Yii::app()->request->getParam('compact', 0));

        HAjax::jsonOk('', array(
            'html' => $this->renderPartial('//site/_search_form', array('isInner' => $isInner, 'compact' => $compact), true),
            'sliderRangeFields' => SearchForm::getSliderRangeFields(),
            'cityField' => SearchForm::getCityField(),
            'countFiled' => SearchForm::getCountFiled(),
            'compact' => $compact,
        ));
    }

}