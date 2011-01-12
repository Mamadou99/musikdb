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

	public static function chooseBetterQuality($release1, $release2) {

		if(!is_a($release1,'ReleaseLogEntry') || !is_a($release2,'ReleaseLogEntry')) return null;

		// $release1 is always prefered if both have the same bitrate
		if($release1->avg_bitrate == $release2->avg_bitrate) return $release1;

		$div = 32000; // in bps
		$maxDiff = 100000; // in bps

		$bitrate1 = $release1->avg_bitrate;
		$bitrate2 = $release2->avg_bitrate;

		if($bitrate1 % $div != 0) $bitrate1+=$maxDiff;
		if($bitrate2 % $div != 0) $bitrate2+=$maxDiff;

		if($bitrate1 > $bitrate2) {
			return $release1;
		}
		else {
			return $release2;
		}
	}

	/**
	 * Returns the corresponding directory letter for $artist
	 * Second optional parameter $title is used if artist is empty
	 *
	 * @param releaseLogEntry $releaseLogEntry
	 */
	public static function getDirectoryLetter($releaseLogEntry) {

		if(!is_a($releaseLogEntry, 'releaseLogEntry')) return '';

		$subject = $releaseLogEntry->artist;
		if(!$subject) $subject = $releaseLogEntry->title;

		$firstChar = strtoupper(substr($subject,0,1));

		$letter = '#';
		if(preg_match('/[A-Z]/', $firstChar)) {
			$letter = $firstChar;
		}

		return $letter;
	}

	/**
	 * Generates the delete command for the specified release log entry
	 *
	 * @param releaseLogEntry $releaseLogEntry
	 */
	public static function getDeleteCommand($releaseLogEntry) {

		if(!is_a($releaseLogEntry, 'releaseLogEntry')) return '';

		$artist = $releaseLogEntry->artist;
		$title = $releaseLogEntry->title;

		$letterDir = Helpers::getDirectoryLetter($releaseLogEntry).DIRECTORY_SEPARATOR;
		$artistDir = '';
		if($artist) $artistDir = $artist.DIRECTORY_SEPARATOR;

		$delete_cmd = 'rm -r ".'.DIRECTORY_SEPARATOR.
			$letterDir.$artistDir.$title.DIRECTORY_SEPARATOR.'"';

		return $delete_cmd;
	}

}