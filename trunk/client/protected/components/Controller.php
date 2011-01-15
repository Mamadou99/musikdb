<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to 'application.views.layouts.column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	public function beforeAction() {

		// stop if user isn't logged in
		if(Yii::app()->user->isGuest) return true;

		// variables needed by frontend
		$vars = array(

			'baseUrl' => Yii::app()->request->baseUrl,
			'serverBaseUrl' => Server::model()->find()->baseUrl,
			'streamUrl' => '/index.php/stream/data',
			'directoryUrl' => '/index.php/directory/listing',
			'coverUrl' => Yii::app()->createUrl('track/cover'),
			'accesstokenValidUrl' => Yii::app()->createUrl('accesstoken/valid'),
			'accesstokenGenerateUrl' => Yii::app()->createUrl('accesstoken/generate'),
			'accesstokenRefreshPeriod' => Yii::app()->params['accesstokenRefreshPeriod'],
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
			'alwaysTranscode' => 0,
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

		Yii::app()->params['vars'] = $vars;

		return true;
	}
}