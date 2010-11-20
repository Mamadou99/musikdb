<?php

class PlaylistController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to 'column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='column2';

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
				'actions'=>array('list', 'load', 'create', 'save', 'delete', 'rename'),
				'users'=>array('@'),
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Lists all playlists
	 */
	public function actionList() {

		$user_id = Yii::app()->user->id;
		$playlists = Playlist::model()->findAllByAttributes(array('user_id'=>$user_id));

		if(count($playlists)) $this->renderPartial('list', array('playlists'=>$playlists));
	}

	/**
	 * Loads a playlist
	 */
	public function actionLoad()
	{
		$id = null;
		if(isset($_REQUEST['id'])) $id = (int) $_REQUEST['id'];
		$user_id = Yii::app()->user->id;

		if(!is_int($id)) {
			// Load first playlist if no ID is given
			$playlist = Playlist::model()->findByAttributes(array('user_id'=>$user_id));

			if(!$playlist) {
				// Create new playlist if no one exists
				$playlist = new Playlist;
				$playlist->name = 'Default Playlist';
				$playlist->user_id = $user_id;
				$playlist->save();
			}
		}
		else {
			// Load specific playlist
			$playlist = Playlist::model()->findByAttributes(array('user_id'=>$user_id,'id'=>$id));
		}

		if($playlist) $this->renderPartial('load', array('playlist'=>$playlist));
	}

	/**
	 * Saves a playlist
	 */
	public function actionSave() {

		$id = null;
		$data = null;

		if(isset($_POST['id'])) $id = (int) $_POST['id'];
		$user_id = Yii::app()->user->id;
		if(isset($_POST['playlist'])) $data = trim($_POST['playlist']);

		if(!$data) {
			echo "Error: Playlist is empty!";
			return;
		}

		$playlist = Playlist::model()->findByAttributes(array('user_id'=>$user_id,'id'=>$id));

		$success = false;
		if($playlist) {
			$playlist->data = $data;
			$success = $playlist->save();
		}

		if($success)
			echo "Playlist successfully saved!";
		else
			echo "Unknown error while trying to save.";
	}

	/**
	 * Creates a new playlist
	 */
	public function actionCreate() {

		if(!isset($_REQUEST['name'])) return;

		$name = trim($_REQUEST['name']);
		if(!$name) $name = 'New Playlist';

		$user_id = Yii::app()->user->id;

		$playlist = new Playlist;
		$playlist->name = $name;
		$playlist->user_id = $user_id;
		$playlist->save();
	}

	/**
	 * Rename a playlist
	 */
	public function actionRename() {

		if(!isset($_REQUEST['id'])) return;

		$id = (int) $_REQUEST['id'];
		if(isset($_REQUEST['name'])) $name = trim($_REQUEST['name']);
		$user_id = Yii::app()->user->id;

		if(!$id || !$name) return;

		$playlist = Playlist::model()->findByAttributes(array('user_id'=>$user_id,'id'=>$id));
		$playlist->name = $name;
		$playlist->save();
	}

	/**
	 * Deletes a playlist
	 */
	public function actionDelete() {

		if(!isset($_REQUEST['id'])) return;

		$id = (int) $_REQUEST['id'];
		$user_id = Yii::app()->user->id;

		if(!$id) return;
		Playlist::model()->deleteAllByAttributes(array('user_id'=>$user_id, 'id'=>$id));
	}

}
