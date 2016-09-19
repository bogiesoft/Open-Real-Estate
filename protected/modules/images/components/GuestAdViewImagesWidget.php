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

class GuestAdViewImagesWidget extends CWidget {
	public $sessionId;
	public $guestAdImages;

	public function getViewPath($checkTheme=true){
		return Yii::getPathOfAlias('application.modules.images.views');
	}

	public function run() {
		$this->registerAssets();
		
		if (!$this->guestAdImages) {
			$filePathName = 'temp__'.$this->sessionId;

			if (is_dir(Yii::getPathOfAlias('webroot.uploads.guestad.'.$filePathName))) {
				$files = getFilesNameArrayInPathWithoutHtml(Yii::getPathOfAlias('webroot.uploads.guestad.'.$filePathName.'.'.Images::ORIGINAL_IMG_DIR));

				if (count($files)) {
					$guestAdImages = array();

					foreach($files as $file) {
						$fileNameExplode = explode('__', $file);

						$model = new Images;
						$model->id = $fileNameExplode[0];
						$model->id_object = 0;
						$model->id_owner = $this->sessionId;
						$model->file_name = $file;
						$model->sorter = $fileNameExplode[0];

						$this->guestAdImages[] = $model;
					}
				}
			}
		}

		$this->render('widgetGuestAdViewImages', array(
			'guestAdImages' => $this->guestAdImages,
		));
	}
	
	public function registerAssets(){
		$assets = dirname(__FILE__).'/../assets';
		$baseUrl = Yii::app()->assetManager->publish($assets);

		if(is_dir($assets)){
			Yii::app()->clientScript->registerCssFile($baseUrl . '/styles.css');
		} else {
			throw new Exception('Image - Error: Couldn\'t find assets folder to publish.');
		}
	}
}