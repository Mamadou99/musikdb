<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

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
				'actions'=>array('login', 'error'),
				'users'=>array('*'),
			),
			array('allow',
				'actions'=>array('index','app','logout'),
				'users'=>array('@'),
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{

		$this->render('index');
	}

	public function actionApp() {

		// get accesstoken
		Yii::import('application.controllers.AccesstokenController');
		$accesstoken = AccesstokenController::generate(Yii::app()->user);

		// variables needed by frontend
		$vars = array(

			'baseUrl' => Yii::app()->request->baseUrl,
			'serverBaseUrl' => Server::model()->find()->baseUrl,
			'streamUrl' => '/index.php/stream/data',
			'coverUrl' => Yii::app()->createUrl('track/cover'),
			'accesstokenValidUrl' => Yii::app()->createUrl('accesstoken/valid'),
			'accesstokenGenerateUrl' => Yii::app()->createUrl('accesstoken/generate'),
			'accesstokenRefreshPeriod' => Yii::app()->params['accesstokenRefreshPeriod'],
			'accesstoken' => $accesstoken->value,
			'savePlaylistUrl' => Yii::app()->createUrl('playlist/save'),
			'loadPlaylistUrl' => Yii::app()->createUrl('playlist/load'),
			'loadPlaylistbrowserUrl' => Yii::app()->createUrl('playlist/list'),
			'createPlaylistUrl' => Yii::app()->createUrl('playlist/create'),
			'savePlaylistUrl' => Yii::app()->createUrl('playlist/save'),
			'renamePlaylistUrl' => Yii::app()->createUrl('playlist/rename'),
			'deletePlaylistUrl' => Yii::app()->createUrl('playlist/delete'),
			'similarTracksUrl' => Yii::app()->createUrl('track/similar'),
			'metaDataUrl' => Yii::app()->createUrl('track/meta'),
			'windowTitle' => Yii::app()->name.' '.Yii::app()->version,
			'jPlayerSwfPath' => Yii::app()->request->baseUrl.'/js',

			// settings which may be overriden by the userprofile
			'crossfadeTime' => (int)Yii::app()->params['crossfadeTime'],
			'transcodingBitrate' => 0,
		);

		// retrieve user settings
		$userprofile = Userprofile::model()->findByAttributes(
				array('user_id'=>Yii::app()->user->id));

		if($userprofile!==null) {
			// override system settings with corresponding usersettings
			foreach($vars as $key=>$value) {
				if(isset($userprofile->$key)) {
					// why do we always get strings from the Userprofile object?
					// let's force them back to INT...
					if(is_int($vars[$key]))
						$vars[$key] = (int)$userprofile->$key;
					else
						$vars[$key] = $userprofile->$key;
				}
			}
			// override server
			if($userprofile->server!==null) {
				$vars['serverBaseUrl']=$userprofile->server->baseUrl;
			}
		}

		$this->render('app', array('vars'=>$vars));
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new LoginForm;

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				$this->redirect(Yii::app()->user->returnUrl);
		}
		// display the login form
		$this->render('login',array('model'=>$model));
	}

	/**
	 * Logs out the current user and redirect to login page.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->createUrl('site/login'));
	}
}