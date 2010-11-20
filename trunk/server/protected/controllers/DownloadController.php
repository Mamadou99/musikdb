<?php

class DownloadController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to 'column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='column2';
	public $defaultAction='do';

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
				'actions'=>array('do'),
				'users'=>array('@'),
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 */
	public function actionDo()
	{
		// Stop on directory traversal attempts
		if(strpos($_GET['dir'], '../') !== false) return;


		$path = Yii::app()->params['mediaPath'].'/'.$_GET['dir'];
		$dirname = basename($path);

		if(is_readable($path)) {
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.$dirname.'.zip"'); 
			header('Content-Transfer-Encoding: binary');
			
			passthru("cd \"$path/../\"; zip -8 -r -q - \"$dirname\" | cat");
		}
	}

}
