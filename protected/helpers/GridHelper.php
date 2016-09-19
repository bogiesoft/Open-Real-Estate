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


class GridHelper
{
    public static function getSummary(Apartment $ad)
    {
        $html = '<div class="summary_info">';
        $html .= '<div class="span1 spanSummaryApImage">';
        $html .= Apartment::returnMainThumbForGrid($ad, 'thumbnail');
        $html .= '</div>';
        $html .= '<div class="span10">';
        $html .= '<div class="title">' . $ad->getTitle(). '</div>';

        $location = array();
        if (issetModule('location')) {
            if (isset($ad->locCountry) && $ad->locCountry) {
                $location[] = $ad->locCountry->getStrByLang('name');
            }
            //              if(isset($ad->locRegion) && $ad->locRegion) {
            //                    $location[] = $ad->locRegion->getStrByLang('name');
            //				}
            if (isset($ad->locCity) && $ad->locCity) {
                $location[] = $ad->locCity->getStrByLang('name');
            }
        } else {
            if (isset($ad->city) && $ad->city) {
                $location[] = $ad->city->getStrByLang('name');
            }
        }
        if ($ad->getAddress())
            $location[] = $ad->getAddress();

        $data = self::getColoredType($ad);
        if (isset($ad->objType) && $ad->objType) {
            $data .= ', ' . $ad->objType->getStrByLang('name');
        }
        if (!empty($location)) {
            $data .= ', ' . implode(', ', $location) . ', <strong>' . $ad->getPrettyPrice(false) . '</strong>';
        }

        $html .= '<div class="summary_info_row">' . $data . '</div>';

        $ownerData = (isset($ad->user) && $ad->user->role != User::ROLE_ADMIN) ? CHtml::link(CHtml::encode($ad->user->email), array("/users/backend/main/view", "id" => $ad->user->id)) : tt("administrator", "common");
        $data = tc('Owner') . ': ' . $ad->user->username . ' - ' . $ownerData;
        $data .= ', '.tc('Date created') . ': ' . HDate::formatDateTime($ad->date_created);
        $html .= '<div class="summary_info_row">' . $data . '</div>';

        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    public static function getColoredType(Apartment $ad)
    {
        $css = '';
        switch ($ad->type) {
            case Apartment::TYPE_SALE:
                $css = 'badge-info';
                break;

            case Apartment::TYPE_RENT;
                $css = 'badge-success';
                break;

            case Apartment::TYPE_BUY;
                $css = 'badge-';
                break;

            case Apartment::TYPE_CHANGE;
                $css = 'badge-danger';
                break;

            case Apartment::TYPE_RENTING;
                $css = 'badge-warning';
                break;
        }

        return '<span class="badge ' . $css . '">' . HApartment::getNameByType($ad->type) . '</span>';
    }
}