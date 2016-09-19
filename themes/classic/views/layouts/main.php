<!DOCTYPE html>
<?php
/*$nameRFC3066 = 'ru-ru';
$allLangs = Lang::getActiveLangs(true);
if ($allLangs) {
	$nameRFC3066 = (array_key_exists(Yii::app()->language, $allLangs) && array_key_exists('name_rfc3066', $allLangs[Yii::app()->language])) ? $allLangs[Yii::app()->language]['name_rfc3066'] : 'ru-ru';
}
$nameRFC3066 = utf8_strtolower($nameRFC3066);
*/
$cs = Yii::app()->clientScript;
$baseUrl = Yii::app()->baseUrl;
$baseThemeUrl = Yii::app()->theme->baseUrl;
?>

<html lang="<?php echo Yii::app()->language;?>">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

	<title><?php echo CHtml::encode($this->seoTitle ? $this->seoTitle : $this->pageTitle); ?></title>
	<meta name="description" content="<?php echo CHtml::encode($this->seoDescription ? $this->seoDescription : $this->pageDescription); ?>" />
	<meta name="keywords" content="<?php echo CHtml::encode($this->seoKeywords ? $this->seoKeywords : $this->pageKeywords); ?>" />
	<link href='https://fonts.googleapis.com/css?family=Roboto:400,300,700,500&subset=latin,cyrillic-ext,greek-ext,greek,vietnamese,latin-ext,cyrillic' rel='stylesheet' type='text/css'>

	<link rel="stylesheet" type="text/css" href="<?php echo $baseThemeUrl; ?>/css/screen.css<?php echo (demo()) ? '?v='.ORE_VERSION: '';?>" media="screen" />
	<link rel="stylesheet" type="text/css" href="<?php echo $baseThemeUrl; ?>/css/print.css<?php echo (demo()) ? '?v='.ORE_VERSION: '';?>" media="print" />
	<!--<link rel="stylesheet" type="text/css" href="<?php echo $baseThemeUrl; ?>/css/form.css" />-->
	<link rel="stylesheet" type="text/css" href="<?php echo $baseThemeUrl; ?>/css/styles.css<?php echo (demo()) ? '?v='.ORE_VERSION: '';?>" media="screen"  />

	<!--[if IE]> <link href="<?php echo $baseThemeUrl; ?>/css/ie.css" rel="stylesheet" type="text/css"> <![endif]-->

	<link rel="icon" href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?php echo Yii::app()->request->getBaseUrl(true); ?>/favicon.ico" type="image/x-icon" />

	<?php
    HSite::registerMainAssets();

	if(Yii::app()->user->checkAccess('backend_access')){
		?><link rel="stylesheet" type="text/css" href="<?php echo $baseThemeUrl; ?>/css/tooltip/tipTip.css" /><?php
	}
	?>
</head>

