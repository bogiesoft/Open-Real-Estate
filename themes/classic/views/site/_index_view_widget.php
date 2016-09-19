<?php
echo '<div class="clear"></div><div>';

	$widgetData = array();

	if ($widget == 'seosummaryinfo') {
		Yii::import('application.modules.seo.components.SeosummaryinfoWidget');
		Yii::import('application.modules.apartments.components.ApartmentsWidget');
	}
	else {
		Yii::import('application.modules.'.$widget.'.components.*');
	}

	switch($widget){
		case 'contactform':
			$widgetData = array('page' => 'index');
			break;

		case 'seosummaryinfo':
		case 'apartments':
			$criteria = $page->getCriteriaForAdList();
			$criteria = HGeo::setForIndexCriteria($criteria);

			$widgetData = array('criteria' => $criteria);
			break;

		case 'entries':
			$widgetData = array('criteria' => $page->getCriteriaForEntriesList());
			break;
	}

	if ($widget == 'seosummaryinfo') {
		$this->widget('SeosummaryinfoWidget');
		$this->widget('ApartmentsWidget', $widgetData);
	}
	else {
		$this->widget(ucfirst($widget).'Widget', $widgetData);
	}

echo '</div><div class="clear"></div>';