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

class Booking extends CFormModel {
	public $apartment_id;
	public $user_id;
	public $date_start;
	public $date_end;
	public $time_in;
	public $time_out;
	public $status;
	public $username;
	public $comment;
	public $useremail;
	public $useremailSearch;
	public $tostatus;
	public $ownerEmail;
	public $phone;
	public $email;
	public $dateCreated;
	public $password;
	public $time_inVal;
	public $time_outVal;
	public $activatekey;
	public $activateLink;
	public $verifyCode;
    public $type;
	public $user_ip;
	public $user_ip_ip2_long;
	public $amount;
	public $num_guest;

	const TIME_BEFORE_NOON = 1;
	const TIME_AFTER_NOON = 2;

	public static function getYiiDateFormat() {
		$return = 'MM/dd/yyyy';
		if (Yii::app()->language == 'ru') {
			$return = 'dd.MM.yyyy';
		}
		return $return;
	}

	public function rules() {
		return array(
			array('date_start, date_end, time_in, time_out, ' . (Yii::app()->user->isGuest ? 'useremail, phone, username' : ''), 'required', 'on' => 'bookingform'),
			array('status, time_in, time_out, amount', 'numerical', 'integerOnly' => true),
			array('num_guest', 'numerical', 'integerOnly' => true, 'min' => 1, 'max' => param('booking_max_guest', 10)),
			array('useremail, username, comment, phone','filter','filter'=>array(new CHtmlPurifier(),'purify')),
			array('useremail', 'email'),
			array('date_start, date_end', 'date', 'format' => self::getYiiDateFormat(), 'on' => 'bookingform'),
			array('date_start', 'myDateValidator', 'on' => 'bookingform'),
			array('useremail', 'myUserEmailValidator', 'on' => 'bookingform'),
			array('useremail, username', 'length', 'max' => 128),
			array('comment', 'length', 'max' => 1024),
			array('phone', 'required'),
			array('date_start, date_end, date_created, status, useremailSearch, apartment_id, id', 'safe', 'on' => 'search'),

			array('verifyCode', (Yii::app()->user->isGuest) ? 'required' : 'safe'),
			array('verifyCode', 'captcha', 'allowEmpty'=> !(Yii::app()->user->isGuest)),
			array('user_ip, user_ip_ip2_long', 'length', 'max' => 60),
		);
	}

	public function myUserEmailValidator() {
		if (Yii::app()->user->isGuest && param('useUserRegistration', 1)) {
			$model = User::model()->findByAttributes(array('email' => $this->useremail));
			if ($model) {
				$this->addError('useremail',
					Yii::t('module_booking', 'User with such e-mail already registered. Please <a title="Login" href="{n}">login</a> and try again.',
						Yii::app()->createUrl('/site/login')));
			}
		}
	}

