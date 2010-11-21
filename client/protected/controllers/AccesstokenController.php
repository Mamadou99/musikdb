<?php

class AccesstokenController extends Controller
{
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
				'actions'=>array('valid'),
				'users'=>array('*'),
			),
			array('allow',
				'actions'=>array('generate'),
				'users'=>array('@'),
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Give the user his current accesstoken
	 */
	public function actionGenerate()
	{
		header('Content-type: text/plain');

		$accesstoken = self::generate(Yii::app()->user);
		echo $accesstoken->value;
	}

	/**
	 * Check if the given Accesstoken is valid
	 *
	 * This action is used by the backend. Since the backend can be
	 * located at another server this action is accessible by anyone.
	 *
	 * TODO: Use a webservice instead!
	 *
	 * 0: Accesstoken valid
	 * 1: Accesstoken invalid
	 * 2: Accesstoken not found
	 */
	public function actionValid()
	{
		header('Content-type: text/plain');

		if(!isset($_REQUEST['accesstoken'])) return;

		// try to find corresponding accesstoken
		$accesstokenValue = $_REQUEST['accesstoken'];
		$model=Accesstoken::model()->findByAttributes(array('value'=>$accesstokenValue));
		if(!$model) die('2');

		// check if accesstoken is valid
		if(time() - strtotime($model->timestamp) <
			Yii::app()->params['accesstokenValidityPeriod']) echo '0';
		else die('1');
	}

	/**
	 * Create or renew the accesstoken for the given WebUser
	 *
	 * @param WebUser
	 * @return Accesstoken
	 */
	public static function generate($webuser) {

		$user=User::model()->findByPk($webuser->id);
		$model=Accesstoken::model()->findByAttributes(array('user_id'=>$webuser->id));

		if(!$model) $model = new Accesstoken();

		$model->user_id = $webuser->id;
		$model->value = substr(md5($user->password.microtime()), 0, 8);
		$model->timestamp = new CDbExpression('NOW()');
		if($model->save()) {
			return $model;
		}
		else return null;
	}

	/**
	 * Lookup the accesstoken for the given WebUser
	 *
	 */
	public static function get($webuser) {

		$user=User::model()->findByPk($webuser->id);
		return Accesstoken::model()->findByAttributes(array('user_id'=>$webuser->id));
	}
}