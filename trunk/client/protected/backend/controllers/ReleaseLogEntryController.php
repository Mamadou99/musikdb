<?php

class ReleaseLogEntryController extends BackendController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

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
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new ReleaseLogEntry;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ReleaseLogEntry']))
		{
			$model->attributes=$_POST['ReleaseLogEntry'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['ReleaseLogEntry']))
		{
			$model->attributes=$_POST['ReleaseLogEntry'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('ReleaseLogEntry');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new ReleaseLogEntry('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ReleaseLogEntry']))
			$model->attributes=$_GET['ReleaseLogEntry'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Processes CSV input
	 */
	public function actionImportCsv()
	{
		$model=new Csv();
		$releases=array();
		$step = 1;

		// step 2 - validate
		if(isset($_POST['Csv']))
		{
			$step = 2;

			$model->attributes=$_POST['Csv'];
			if($model->validate()) {

				// get releases from csv input
				$releases = $this->createReleasesFromCsv($model->input);

				// validate releases and add error messages on failure
				$errors = '';
				$tempStore = array();
				foreach($releases as $lineno => $release) {
					$errorMsg = '';

					// check if there's a duplicate in the user's input
					if(array_search($release->artist.$release->title, $tempStore) !== false)
						$errorMsg.= 'This release appears more than once in your input: <em>'.
							"{$release->artist} &ndash; {$release->title}</em>\n";

					// validate model
					if(!$release->validate())
						$errorMsg.= CHtml::errorSummary($release);

					if($errorMsg) $errors.= "Error in line $lineno: $errorMsg<br />";
					$tempStore[] = $release->artist.$release->title;
				}
				unset($tempStore);
				if($errors) $model->addError('input', $errors);
			}
		}

		// step 3 - sure?
		if(isset($_POST['Csv']) && !isset($_POST['sure']) && !$model->hasErrors())
		{
			$step = 3;
		}


		// step 4 - save
		if(isset($_POST['Csv']) && isset($_POST['sure']) && !$model->hasErrors())
		{
			$step = 4;

			// save releases and redirect to index page on success
			$error = false;
			foreach($releases as $release) {
				if(!$release->save()) {
					$error = true;
				}
			}
			if(!$error) $this->redirect(array('index'));
			else die('Unexpected error');
		}

		$this->render('importcsv',array(
			'step'=>$step,
			'model'=>$model,
			'releases'=>$releases,
		));
	}

	/**
	 * Converts the CSV user input into Release models
	 * @return array
	 */
	protected function createReleasesFromCsv($csv) {

		$models = array();

		$lines = explode("\n", $csv);
		foreach($lines as $lineno => $line) {
			if(!trim($line)) continue;

			$record = str_getcsv(trim($line), ',', '"', '\\');

			$model = new ReleaseLogEntry;

			// type
			$model->type = $record[0];

			// artist
			if(isset($record[1])) $model->artist = $record[1];
			else $model->artist = null;

			// title
			if(isset($record[2])) $model->title = $record[2];

			// user id
			if(isset($record[3])) $model->user_id = (int) $record[3];

			// date
			if(isset($record[4])) $model->date = $record[4];

			$models[$lineno+1] = $model;
		}

		return $models;
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=ReleaseLogEntry::model()->findByPk((int)$id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='release-log-entry-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
