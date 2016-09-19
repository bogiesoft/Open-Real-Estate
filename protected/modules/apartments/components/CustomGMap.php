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

class CustomGMap {
	private static $jsVars;
	private static $jsCode;

	public static function createMap($isAppartment = false, $scrollWheel = true, $draggable = true, $viewMany = false){
		$zoom = ($viewMany) ? param('module_apartments_gmapsZoomManyApartments', 11) : param('module_apartments_gmapsZoomApartment',15);

		self::$jsVars = '
		var mapGMap;
		var fenWayPanorama;
		var markersGMap = [];
		var markersForClasterGMap = [];
		var infoWindowsGMap = [];
		var latLngList = [];
		var markerClusterGMap;
		';

		self::$jsCode = '
		var initScrollWheel = "'.($scrollWheel).'";
		var initDraggable = "'.($draggable).'";
		var centerMapGMap = new google.maps.LatLng('.param('module_apartments_gmapsCenterY', 55.75411314653655).', '.param('module_apartments_gmapsCenterX', 37.620717508911184).');
			
		mapGMap = new google.maps.Map(document.getElementById("googleMap"), {
			zoom: '.$zoom.',
			center: centerMapGMap,
			mapTypeId: google.maps.MapTypeId.ROADMAP,
			maxZoom: 17,
			scrollwheel: initScrollWheel,
			draggable: initDraggable
		});
		';
	}

	public static function addMarker($model, $inMarker = null, $draggable = 'false', $return = false){
		if(is_object($model)) {
			$id = $model->id;
			$lat = $model->lat;
			$lng = $model->lng;
			$title = $model->getStrByLang('title');
			$iconFile = $model->getMapIconUrl();
		}
		elseif(is_array($model)) {
			$id = $model['id'];
			$lat = $model['lat'];
			$lng = $model['lng'];
			$title = $model['title_'.Yii::app()->language];
			$iconFile = ($model['objTypeIconFile']) ? Yii::app()->getBaseUrl().'/'.ApartmentObjType::model()->iconsMapPath.'/'.$model['objTypeIconFile'] : Yii::app()->theme->baseUrl."/images/house.png";
		}
		else return false;

		if($lat && $lng) {
			if (!$inMarker) {
				if(is_object($model)) {
					$id = $model->id;
					$title = $model->getStrByLang('title');
					$address = $model->getStrByLang('address');
					$url = $model->getUrl();
					$images = $model->images;
				}
				elseif(is_array($model)) {
					$id = $model['id'];
					$title = $model['title_'.Yii::app()->language];
					$address = $model['address_'.Yii::app()->language];
					$url = (isset($model['seoUrl']) && $model['seoUrl']) ? Yii::app()->createAbsoluteUrl('/apartments/main/view', array('url' => $model['seoUrl'] . (param('urlExtension') ? '.html' : ''))) : Yii::app()->createAbsoluteUrl('/apartments/main/view', array('id' => $id));
					$images = (isset($model['images'])) ? $model['images'] : null;
				}
				$res = Images::getMainThumb(150, 100, $images);
				$inMarker = '<div class="gmap-marker"><div align="center" class="gmap-marker-adlink">';
				$inMarker .= CHtml::link('<strong>'.tt("ID", "apartments").': '.$id.'</strong>, '.CHtml::encode($title), $url);
				$inMarker .= '</div><div align="center" class="gmap-marker-img">';
				$inMarker .= CHtml::image($res['thumbUrl'], $title, array('title' => $title)).'</div>';
				$inMarker .= '<div align="center" class="gmap-marker-adress">';
				$inMarker .= CHtml::encode($address).'</div></div>';
			}

			if ($return) {
				return array(
					'id' => $id,
					'lat' => $lat,
					'lng' => $lng,
					'title' => $title,
					'address' => $address,
					'url' => $url,
					'iconFile' => $iconFile,
					'content' => $inMarker,
					'draggable' => false,
				);
			}

			self::$jsCode .= '
				var latLng'.$id.' = new google.maps.LatLng('.$lat.', '.$lng.');
				latLngList.push(latLng'.$id.');
				markersGMap['.$id.'] = new google.maps.Marker({
					position: latLng'.$id.',
					title: "'.CJavaScript::quote($title).'",
					content: "'.CJavaScript::quote($inMarker).'",
					icon: "'.$iconFile.'",
					map: mapGMap,
					draggable: '.$draggable.'
				});
				markersForClasterGMap.push(markersGMap['.$id.']);
				infoWindowsGMap['.$id.'] = new google.maps.InfoWindow({
					content: "'.CJavaScript::quote($inMarker).'"
				});

				var infowindow = null;

				google.maps.event.addListener(markersGMap['.$id.'], "click", function() {
					if (infowindow) {
						infowindow.close();
					}

					infoWindowsGMap['.$id.'].open(mapGMap, markersGMap['.$id.']);
					infowindow = infoWindowsGMap['.$id.'];
				});
			';
		}
	}

