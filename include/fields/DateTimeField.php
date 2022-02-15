<?php
require_once 'include/utils/utils.php';

class DateTimeField {

	static protected $databaseTimeZone = null;
	protected $datetime;
	private static $cache = array();

	/**
	 *时间传参
	 * @param type $value
	 */
	public function __construct($value) {
		if(empty($value)) {
			$value = date("Y-m-d H:i:s");
		}
		$this->date = null;
		$this->time = null;
		$this->datetime = $value;
	}

	/** 日期转换为数据库格式
	 * @param $user -- value :: Type Users
	 * @returns $insert_date -- insert_date :: Type string
	 */
	function getDBInsertDateValue($user = null) {
		$value = explode(' ', $this->datetime);
		if (count($value) == 2) {
			$value[0] = self::convertToUserFormat($value[0]);
		}
		$insert_time = '';
		if ($value[1] != '') {
			$date = self::convertToDBTimeZone($this->datetime, $user);
			$insert_date = $date->format('Y-m-d');
		} else {
			$insert_date = self::convertToDBFormat($value[0]);
		}
		return $insert_date;
	}

	/**
	 *
	 * @param Users $user
	 * @return String
	 */
	public function getDBInsertDateTimeValue($user = null) {
		return $this->getDBInsertDateValue($user) . ' ' .$this->getDBInsertTimeValue($user);
	}

	public function getDisplayDateTimeValue ($user = null) {
		return $this->getDisplayDate($user) . ' ' .$this->getDisplayTime($user);
	}

	/**
	 * 格式化用户自定义日期
	 * @global Users $current_user
	 * @param type $date
	 * @param Users $user
	 * @return type
	 */
	public static function convertToDBFormat($date, $user = null) {
		global $current_user;
		if(empty($user)) {
			$user = $current_user;
		}
		$format = $user->date_format;
		return self::__convertToDBFormat($date, $format);
	}

	/**
	 * 日期格式 年 月 日 日历模块使用
	 * @param type $date
	 * @param string $format
	 * @return string
	 */
	public static function __convertToDBFormat($date, $format= 'dd-mm-yyyy') {
		$dbDate = '';
		if ($format == 'dd-mm-yyyy') {
			list($d, $m, $y) = explode('-', $date);
		} elseif ($format == 'mm-dd-yyyy') {
			list($m, $d, $y) = explode('-', $date);
		} elseif ($format == 'yyyy-mm-dd') {
			list($y, $m, $d) = explode('-', $date);
		}
		if (!$y && !$m && !$d) {
			$dbDate = '';
		} else {
			$dbDate = $y . '-' . $m . '-' . $d;
		}
		return $dbDate;
	}

	/**
	 * 把日期拆分成数组格式
	 * @return Array
	 */
	public static function convertToInternalFormat($date) {
		if(!is_array($date)) {
			$date = explode(' ', $date);
		}
		return $date;
	}

	/**
	 * 日期转换为用户格式
	 * @global Users $current_user
	 * @param type $date
	 * @param Users $user
	 * @return type
	 */
	public static function convertToUserFormat($date, $user = null) {
		if(empty($user)){
			global $current_user;
			$user = $current_user;
		}
		$format = $user->date_format;
		return self::__convertToUserFormat($date, $format);
	}

	/**
	 * 日期转换为用户格式
	 * @param type $date
	 * @param type $format
	 * @return type
	 */
	public static function __convertToUserFormat($date, $format='dd-mm-yyyy') {
		$date = self::convertToInternalFormat($date);
		list($y, $m, $d) = explode('-', $date[0]);
		if ($format == 'dd-mm-yyyy') {
			$date[0] = $d . '-' . $m . '-' . $y;
		} elseif ($format == 'mm-dd-yyyy') {
			$date[0] = $m . '-' . $d . '-' . $y;
		} elseif ($format == 'yyyy-mm-dd') {
			$date[0] = $y . '-' . $m . '-' . $d;
		}
		if ($date[1] != '') {
			$userDate = $date[0] . ' ' . $date[1];
		} else {
			$userDate = $date[0];
		}
		return $userDate;
	}

