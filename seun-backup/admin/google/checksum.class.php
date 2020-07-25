<?php
define("SYSTEMKEY", "qfe78dsd3§%sd"); // your secret password
define("USE_DYNAMIC_PATTERN", true); // use dynamically build date patterns
/**
 * Checksum
 *
 * @author Thomas Schäfer
 * @desc time limited checksum builder
 * @example
 *
 * # build
 * // injected unlimited number of parameter arguments as string or integer
 * $checksumToLink = Checksum:: build ("YourFirstParameter"," YourSecondParameter"); // sample output aeeb0da600ab1d9934f1f065498c7497479cbd12
 * output view link:
 * <a href="http://www.your-domain.de/index.php?a=YourFirstParameter&b=YourSecondParameter&csm=$checksumToLink"></a>
 *
 * incoming request (e.g.):
 * $url = parse_url($_SERVER["REQUEST_URI"]);
 * parse_str($url["query"], $query);
 *
 * # proof
 * // inject the same number of parameter arguments as above with
 * // leading checksum string
 * $boolProof = Checksum::proof($query["csm"],$query["a"],$query["b"]);
 *
 * if($boolProof) echo "valid";
 * else echo "invalid";
 *
 * features:
 * - build
 * - proof
 * - setTimestamp(date)
 * - setValidTimeRange (seconds)
 * - setValidTimeRangeInDays (days)
 * - isValidFromTo (date, date)
 *
 */
class Checksum
{
	/**
	 * @const time shift constant in seconds for valid checksum
	 * desc should correspond with session validation time
	 */
	const DYN_PATTERN = USE_DYNAMIC_PATTERN; // true = dynamically build, false = individual pattern array
	private static $iCounter = 0;
	private static $strTimestamp = null;
	private static $strTimestampTo = null;
	private static $validTimeRange = 3600; // one hour valid
	/**
	 * checksum
	 * @desc build query checksum
	 * @return string 		new checksum
	 */
	public static function build()
	{
		$argCounter = func_num_args();
		if ($argCounter == 0)
		{
			throw new Exception("You need to define some string parameters for being able to build a checksum");
		}
		self::$iCounter = $argCounter;
		$arguments = func_get_args();
		$string = call_user_func(array (
				"self",
				"getRawKeyString"
			),
			$arguments
		);
		$time = self::getRawKeyTime();
		// value key

		$key = self::keyPattern($string, $time);
		return implode("", $key);
	}
	/**
	 * proof
	 * @desc proof checksum on string and time parts
	 * @param string reference 		referencing checksum
	 * @return bool
	 */
	public static function proof()
	{
		// checks
		$isValidChecksum = false;
		$isValidString = false;
		$isValidTime = false;
		$reference_checksum = null;
		if (func_num_args() <= 1)
		{
			throw new Exception("You need to define more then a single parameters");
		}

		$internal_arguments = array ();
		$arguments = func_get_args();
		foreach ($arguments as $argument)
		{
			if (preg_match('/[a-z0-9]{32}/', $argument))
			{
				$reference_checksum = $argument;
			}
			else
			{
				$internal_arguments[] = $argument;
			}
		}

		// new 	values
		$string = call_user_func(array (
			"self",
			"getRawKeyString"
			),
			$internal_arguments
		);
		$time = self::getRawKeyTime(); // time part

		// new value key
		$keyPattern = self::keyPattern($string, $time);

		// old values
		$timeParts = self::getTimeParts(); // positions of time parts (8,9)
		$stringReference = str_split($reference_checksum, 4);
		$timeReference = $stringReference[$timeParts[8]] . $stringReference[$timeParts[9]]; //
		$decodedTime = hexdec($timeReference);

		// begin validate time range
		$referingTime = time();
		if(
			!empty(Checksum::$strTimestamp) and
			!empty(Checksum::$strTimestampTo) and
			$referingTime >= Checksum::$strTimestamp and
			$referingTime <= Checksum::$strTimestampTo
		) {
			$isValidTime = true;
		} elseif(empty($isValidTime) and $referingTime - Checksum::$validTimeRange < $decodedTime){
			$isValidTime = true;
		}
		// end validate time range

		// check parts of old and new string value
		$isValidString = Checksum::validate($string, $time, $keyPattern, $stringReference);

		// check secondary conditions
		if ($isValidString and $isValidTime)
		{
			$isValidChecksum = true; // set primary condition => checksum is valid
		}
		return $isValidChecksum;
	}
	/**
	 * setTimestamp
	 * @param integer $timestamp
	 * @desc change the timestamp
	 */
	public static function setTimestamp($timestamp)
	{
		if (is_string($timestamp)){
			self::$strTimestamp = strtotime($timestamp);
		} else {
			throw new Exception("Timestamp has to be of type string, a valid date format which can be converted to unix time.");
		}
	}
	/**
	 * specify individual ranges
	 * @param integer $validTimeRange
	 */
	public static function setValidTimeRange($validTimeRange = null)
	{
		if (is_integer($validTimeRange) and $validTimeRange > -1) {
			self::$validTimeRange = $validTimeRange;
		} else {
			throw new Exception("Range has to be of type integer and greater than -1.");
		}
	}

