<?php

class Helpers extends Controller {

	public static function encodeUrl($string) {

		//return str_replace('%2F', '/', rawurlencode($string));
		return base64_encode($string);
	}

	public static function decodeUrl($string) {

		//return rawurldecode($string);
		return base64_decode($string);
	}

	public static function secToMinSec($seconds) {

		$minutes=0;
		while($seconds >= 60) {
			$seconds-=60;
			$minutes++;
		}

		return $minutes.':'.sprintf("%02d",$seconds);
	}

	public static function minSecToSec($string) {

		// TODO: Use this function in CollectionScanner

		$length_parts = explode(':',$string);
		$minutes = (int) $length_parts[0];
		$seconds = (int) $length_parts[1];
		return $minutes*60 + $seconds;
	}

	public static function addUrlParams($url, $params) {

		if(strpos($url, '?')===false)
			$url.='?';

		foreach($params as $key => $value)
			$url.="&$key=$value";

		return $url;
	}

	public static function checkAccesstoken($accesstoken) {

		if(!trim($accesstoken)) return false;

		$checkUrl = self::addUrlParams(
			Yii::app()->params['accessUrl'], array('accesstoken'=>$accesstoken));
		$output = trim(file_get_contents($checkUrl));

		if($output != '0') return false;

		return true;
	}

}