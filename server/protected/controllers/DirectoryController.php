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
		if(!Helpers::checkAccesstoken($_GET['accesstoken']))
			die('Accesstoken not valid');

		$model=new Directory();
		$model->baseDir = Yii::app()->params['mediaPath'];

		if(!is_readable($model->baseDir)) {
			die('Directory not available');
		}

		if(isset($_REQUEST['dir']))
			$model->directory = Helpers::decodeUrl($_REQUEST['dir']);

		$model->allowedExts = array('mp3');
		$model->contents = $this->readDirectory($model);
		$model->upperDir = '';

		$this->renderPartial('listing',array('model'=>$model));
	}


	public function readDirectory($model, $addDownload=true) {

		$relPath = $model->directory;
		$dir = $model->baseDir.'/'.trim($model->directory, '/');
		$contents = array();
		$hasCover = false;
		$hasFiles = false;
		$hasDirs = false;

		$d = dir($dir);
		while(false !== ($entry = $d->read())) {

			// Skip hidden files
			if($entry[0] == ".") continue;

			if(is_dir("$dir/$entry")) {
				$contents[$entry] = array(
						'relpath' => "$relPath/$entry",
						'name' => $entry,
						'type' => 'dir',
				);
				$hasDirs = true;
			}
			elseif(is_readable("$dir/$entry")) {

				$ext = trim(strrchr($entry, '.'), '.');
				if($entry == 'folder.jpg') $hasCover = true;
				if(!in_array($ext, $model->allowedExts)) continue;

				$contents[$entry] = array(
						'relpath' => trim("$relPath/$entry", '/'),
						'name' => $entry,
						'type' => 'media',
						'ext' => $ext
				);
				$hasFiles = true;
			}

		}

		// Add download link if directory contains only files
		if($addDownload && $hasFiles && !$hasDirs) {
			$contents[$relPath] = array(
				'relpath' => '',
				'name' => basename(trim($relPath, '/')).'.zip',
				'type' => 'download',
				'ext' => 'zip'
			);
		}

		if($hasCover) {
			foreach($contents as $key => $value) {
				$contents[$key]['cover'] = true;
			}
		}

		ksort($contents);
		return $contents;
	}

}
