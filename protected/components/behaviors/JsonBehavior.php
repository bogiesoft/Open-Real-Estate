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

class JsonBehavior extends CActiveRecordBehavior{
    /** @var  array decoded data */
    private $cacheJson;

    public $jsonField = 'json_data';

    public function setInJson($key, $value, $save = false){
        $this->loadCache();
        $this->cacheJson[$key] = $value;
        if($save){
            return $this->saveJson();
        } else {
            $this->getOwner()->{$this->jsonField} = CJSON::encode($this->cacheJson);
        }
        return true;
    }

    public function getFromJson($key, $default = NULL){
        $this->loadCache();
        return isset($this->cacheJson[$key]) ? $this->cacheJson[$key] : $default;
    }

    public function deleteInJson($key, $save = true){
        $this->loadCache();
        if(isset($this->cacheJson[$key])){
            unset($this->cacheJson[$key]);
            if($save){
                $this->saveJson();
            }
        }
    }

    private function loadCache(){
        if(!$this->cacheJson){
            $this->cacheJson = $this->getOwner()->{$this->jsonField} ? CJSON::decode($this->getOwner()->{$this->jsonField}) : array();
        }
    }

    private function saveJson(){
        $this->getOwner()->{$this->jsonField} = CJSON::encode($this->cacheJson);
        if($this->getOwner()->save(true, array($this->jsonField))){
            return true;
        }
        //logs($this->getOwner()->errors);
        return false;
    }
}