<body>
	<?php if (demo()) :?>
		<?php $this->renderPartial('//site/ads-block', array()); ?>
	<?php endif; ?>

	<div id="container" <?php echo (demo()) ? 'style="padding-top: 40px;"' : '';?> >
		<noscript><div class="noscript"><?php echo Yii::t('common', 'Allow javascript in your browser for comfortable use site.'); ?></div></noscript>
		<div class="logo">
			<a title="<?php echo Yii::t('common', 'Go to main page'); ?>" href="<?php echo Yii::app()->controller->createAbsoluteUrl('/'); ?>">
				<div class="logo-img"> <img width="77" height="70" alt="" src="<?php echo Yii::app()->theme->baseUrl; ?>/images/pages/logo-open-ore.png" /></div>
				<div class="logo-text"><?php echo CHtml::encode(Yii::app()->name);?></div>
			</a>
		</div>

		<?php
		if(!isFree()){
			$languages = Lang::getActiveLangs(true);
			if(count($languages) > 1){
				$this->widget('application.modules.lang.components.langSelectorWidget', array( 'type' => 'links', 'languages' => $languages ));
			}
			if(issetModule('currency') && count(Currency::getActiveCurrency()) >1){
				$this->widget('application.modules.currency.components.currencySelectorWidget');
			}
		}
		?>

		<div id="user-cpanel"  class="menu_item">
			<?php
				$this->widget('zii.widgets.CMenu',array(
					'id' => 'nav',
					'items'=>$this->aData['userCpanelItems'],
					'htmlOptions' => array('class' => 'dropDownNav'),
				));
			?>
		</div>

		<div id="search" class="menu_item">
			<?php
			if (param('useYandexShare', 0))
				$this->widget('application.extensions.YandexShareApi', array(
					'services' => param('yaShareServices', 'yazakladki,moikrug,linkedin,vkontakte,facebook,twitter,odnoklassniki')
				));
			if (param('useInternalShare', 1))
				$this->widget('ext.sharebox.EShareBox', array(
					'url' => Yii::app()->getRequest()->getHostInfo().Yii::app()->request->url,
					'title'=> CHtml::encode($this->seoTitle ? $this->seoTitle : $this->pageTitle),
					'iconSize' => 16,
					'include' => explode(',', param('intenalServices', 'vk,facebook,twitter,google-plus,stumbleupon,digg,delicious,linkedin,reddit,technorati,entriesvine')),
				));

				/*$this->widget('zii.widgets.CMenu',array(
					'id' => 'dropDownNav',
					'items'=>$this->aData['topMenuItems'],
					'htmlOptions' => array('class' => 'dropDownNav'),
				));*/

				$this->widget('CustomMenu',array(
					'id' => 'sf-menu-id',
					'items' => $this->aData['topMenuItems'],
					'htmlOptions' => array('class' => 'sf-menu'),
					'encodeLabel' => false,
					'activateParents' => true,
				));
			?>
		</div>

		<div class="content">
			<?php echo $content; ?>
			<div class="clear"></div>
		</div>

		<?php
			if(issetModule('advertising')) {
				$this->renderPartial('//modules/advertising/views/advert-bottom');
			}
		?>

		<div class="footer">
			<?php echo getGA(); ?>
			<?php echo getJivo(); ?>
			<p class="slogan">&copy;&nbsp;<?php echo CHtml::encode(Yii::app()->name).', '.date('Y'); ?></p>
			<!-- <?php echo param('version_name').' '.param('version'); ?> -->
		</div>
	</div>

	<div id="loading" style="display:none;"><?php echo Yii::t('common', 'Loading content...'); ?></div>
	<div id="loading-blocks" style="display:none;"></div>
	<div id="overlay-content" style="display:none;"></div>
	<?php
    $cs->registerScript('main-vars', '
		var BASE_URL = '.CJavaScript::encode(Yii::app()->baseUrl).';
        var CHANGE_SEARCH_URL = '.CJavaScript::encode(Yii::app()->createUrl('/quicksearch/main/mainsearch/countAjax/1')).';
		var INDICATOR = "'. Yii::app()->theme->baseUrl . "/images/pages/indicator.gif".'";
		var LOADING_NAME = "'. tc('Loading ...').'";
		var params = {
			change_search_ajax: '.param("change_search_ajax", 1).'
		}
	', CClientScript::POS_HEAD, array(), true);

    $this->renderPartial('//layouts/_common');

	$this->widget('application.modules.fancybox.EFancyBox', array(
			'target'=>'a.fancy',
			'config'=>array(
				'ajax' => array('data'=>"isFancy=true"),
				'titlePosition' => 'inside',
				'onClosed' => 'js:function(){
					var capClick = $(".get-new-ver-code");
					if(typeof capClick !== "undefined")	{ 
						capClick.click(); 
					}
				}'
			),
		)
	);

	/*$this->widget('ext.magnific-popup.EMagnificPopup', array(
		'target'=>'a.fancy',
		'type' => 'image',
		'options' => array(
			'closeOnContentClick' => true,
			'mainClass' => 'mfp-img-mobile',
			'callbacks' => array(
				'close' => 'js:function(){
					var capClick = $(".get-new-ver-code");
					if(typeof capClick !== "undefined")	capClick.click();
				}
				',
			),
		),
	));

	$this->widget('ext.magnific-popup.EMagnificPopup', array(
			'target'=>'.mgp-open-inline',
			'type' => 'inline',
			'options' => array(
				'preloader' => false,
				'focus' => '#name',
				'callbacks' => array(
					'beforeOpen' => 'js:function() {
						if($(window).width() < 700) {
						  this.st.focus = false;
						} else {
						  this.st.focus = "#name";
						}
					  }
					',
					'close' => 'js:function(){
						var capClick = $(".get-new-ver-code");
						if(typeof capClick !== "undefined")	capClick.click();
					}
					',
				),
			),
		)
	);

	$this->widget('ext.magnific-popup.EMagnificPopup', array(
			'target'=>'.mgp-open-ajax',
			'type' => 'ajax',
			'options' => array(
				'preloader' => false,
				'focus' => '#name',
				'callbacks' => array(
					'beforeOpen' => 'js:function() {
						if($(window).width() < 700) {
						  this.st.focus = false;
						} else {
						  this.st.focus = "#name";
						}
					  }
					',
					'close' => 'js:function(){
						var capClick = $(".get-new-ver-code");
						if(typeof capClick !== "undefined")	capClick.click();
					}
					',
				),
			),
		)
	);*/

	if(Yii::app()->user->checkAccess('apartments_admin')){
		$cs->registerScriptFile($baseThemeUrl.'/js/tooltip/jquery.tipTip.js', CClientScript::POS_HEAD);
		$cs->registerScript('adminMenuToolTip', '
			$(function(){
				$(".adminMainNavItem").tipTip({maxWidth: "auto", edgeOffset: 10, delay: 200});
			});
		', CClientScript::POS_READY);
		?>

		<div class="admin-menu-small <?php echo demo() ? 'admin-menu-small-demo' : '';?> ">
			<a href="<?php echo $baseUrl; ?>/apartments/backend/main/admin">
				<img src="<?php echo $baseThemeUrl; ?>/images/adminmenu/administrator.png" alt="<?php echo Yii::t('common','Administration'); ?>" title="<?php echo Yii::t('common','Administration'); ?>" class="adminMainNavItem" />
			</a>
		</div>
	<?php } ?>
	
	<?php		
		if (param('useShowInfoUseCookie') && isset(Yii::app()->controller->privatePolicyPage) && !empty(Yii::app()->controller->privatePolicyPage)) {
			$privatePolicyPage = Yii::app()->controller->privatePolicyPage;
			$cs->registerScript('display-info-use-cookie-policy', '
				$.cookieBar({/*acceptOnContinue:false, */ fixed: true, bottom: true, message: "'.  CHtml::encode(Yii::app()->name).' '.CHtml::encode(tc('uses cookie')).', <a href=\"'.$privatePolicyPage->getUrl().'\" target=\'_blank\'>'.$privatePolicyPage->getStrByLang('title').'</a>", acceptText : "X"});
			', CClientScript::POS_READY);
		}
	?>
</body>
</html>