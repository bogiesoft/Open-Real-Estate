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


Yii::import('zii.widgets.grid.CGridView');

class NoBootstrapGridView extends CGridView {
	public $template="{summary}\n{pager}\n{items}\n{pager}";

	public function init() {
		$this->pager = array(
			'class'=>'itemPaginator'
		);

		if(Yii::app()->theme->name == 'atlas'){
			$this->pager = array(
				'class'=>'itemPaginatorAtlas',
				'header' => '',
				'selectedPageCssClass' => 'current',
				'htmlOptions' => array(
					'class' => ''
				)
			);

			$this->pagerCssClass = 'pagination';
		}
		
		parent::init();
		
		Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/common/js/stickytableheaders/jquery.stickytableheaders.js', CClientScript::POS_END);
		Yii::app()->clientScript->registerScript('sticky-table-headers-func-'.$this->getId(), '
			function attachStickyTableHeader() {
				$("#'.$this->getId().'").stickyTableHeaders();
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