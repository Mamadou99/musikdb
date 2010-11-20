<?php

class UserprofileController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';


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
				'actions'=>array('update'),
				'users'=>array('@'),
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}


	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}


	/**
	 * Updates a user profile
	 * User Profile will be created if necessary
	 */
	public function actionUpdate()
	{
		$status=null;

		try {
			$model=$this->loadModel(Yii::app()->user->id);
		} catch (CHttpException $e) {
			$model=new Userprofile();
			$model->user_id=Yii::app()->user->id;
		}

		if(isset($_POST['Userprofile']))
		{
			$model->attributes=$_POST['Userprofile'];
			if($model->save())
				$status=0;
			else
				$status=1;
		}

		$this->renderPartial('update',array('model'=>$model, 'status'=>$status));
	}


	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($user_id)
	{
		$model=Userprofile::model()->findByAttributes(array('user_id'=>(int)$user_id));
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
}
