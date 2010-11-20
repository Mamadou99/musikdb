<?php

class SettingsWidget extends CWidget
{
	private $_model;

	public function run()
	{
		$this->loadModel();

		// Get existing user profile or create a new one
		if(isset($this->_model->userprofile))
			$userprofile=$this->_model->userprofile;
		else
			$userprofile=new Userprofile();

		$this->render('settingsWidget', array('model'=>$userprofile));
	}

	/**
	 * Returns the data model based on the primary key of the currently logged in user.
	 * If the data model is not found, an HTTP exception will be raised.
	 */
	public function loadModel()
	{
		if($this->_model===null)
		{
			$this->_model=User::model()->findbyPk(Yii::app()->user->id);

			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}

}

?>
