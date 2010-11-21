<?php

class DirectoryController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to 'column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='column2';
	public $defaultAction='listing';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
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
				'actions'=>array('listing'),
				'users'=>array('*'),
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 */
	public function actionListing()
	{
		// get accesstoken
		Yii::import('application.controllers.AccesstokenController');
		$accesstoken=AccesstokenController::get(Yii::app()->user);
		if($accesstoken) $accesstokenValue=$accesstoken->value;

		$dir=$_REQUEST['dir'];
		if($dir=='/') $dir='';

		$vars=Yii::app()->params['vars'];
		$request=$vars['serverBaseUrl'].$vars['directoryUrl'].
				'/dir/'.$dir.'/accesstoken/'.$accesstokenValue;

		$response=json_decode(file_get_contents($request));

		$this->renderPartial('listing',array(
			'model'=>$response,
			'accesstoken'=>$accesstokenValue));
	}

}