	public function myDateValidator($param) {
		$dateStart = CDateTimeParser::parse($this->date_start, self::getYiiDateFormat()); // format to unix timestamp
		$dateEnd = CDateTimeParser::parse($this->date_end, self::getYiiDateFormat()); // format to unix timestamp

		if ($param == 'date_start' && $dateStart < CDateTimeParser::parse(date('Y-m-d'), 'yyyy-MM-dd')) {
			$this->addError('date_start', tt('Wrong check-in date', 'booking'));
		}
		if ($param == 'date_end' && $dateEnd <= $dateStart) {
			$this->addError('date_end', tt('Wrong check-out date', 'booking'));
		}

		if(issetModule('bookingcalendar')) {
			$result = Yii::app()->db->createCommand()
				->select('id')
				->from('{{booking_calendar}}')
				->where('apartment_id = "'.$this->apartment_id.'" AND status = "'.Bookingcalendar::STATUS_BUSY.'" AND
						UNIX_TIMESTAMP(date_start) > "'.$dateStart.'" AND UNIX_TIMESTAMP(date_end) < "'.$dateEnd.'"')
				->queryScalar();

			if ($param == 'date_start' && $result) {
				$this->addError('date', tt('You chose dates in the range of which there are busy days', 'bookingcalendar'));
			}

			if(!param('booking_half_day')){
				return;
			}

			$dateStart = date('Y-m-d', $dateStart);
			$dateEnd = date('Y-m-d', $dateEnd);

			$result = Yii::app()->db->createCommand()
				->select('t.id, b.time_in, b.time_out, t.date_start, t.date_end')
				->from('{{booking_calendar}} AS t')
				->leftJoin('{{booking_table}} AS b', 't.booking_id = b.id')
				->where('t.apartment_id = "'.$this->apartment_id.'" AND t.status = "'.Bookingcalendar::STATUS_BUSY.'" AND
						(t.date_start = "'.$dateStart.'" OR t.date_end = "'.$dateEnd.'" OR
						 t.date_start = "'.$dateEnd.'" OR t.date_end = "'.$dateStart.'")
						')
				->queryAll();

			if ($result) {
				foreach($result as $row){
					if($row['time_out'] == Booking::TIME_BEFORE_NOON && $row['date_end'] == $dateStart && $row['time_out'] == $this->time_in){
						$this->addError('time_in', tt('Available Check-in Time for this date is only in the afternoon', 'bookingcalendar'));
					} elseif($row['date_end'] == $dateStart && $row['time_out'] == $this->time_in) {
						$this->addError('date_start', tt('Date you have chosen is unavailable', 'bookingcalendar'));
					}

					if($row['time_in'] == Booking::TIME_AFTER_NOON && $row['date_start'] == $dateEnd && $row['time_in'] == $this->time_out){
						$this->addError('time_out', tt('Available Check-Out Time for this date is only in the forenoon', 'bookingcalendar'));
					} elseif($row['date_start'] == $dateEnd && $row['time_in'] == $this->time_out){
						$this->addError('date_end', tt('Date you have chosen is unavailable', 'bookingcalendar'));
					}

					if($row['date_end'] == $dateEnd && $row['date_start'] == $dateStart){
						$this->addError('date', tt('Dates you have chosen are unavailable', 'bookingcalendar'));
					}
				}
			}
		}
	}

	public function attributeLabels() {
		return array(
			'date_start' => tt('Check-in date', 'booking'),
			'date_end' => tt('Check-out date', 'booking'),
			'email' => Yii::t('common', 'E-mail'),
			'time_in' => tt('Check-in time', 'booking'),
			'time_out' => tt('Check-out time', 'booking'),
			'comment' => tt('Comment', 'booking'),
			'username' => tt('Your name', 'booking'),
			'status' => tt('Status', 'booking'),
			'useremail' => Yii::t('common', 'E-mail'),
			'useremailSearch' => tt('User e-mail', 'booking'),
			'sum' => tt('Booking price', 'booking'),
			'date_created' => tt('Creation date', 'booking'),
			'dateCreated' => tt('Creation date', 'booking'),
			'apartment_id' => tt('Apartment ID', 'booking'),
			'id' => tt('ID', 'apartments'),
			'phone' => Yii::t('common', 'Your phone number'),
			'verifyCode' => tc('Verify Code'),
			'num_guest' => tt('Number of guests', 'booking'),
		);
	}

	public static function getDate($mysqlDate, $full = 0) {
		if (!$full) {
			$date = CDateTimeParser::parse($mysqlDate, 'yyyy-MM-dd');
		}
		else {
			$date = CDateTimeParser::parse($mysqlDate, 'yyyy-MM-dd hh:mm:ss');
		}
		return Yii::app()->dateFormatter->format(self::getYiiDateFormat(), $date);
	}

	public static function getJsDateFormat() {
		$dateFormat = 'dd.mm.yy';
		if (Yii::app()->language != 'ru') {
			$dateFormat = 'mm/dd/yy';
		}
		return $dateFormat;
	}

    public $city_id;

    public function getCityName(){
        if(issetModule('location')){
            $city = City::model()->findByPk($this->city_id);
        } else {
            $city = ApartmentCity::model()->findByPk($this->city_id);
        }
        return $city->getStrByLang('name');
    }

	public static function getNumGuestList()
	{
		return array_combine(range(1, param('booking_max_guest', 10)), range(1, param('booking_max_guest', 10)));
	}

	/*
	 * use in:
	 * protected/modules/bookingcalendar/extensions/Calendar.php
	 * protected/modules/bookingcalendar/extensions/FFJuiDatePicker.php
	 */
	public static function getJsCode()
	{
		return <<<EOD
js:function(date){
	if (reservedDays !== undefined) {
		for (i = 0; i < reservedDays.length; i++) {
			var year = date.getFullYear();
			var month = date.getMonth() + 1;
			var day = date.getDate();
			if (year == reservedDays[i][0] && month == reservedDays[i][1] && day == reservedDays[i][2])
			{
				var key = year + '-' + month + '-' + day;
				if(typeof extremeDays != 'undefined' && typeof extremeDays[key] != 'undefined'){
					if(extremeDays[key] == 1){
						return [true, "datepicker-calendarDescriptionReserved_before"];
					}else if(extremeDays[key] == 2){
						return [true, "datepicker-calendarDescriptionReserved_after"];
					}
				}
				return [false, "datepicker-calendarDescriptionReserved"];
			}
		}
		return [true,""];
	}
}
EOD;
	}

	public static function getTimeList($lang = null, $time = null)
	{
		$list = array(
			self::TIME_BEFORE_NOON => tt('In the forenoon', 'booking', $lang),
			self::TIME_AFTER_NOON => tt('In the afternoon', 'booking', $lang),
		);

		if($time !== null){
			return isset($list[$time]) ? $list[$time] : '?';
		}

		return $list;
	}
}