	/**
	 * 按时区显示时间
	 * @global Users $current_user
	 * @param type $value
	 * @param Users $user
	 */
	public static function convertToUserTimeZone($value, $user = null ) {
		global $current_user, $default_timezone;
		if(empty($user)) {
			$user = $current_user;
		}
		$timeZone = $user->time_zone ? $user->time_zone : $default_timezone;
		return self::convertTimeZone($value, self::getDBTimeZone(), $timeZone);
	}

	/**
	 *
	 * @global Users $current_user
	 * @param type $value
	 * @param Users $user
	 */
	public static function convertToDBTimeZone( $value, $user = null ) {
		global $current_user, $default_timezone;
		if(empty($user)) {
			$user = $current_user;
		}
		$timeZone = $user->time_zone ? $user->time_zone : $default_timezone;
		$value = self::sanitizeDate($value, $user);
		return self::convertTimeZone($value, $timeZone, self::getDBTimeZone() );
	}

	/**
	 *
	 * @param type $time
	 * @param type $sourceTimeZoneName
	 * @param type $targetTimeZoneName
	 * @return DateTime
	 */
	public static function convertTimeZone($time, $sourceTimeZoneName, $targetTimeZoneName) {
		// TODO Caching is causing problem in getting the right date time format in Calendar module.
		// Need to figure out the root cause for the problem. Till then, disabling caching.
		//if(empty(self::$cache[$time][$targetTimeZoneName])) {
			// create datetime object for given time in source timezone
			$sourceTimeZone = new DateTimeZone($sourceTimeZoneName);
			if($time == '24:00') $time = '00:00';
			$myDateTime = new DateTime($time, $sourceTimeZone);

			// convert this to target timezone using the DateTimeZone object
			$targetTimeZone = new DateTimeZone($targetTimeZoneName);
			$myDateTime->setTimeZone($targetTimeZone);
			self::$cache[$time][$targetTimeZoneName] = $myDateTime;
		//}
		$myDateTime = self::$cache[$time][$targetTimeZoneName];
		return $myDateTime;
	}

	/** Function to set timee values compatible to database (GMT)
	 * @param $user -- value :: Type Users
	 * @returns $insert_date -- insert_date :: Type string
	 */
	function getDBInsertTimeValue($user = null) {
		global $log;
		$log->debug("Entering getDBInsertTimeValue(" . $this->datetime . ") method ...");
		$date = self::convertToDBTimeZone($this->datetime, $user);
		$log->debug("Exiting getDBInsertTimeValue method ...");
		return $date->format("H:i:s");
	}

	/**
	 * 用户自定义日期和时间显示格式
	 * @global type $log
	 * @global Users $current_user
	 * @return string
	 */
	function getDisplayDate( $user = null ) {
		$date_value = explode(' ',$this->datetime);
		if ($date_value[1] != '') {
			$date = self::convertToUserTimeZone($this->datetime, $user);
			$date_value = $date->format('Y-m-d');
		}
		$display_date = self::convertToUserFormat($date_value, $user);
		return $display_date;
	}

	function getDisplayTime( $user = null ) {
		$date = self::convertToUserTimeZone($this->datetime, $user);
		$time = $date->format("H:i:s");
		return $time;
	}
	
	//数据库时区
	static function getDBTimeZone() {
		if(empty(self::$databaseTimeZone)) {
			$defaultTimeZone = date_default_timezone_get();
			//返回不会为空
			if(empty($defaultTimeZone)) {
				$defaultTimeZone = 'UTC';
			}
			self::$databaseTimeZone = $defaultTimeZone;
		}
		return self::$databaseTimeZone;
	}

	//时间格式转换为标准函数参数
	static function getPHPDateFormat( $user = null) {
		if(empty($user)) {
			global $current_user;
			$user = $current_user;
		}
		return str_replace(array('yyyy', 'mm','dd'), array('Y', 'm', 'd'), $user->date_format);
	}
	//日期和时间格式化为YMDHIS
	//like 22-09-2013 12:01 to 2013-09-22 12:01
	private static function sanitizeDate($value, $user) {
		if(empty($user)) {
			global $current_user;
			$user = $current_user;
		}
		if($user->date_format == 'mm-dd-yyyy') {
			list($date, $time) = explode(' ', $value);
			if(!empty($date)) {
				list($m, $d, $y) = explode('-', $date);
				if(strlen($m) < 3) {
					$time = ' '.$time;
					$value = "$y-$m-$d".rtrim($time);
				}
			}
		}
		return $value;
	}
}
