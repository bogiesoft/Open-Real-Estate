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

class MainController extends ModuleAdminController{
	public $modelName = 'Lang';

	public function accessRules(){
		return array(
			array('allow',
				'expression'=> "Yii::app()->user->checkAccess('all_lang_and_currency_admin')",
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

    public function actionAdmin(){
   		$this->getMaxSorter();
		$this->getMinSorter();

		Yii::app()->user->setFlash('warning', Yii::t('module_lang','moduleAdminHelp',
			array('{link}'=>CHtml::link(tc('Currency'), array('/currency/backend/main/admin'))))
		);

   		parent::actionAdmin();
   	}

    public function actionIndex(){
        $this->redirect('admin');
    }

    public function actionView($id){
        $this->redirect('admin');
    }
/*
	public function actionCompare(){
		$sql = 'SELECT * FROM {{translate_message}} WHERE translation_de = ""';
		$result = Yii::app()->db->createCommand($sql)->queryAll();

		foreach ($result as $key=>$value) {
			$sql1 = 'SELECT translation_de FROM {{translate_message_de}} WHERE category = :category AND message = :message';
			$result1 = Yii::app()->db->createCommand($sql1)->queryScalar(array(':category'=>$value['category'], ':message'=>$value['message']));
			$result[$key]['translation_de'] = $result1;
			if (!$result1) {
				echo "<pre>";
				print_r($result[$key]);
				echo "</pre>";
			} else {
				$sql = 'UPDATE {{translate_message}} SET translation_de = :translation_de WHERE id="'.$value['id'].'"';
				Yii::app()->db->createCommand($sql)->execute(array(':translation_de' => $result1));
			}

		}


	}
*/
	public function actionSetDefault(){
        $id = (int) Yii::app()->request->getPost('id');
		$admin_mail = (int) Yii::app()->request->getPost('admin_mail');
        $model = Lang::model()->findByPk($id);
        $model->setDefault($admin_mail);

		# update {dbPrefix}menu - {baseUrl}
		$newDefaultNameISO = (isset($model->name_iso) && $model->name_iso) ? $model->name_iso : '';
		if ($newDefaultNameISO) {
			$sql = "SELECT id, name_iso FROM {{lang}}";
			$allLangs = Yii::app()->db->createCommand($sql)->queryAll();
			if (is_array($allLangs) && count($allLangs)) {
				$allLangs = CHtml::listData($allLangs, 'id', 'name_iso');

				$fields = $where  = array();
				foreach($allLangs as $lang) {
					$fields[] = 'href_'.$lang;
					$where[] = 'href_'.$lang.' LIKE "{baseUrl}%"';
				}

				if (count($fields)) {
					$sql = 'SELECT id, '.implode(', ', $fields).' FROM {{menu}} WHERE '.implode('OR ', $where).' GROUP BY id';
					$allRecords = Yii::app()->db->createCommand($sql)->queryAll();

					if (is_array($allRecords) && count($allRecords)) {
						foreach($allRecords as $record) {
							if (is_array($record) && isset($record['id'])) {
								$menuModel = Menu::model()->findByPk($record['id']);
								if ($menuModel) {
									$attr = $attrValues = array();
									foreach ($record as $itemKey => $itemValue) {
										if ($itemKey != 'id') {
											$attr[] = $itemKey;
											$attrValues[] = $itemValue;
										}
									}

									# delete all prefixes
									foreach($allLangs as $lang) {
										foreach($attrValues as &$attrVal) {
											$attrVal = preg_replace('/\/'.$lang.'\/\b/iU', '/', $attrVal);
										}
									}

									# add prefixes except new default lang
									$fullAttrToValues = array_combine($attr, $attrValues);

									$tmpAllLangs = $allLangs;
									if(($keyToDel = array_search($newDefaultNameISO, $tmpAllLangs)) !== false) {
										unset($tmpAllLangs[$keyToDel]);
									}

									foreach($tmpAllLangs as $lang) {
										if (array_key_exists('href_'.$lang, $fullAttrToValues)) {
											$value = $fullAttrToValues['href_'.$lang];
											$value = preg_replace('/{baseUrl}\/\b/i', '{baseUrl}/'.$lang.'/', $value);
											$fullAttrToValues['href_'.$lang] = $value;
										}
									}

									$menuModel->setAttributes($fullAttrToValues);
									$menuModel->update($attr);
								}
							}
						}
					}
				}
			}
		}


        Yii::app()->end();
    }

	public function actionActivate(){
		if(demo()){
            throw new CException(tc('Sorry, this action is not allowed on the demo server.'));
        }
		
        $id = (int) $_GET['id'];
        $action = $_GET['action'];
        if($id){
            $model = Lang::model()->findByPk($id);
            if(($model->main == 1 || $model->admin_mail == 1) && $action != 'activate'){
                Yii::app()->end();
            }
        }
        parent::actionActivate();
    }

	public function actionDelete($id) {
		if(demo()){
            throw new CException(tc('Sorry, this action is not allowed on the demo server.'));
        }
		
		parent::actionDelete($id);
	}
}
