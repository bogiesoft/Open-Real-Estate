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

Yii::import('ext.groupgridview.BootGroupGridView');

class CustomBootStrapGroupGridView extends BootGroupGridView {
	//public $pager = array('class'=>'objectPaginator');
	public $template = "{summary}\n{pager}\n{items}\n{pager}";

	//public $extraRowColumns = array('reference_category_id');
	public $mergeType = 'nested';

	public $type = 'striped bordered condensed';

	public $pager = array('class'=>'bootstrap.widgets.TbPager', 'displayFirstAndLast' => true);
	
	public function init() {
		parent::init();
		
		Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/common/js/stickytableheaders/jquery.stickytableheaders.js', CClientScript::POS_END);
		
		Yii::app()->clientScript->registerScript('sticky-table-headers-func-'.$this->getId(), '
			function attachStickyTableHeader() {
				if( $(".navbar-fixed-top").css("position").toLowerCase() == "fixed") {
					$("#'.$this->getId().'").stickyTableHeaders({fixedOffset: $(".navbar-fixed-top")});
				}
				else {
					$("#'.$this->getId().'").stickyTableHeaders({fixedOffset: 0});
				}
			}
		', CClientScript::POS_END);
		
		Yii::app()->clientScript->registerScript('call-sticky-table-headers-'.$this->getId(), '
			attachStickyTableHeader();
			
			$(window).bind("load", attachStickyTableHeader);
			$(window).bind("resize", attachStickyTableHeader);
			$(window).bind("orientationchange", attachStickyTableHeader);
		', CClientScript::POS_READY);
	}
}