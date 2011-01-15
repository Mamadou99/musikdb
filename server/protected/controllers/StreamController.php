<?php

class StreamController extends Controller
{
	/**
	 * @var CModel the currently loaded data model instance.
	 */
	private $_model;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl',
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('data'),
				'users'=>array('*'),
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Check accesstoken and create requested mp3 stream
	 *
	 * Parameters are encoded in a special way to make browsers think
	 * they handle a regular file. Acutally only one GET parameter with
	 * an empty value will be submitted. All information is in the key.
	 *
	 * Structure:
	 *
	 * [accesstoken].[relpath (base64)]_[bitrate/size].[mp3|jpg]
	 *
	 * e.g.
	 * 	 bf1258c7.QWxiZW4vSy9Lb29rcywgVGhlL0tvbmsvMDMgLSBUaGUgS29va3MgLSBNci4gTWFrZXIubXAz.128.mp3
	 * 	 bf1258c7.QWxiZW4vSy9Lb29rcywgVGhlL0tvbmsvMDMgLSBUaGUgS29va3MgLSBNci4gTWFrZXIubXAz.88.jpg
	 *
	 */
	public function actionData() {

		if(!count($_GET)) return;

		// decode parameters from GET variable
		reset($_GET);
		$params = explode('.',key($_GET));
		if(!count($params) == 3) return;
		$accesstokenValue = $params[0];
		$relpath = Helpers::decodeUrl($params[1]);
		$bitrate_size = $params[2];
		$alwaysTranscode = ((int) $params[3]==1) ? true : false;
		$ext = $params[4];

		// Check accesstoken
		if(!Helpers::checkAccesstoken($accesstokenValue)) return;

		// Create audio stream
		if($ext == 'mp3') {
			$this->_model=new AudioStream();
			$this->_model->relpath = $relpath;
			$this->_model->accesstoken = $accesstokenValue;
			$this->_model->bitrate = $bitrate_size;
			$this->_model->baseDir = Yii::app()->params['mediaPath'];
			$this->_model->alwaysTranscode = $alwaysTranscode;
			if(!$this->_model->validate()) return;

			$this->streamAudio();
		}

		// Create image stream
		else if($ext == 'jpg') {
			$this->_model=new ImageStream();
			$this->_model->relpath = dirname($relpath).
					DIRECTORY_SEPARATOR.Yii::app()->params['coverFile'];
			$this->_model->accesstoken = $accesstokenValue;
			$this->_model->size = $bitrate_size;
			$this->_model->baseDir = Yii::app()->params['mediaPath'];
			if(!$this->_model->validate()) return;

			$this->streamImage();
		}
	}

	/**
	 * Stream audio
	 *
	 */
	private function streamAudio() {

		$path = $this->_model->baseDir.DIRECTORY_SEPARATOR.$this->_model->relpath;
		$origExt = pathinfo($this->_model->relpath, PATHINFO_EXTENSION);

		if(!is_readable($path)) {
			header("HTTP/1.0 404 Not Found");
			return;
		}

		session_write_close();
		header("Content-Type: audio/mpeg");

		// direct stream (only for mp3 files)
		if(!$this->_model->alwaysTranscode && $origExt == 'mp3') {

			header("Content-Length: ".filesize($path));
			echo file_get_contents($path);
		}
		// transcoding
		else {
			$bitrate = $this->_model->bitrate;

			// we need to know the filesize of the transcoded file which isn't there yet
			// track lengths are stored in the database
			$file = File::model()->findByAttributes(array('relpath'=>$this->_model->relpath));
			if($file!==null)
				$filesize = $bitrate/8 * $file->length;
			// If the file isn't in the database we assume that it will be 3 MB.
			// The loading percentage will display wrong but the rest will work fine.
			else $filesize = 3*1024*1024;
			header("Content-Length: $filesize");

			$command = Yii::app()->params['ffmpegBin']." -ab $bitrate -i \"$path\" -f mp3 pipe: | cat";
			echo passthru($command, $return_var);
		}
	}

	/**
	 * Stream image
	 */
	private function streamImage() {

		header("Content-Type: image/jpeg");
		$path = $this->_model->baseDir.DIRECTORY_SEPARATOR.$this->_model->relpath;

		if(!is_readable($path)) $path=dirname(Yii::app()->basePath).DIRECTORY_SEPARATOR.'images'.
				DIRECTORY_SEPARATOR.'nocover.jpg';

		session_write_close();
		if($this->_model->size==0) {
			echo file_get_contents($path);
		}
		else {
			// TODO: image scaling and caching
		}
	}
}