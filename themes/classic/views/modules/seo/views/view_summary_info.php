<?php
$this->pageTitle .= ' - '.$cityModel->getStrByLang('name');

$widgetTitle = (!empty($widgetTitle)) ? $widgetTitle : $this->pageTitle; 

if ($cityModel && $objTypeModel) {
	$cityName = (!empty($seoCity)) ? $seoCity->getStrByLang('title') : $cityModel->getStrByLang('name');
	$this->breadcrumbs=array(
		$cityName => Yii::app()->controller->createUrl('/seo/main/viewsummaryinfo', array('cityUrlName' => $cityUrlName)),
		$widgetTitle,
	);
}
else {
	$this->breadcrumbs=array(
		$widgetTitle,
	);
}
?>

<?php if ($bodyText):?>
<div class="full-city-summary-info">
	<?php echo $bodyText;?>
</div>
<div class="clear"></div>
<?php endif;?>

<?php
$this->widget('application.modules.apartments.components.ApartmentsWidget', array(
	'criteria' => $criteria,
	'count' => null,
	'showCount' => false,
	'widgetTitle' => $widgetTitle,
));