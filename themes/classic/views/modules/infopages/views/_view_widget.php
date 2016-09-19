<?php
echo '<div class="clear"></div><div>';
$widgetData = array();

if ($model->widget == 'seosummaryinfo') {
	Yii::import('application.modules.seo.components.SeosummaryinfoWidget');
	Yii::import('application.modules.apartments.components.ApartmentsWidget');
}
else {
	Yii::import('application.modules.'.$model->widget.'.components.*');
}

switch($model->widget){
    case 'contactform':
        $widgetData = array('page' => 'index');
        break;
	
	case 'seosummaryinfo':
    case 'apartments':
        $widgetData = array('criteria' => $model->getCriteriaForAdList(), 'showWidgetTitle' => false);
        break;
	
	case 'entries':
        $widgetData = array('criteria' => $model->getCriteriaForEntriesList(), 'showWidgetTitle' => false);
        break;
}

if ($model->widget == 'seosummaryinfo') {
	$this->widget('SeosummaryinfoWidget');
	$this->widget('ApartmentsWidget', $widgetData);
}
else {
	$this->widget(ucfirst($model->widget).'Widget', $widgetData);
}

echo '</div>';
echo '<div class="clear"></div>';