	public static function clusterMarkers(){
		self::$jsCode .= 'var mcOptions = {'
				. 'zoomOnClick:false, '
				. 'maxZoom: 16, '
				. 'gridSize: 50, '
				. 'styles: [{
					height: 53,
					url: "'.Yii::app()->getBaseUrl().'/images/maps/m1.png",
					width: 53
					},
					{
					height: 56,
					url: "'.Yii::app()->getBaseUrl().'/images/maps/m2.png",
					width: 56
					},
					{
					height: 66,
					url: "'.Yii::app()->getBaseUrl().'/images/maps/m3.png",
					width: 66
					},
					{
					height: 78,
					url: "'.Yii::app()->getBaseUrl().'/images/maps/m4.png",
					width: 78
					},
					{
					height: 90,
					url: "'.Yii::app()->getBaseUrl().'/images/maps/m5.png",
					width: 90
				}]'
				. '};';
		self::$jsCode .= 'markerClusterGMap = new MarkerClusterer(mapGMap, markersForClasterGMap, mcOptions);';

		self::$jsCode .= '
			google.maps.event.addListener(markerClusterGMap, "clusterclick", function (cluster, $event) {
    			var newCenter = cluster.getCenter();
				var newCenterLat = newCenter.lat();
				var newCenterLng = newCenter.lng();
				var currentZoom = mapGMap.getZoom();

				mapGMap.panTo(new google.maps.LatLng(newCenterLat,newCenterLng));

				if(currentZoom < 16) {
					mapGMap.setZoom(currentZoom+1);
				}
				else {
					var markers = cluster.getMarkers();
					if (markers.length != 0) {
						var content = "<div class=\'gmap-marker-clusterer-infowindow\'>";

						$.each(markers, function(x, marker) {
							content = content + "<br />" + marker.content;
						});
						content = content + "</div>";

						var info = new google.maps.MVCObject;
    					info.set("position", cluster.center_);

						var infowindow = new google.maps.InfoWindow();
						infowindow.close();
						//infowindow.setPosition(newCenter);
						infowindow.setContent(content);
						infowindow.open(mapGMap, info);
						}
					}
				});
		';

		//self::$jsCode .= 'markerClusterGMap = new MarkerClusterer(mapGMap, markersForClasterGMap);';
	}

	public static function setCenter(){
		self::$jsCode .= '
			if(latLngList.length > 0){
				var bounds = new google.maps.LatLngBounds ();
				for (var i = 0, LtLgLen = latLngList.length; i < LtLgLen; i++) {
					bounds.extend (latLngList[i]);
				}
				mapGMap.fitBounds(bounds);
			}
		';
	}

	public static function render(){
		echo CHtml::tag('div', array('id' => 'googleMap'), '', true);
		echo CHtml::tag('div', array('class' => 'clear'), '', true);
		echo CHtml::tag('div', array('id' => 'mapWarningBox', 'style' => 'display:none;'), tc('Please zoom in.'), true);

		$js1 = 'https://maps.google.com/maps/api/js?v=3&key='.param('googleMapApiKey').'&callback=initGmap&language='.Yii::app()->language;
		self::$jsVars .= "\n loadScript('$js1', true);\n";

		echo CHtml::script(PHP_EOL . self::$jsVars . PHP_EOL . 'function initGmap() { ' . self::$jsCode . ' }');
	}


	public static function actionGmap($id, $model, $inMarker, $withPanorama = false){

		$isOwner = self::isOwner($model);

		// If we have already created marker - show it
		if (($model->lat && $model->lng) ||
			!param('module_apartments_gmapsCenterY', 37.620717508911184) ||
			!param('module_apartments_gmapsCenterX', 55.75411314653655)) {

			self::createMap(true);
			self::$jsCode .= '
				mapGMap.setCenter(new google.maps.LatLng('.$model->lat.', '.$model->lng.'));
			';

			$draggable = $isOwner ? 'true' : 'false';

			self::addMarker($model, $inMarker, $draggable);

			if($isOwner){
				self::$jsCode .= '
					google.maps.event.addListener(markersGMap['.$model->id.'], "dragend", function (event) { $.ajax({
						type: "POST",
						url:"'.Yii::app()->controller->createUrl('savecoords', array('id' => $model->id) ).'",
						data: ({"lat": event.latLng.lat(), "lng": event.latLng.lng()}),
						cache:false
					}); });
				';
			}
		} else {
			if(!$isOwner){
				return '';
			}

			$model->lat = param('module_apartments_gmapsCenterY', 37.620717508911184);
			$model->lng = param('module_apartments_gmapsCenterX', 55.75411314653655);


			self::actionGmap($id, $model, $inMarker);
			return false;
		}

		if($withPanorama){
			self::$jsCode .= '
					var fenWayPanorama = new google.maps.LatLng('.$model->lat.', '.$model->lng.');
					if (($("#gmap-panorama").length > 0)) {
						var streetViewService = new google.maps.StreetViewService();
						streetViewService.getPanoramaByLocation(fenWayPanorama, 30, function (streetViewPanoramaData, status) {
							if (status === google.maps.StreetViewStatus.OK) {
								$("#gmap-panorama").show().css("visibility", "visible");
								google.maps.event.addDomListener(window, "load", initializeGmapPanorama);
							} else {
								$("#gmap-panorama").hide().css("visibility", "hidden");
							}
						});
					}
			';
		}

		self::render();
	}

	public static function setLazyLoadListeners() {
		self::$jsCode .= '			
			var fetchedAreasBounds;
			var jqXHR;
			var markersGMap = [];
			var infoWindowsGMap = [];
			var mcOptions = {zoomOnClick:false, maxZoom: 16, gridSize: 50, styles: [{
					height: 53,
					url: "'.Yii::app()->getBaseUrl().'/images/maps/m1.png",
					width: 53
					},
					{
					height: 56,
					url: "'.Yii::app()->getBaseUrl().'/images/maps/m2.png",
					width: 56
					},
					{
					height: 66,
					url: "'.Yii::app()->getBaseUrl().'/images/maps/m3.png",
					width: 66
					},
					{
					height: 78,
					url: "'.Yii::app()->getBaseUrl().'/images/maps/m4.png",
					width: 78
					},
					{
					height: 90,
					url: "'.Yii::app()->getBaseUrl().'/images/maps/m5.png",
					width: 90
				}]};
			var markerClusterGMap = new MarkerClusterer(mapGMap, null, mcOptions);
			
			function gMapRefreshPointsIfNessecary(){
                var mapBounds = mapGMap.getBounds();
                var sw = mapBounds.getSouthWest();
                var ne = mapBounds.getNorthEast();
                if(!fetchedAreasBounds 
                    || !fetchedAreasBounds.contains(sw)
                    || !fetchedAreasBounds.contains(ne)
                    ){
                    //get available areas to display on map
                    gMapFetch({
                        southWest: {
                            lat: sw.lat(),
                            lng: sw.lng()
                        },
                        northEast: {
                            lat: ne.lat(),
                            lng: ne.lng()
                        }
                    });
                }
            }
			
            function gMapOnBoundsChanged(){
				google.maps.event.clearListeners(mapGMap, "bounds_changed");
				gMapRefreshPointsIfNessecary();
            }
			
            function gMapFetch(data){			
				data.filterPriceType = "'.(int) Yii::app()->request->getParam('filterPriceType').'";
				data.filterObjType = "'.(int) Yii::app()->request->getParam('filterObjType').'";
				
                if(jqXHR){
                    jqXHR.abort();
                    jqXHR = null;
                }
                jqXHR = $.post(
					"'.Yii::app()->controller->createUrl('/site/getMarkersViewAllMap', array(Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken)).'",
					data,
					gMapOnDataFetched
				);
            }
			
            function gMapOnDataFetched(data){	
				var jsonAnswer = $.parseJSON(data);
								
                if(jsonAnswer.needZoom){
					$("#mapWarningBox").show();
				}
				else {
					$("#mapWarningBox").hide();
					
					if(jsonAnswer.markers) {
						markerClusterGMap.clearMarkers();
						
						$.each( jsonAnswer.markers, function( key, value ) {	
							if (typeof markersGMap[value.id] == "undefined") {
								var newMarkerLatLng = new google.maps.LatLng(value.lat, value.lng);
								markersGMap[value.id] = new google.maps.Marker({
									position: newMarkerLatLng,
									title: value.title,
									content: value.content,
									icon: value.iconFile,
									map: mapGMap,
									draggable: false
								});

								infoWindowsGMap[value.id] = new google.maps.InfoWindow({
									content: value.content
								});

								var infowindow = null;

								google.maps.event.addListener(markersGMap[value.id], "click", function() {
									if (infowindow) {
										infowindow.close();
									}

									infoWindowsGMap[value.id].open(mapGMap, markersGMap[value.id]);
									infowindow = infoWindowsGMap[value.id];
								});
							}
						});
						
						markerClusterGMap.addMarkers(markersGMap);
					}
				}
				
				/*var mapBounds = mapGMap.getBounds();
				var sw = mapBounds.getSouthWest();
				var ne = mapBounds.getNorthEast();
				fetchedAreasBounds = new google.maps.LatLngBounds(
					new google.maps.LatLng(sw.lat(), sw.lng()),
					new google.maps.LatLng(ne.lat(), ne.lng())
				);*/
            }     

			google.maps.event.addListener(mapGMap, "bounds_changed", function() {
				gMapOnBoundsChanged();
			});

			google.maps.event.addListener(mapGMap, "dragend", function() {
				gMapRefreshPointsIfNessecary();
			});

			google.maps.event.addListener(mapGMap, "zoom_changed", function() {
				gMapRefreshPointsIfNessecary();
			});
			
			google.maps.event.addListener(markerClusterGMap, "clusterclick", function (cluster, $event) {
				var newCenter = cluster.getCenter();
				var newCenterLat = newCenter.lat();
				var newCenterLng = newCenter.lng();
				var currentZoom = mapGMap.getZoom();

				mapGMap.panTo(new google.maps.LatLng(newCenterLat,newCenterLng));

				if(currentZoom < 16) {
					mapGMap.setZoom(currentZoom+1);
				}
				else {
					var markers = cluster.getMarkers();
					if (markers.length != 0) {
						var content = "<div class=\'gmap-marker-clusterer-infowindow\'>";

						$.each(markers, function(x, marker) {
							content = content + "<br />" + marker.content;
						});
						content = content + "</div>";

						var info = new google.maps.MVCObject;
						info.set("position", cluster.center_);

						var infowindow = new google.maps.InfoWindow();
						infowindow.close();
						//infowindow.setPosition(newCenter);
						infowindow.setContent(content);
						infowindow.open(mapGMap, info);
						}
					}
			});
		';
	}

	private static function isOwner($model){
		return Yii::app()->user->checkAccess('backend_access') || param('useUserads', 1) && !Yii::app()->user->isGuest && $model->isOwner();
	}
}