	/**
	 * setValidTimeRangeInDays
	 * @param integer $validDays
	 */
	public static function setValidTimeRangeInDays($validDays = null)
	{
		selfValidTimeRange(3600*24*$validDays);
	}

	/**
	 * isValidFromTo
	 * @param string $dateFrom a valid timestamp, e.g.: 2009-01-01
	 * @param string $dateTo a valid timestamp, e.g.: 2009-02-01
	 */
	public static function isValidFromTo($dateFrom = null, $dateTo = null)
	{
		if (is_string($dateFrom) and is_string($dateTo))
		{
			$df = strtotime($dateFrom);
			$dt = strtotime($dateTo);
			if($dt>$df) {
				self::$strTimestamp = strtotime($dateFrom);
				self::$strTimestampTo = strtotime($dateTo);
			} else {
				throw new Exception("DateTo has to be greater than DateFrom");
			}
		}
		else
		{
			throw new Exception("Range has to be of type integer and greater than -1.");
		}
	}

	/**
	 * getRawKeyTime
	 * @desc splits hex time string into parts
	 * @return array
	 */
	private static function getRawKeyTime()
	{
		if (!empty (self::$strTimestamp))
		{
			return str_split(dechex(self::$strTimestamp), 4);
		}
		else
		{
			return str_split(dechex(time()), 4);
		}
	}
	/**
	 * getRawKeyString
	 * @desc creates hash (system key added) and splits md5 string into parts
	 * @return array
	 */
	private static function getRawKeyString()
	{
		$strRawKey = "";
		if (func_num_args() > 0)
		{
			$arguments = func_get_args();
			foreach ($arguments[0] as $argument)
			{
				$strRawKey .= $argument;
			}
		}
		else
		{
			throw Exception("No parameters had been injected.");
		}
		$strRawKey .= SYSTEMKEY;
		$string = str_split(md5($strRawKey), 4);
		return $string;
	}
	/**
	 * drawPatternByDay
	 * @desc returns pattern orders for each day
	 * @param integer $indexDayPattern
	 * @return array
	 */
	private static function drawPatternByDay($indexDayPattern)
	{
		switch (self::DYN_PATTERN)
		{
			// dynamically build date pattern
			case true :
				$num = date("Ym");
				$t = date("t");
				$range = array ();
				$arrSort = array ();
				$pattern = array ();
				for ($i = 1; $i <= $t; $i++)
				{
					$date = strtotime(date("Ym") . str_pad($i, 2, "0", STR_PAD_LEFT));
					$str = str_split(md5($date), 4);
					foreach ($str as $index => $val)
					{
						$range[$i][$index] = $val;
					}
					$weekday = date("w", $date);
					$parts = str_split(md5(strtotime(date("Ym"))), 4);
					$range[$i][8] = $str[8] = $parts[$weekday];
					$range[$i][9] = $str[9] = $parts[$weekday +1];
					sort($str);
					$array = array_flip($range[$i]);
					foreach ($str as $key => $val)
					{
						$pattern[$i][$key] = $array[$val];
					}
				}
				return ($pattern[$indexDayPattern -1]);
			default :
				/*
				 * positions: 0 - 7 => string parts 8 - 9 => time parts shifting parts
				 * by reordering range
				 */
			$patterns = array(
				array(0,2,4,6,8,1,3,5,7,9),
				array(1,3,5,7,9,2,4,6,8,0),
				array(2,4,6,8,0,3,5,7,9,1),
				array(3,5,7,9,1,2,4,6,8,0),
				array(2,4,6,8,1,3,5,7,9,0),
				array(7,9,0,2,4,6,8,1,3,5),
				array(1,2,4,6,8,0,3,5,7,9),
				array(8,0,3,5,7,1,2,4,6,9),
				array(5,7,2,4,6,8,0,3,9,1),
				array(6,8,0,3,5,7,2,4,9,1),
				array(8,0,3,5,6,7,2,4,9,1),
				array(7,2,4,9,8,0,3,5,6,1),
				array(2,0,4,6,8,1,3,5,7,9),
				array(1,3,7,5,9,2,4,6,8,0),
				array(2,4,8,6,0,3,5,7,9,1),
				array(3,5,9,7,1,2,4,6,8,0),
				array(2,4,6,8,1,5,3,7,9,0),
				array(7,9,0,2,6,4,8,1,3,5),
				array(1,2,4,6,8,0,5,3,7,9),
				array(8,0,3,5,7,1,2,4,6,9),
				array(5,7,2,4,6,3,0,8,9,1),
				array(6,8,4,3,5,7,2,0,9,1),
				array(8,2,3,5,6,7,0,4,9,1),
				array(7,2,1,9,5,0,3,8,6,4),
				array(5,7,2,4,1,8,0,3,9,6),
				array(6,8,9,3,5,2,7,4,0,1),
				array(8,0,5,3,6,7,4,2,9,1),
				array(4,2,6,0,8,3,5,7,9,1),
				array(3,1,5,2,9,7,4,6,8,0),
				array(1,2,4,0,8,6,3,5,7,9),
				array(0,8,3,5,7,2,1,4,6,9),
			);

			return ($patterns[$indexDayPattern-1]);
		}
	}
	/**
	 * keyPattern
	 * @desc re-orders the key map by pattern
	 * @param array $string
	 * @param array $key
	 * @return array
	 */
	private static function keyPattern($string, $time)
	{
		$indexDayPattern = self::drawPatternByDay(date("d"));
		$merge = array_merge($string, $time);
		$newPatternOrder = array ();
		foreach ($indexDayPattern as $index)
		{
			$newPatternOrder[] = $merge[$index];
		}
		return $newPatternOrder;
	}
	/**
	 * getTimeParts
	 * @desc returns array of positions of time parts
	 * return array
	 */
	private static function getTimeParts()
	{
		$pattern = self::drawPatternByDay(date("d"));
		$timeParts = array ();
		foreach ($pattern as $key => $index)
		{
			switch ($index)
			{
				// time parts
				case 9 :
				case 8 :
					$timeParts[$index] = $key;
					break;
					// string parts
				default :
					break;
			}
		}
		return $timeParts;
	}
	/**
	 * validate
	 * @desc checks against pattern if the checksum is valid
	 * @param array $string
	 * @param array $time
	 * @param array $key 					new value key
	 * @param array $stringReference		old value key
	 * return bool
	 */
	private static function validate($string, $time, $key, $stringReference)
	{
		$pattern = self::drawPatternByDay(date("d"));
		$intCounter = 1;
		foreach ($pattern as $index)
		{
			switch ($index)
			{
				// time parts
				case 9 :
				case 8 :
					break;
					// string parts
				default :
					if ($key[$index] == $stringReference[$index])
					{
						$intCounter++;
					}
					break;
			}
		}
		if ($intCounter >= 7)
		{ // all string parts are equal
			return true;
		}
		return false;
	}
}