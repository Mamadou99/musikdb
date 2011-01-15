<?php

class AudioStream extends CModel
{
	public $bitrate;
	public $accesstoken;
	public $relpath;
	public $baseDir;
	public $alwaysTranscode;


	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('accesstoken, relpath, baseDir', 'required'),
		);
	}

	public function attributeNames()
	{
		return array(
			'bitrate',
			'accesstoken',
			'relpath',
			'baseDir',
			'alwaysTranscode',
		);
	}

}