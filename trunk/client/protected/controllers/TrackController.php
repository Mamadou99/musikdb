<?php

class TrackController extends Controller
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
	 * @var string Application's Last.fm API Key
	 */
	const LASTFM_API_KEY = '7d6be058bd5e7a2c446abd7cb8f40339';

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
				'actions'=>array('search','cover','similar','meta'),
				'users'=>array('@'),
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}

	public function actionSearch() {

		$query = '';
		if(isset($_REQUEST['query'])) $query = trim($_REQUEST['query']);

		if(!$query) return;

		$quotedQuery = Yii::app()->db->quoteValue($query);

		$criteria = new CDbCriteria;
		$criteria->limit = Yii::app()->params['searchLimit'];
		$criteria->with = 'track';
		$criteria->condition = "MATCH (t.meta) AGAINST (\"$quotedQuery\")";

		$index = Metastring::model()->findAll($criteria);

		$tracks = array();
		foreach($index as $entry) $tracks[] = $entry->track;

		$this->renderPartial('searchresult',array('tracks'=>$tracks));

	}

	public function actionSimilar() {

		if(!isset($_REQUEST['id'])) return;

		$track_id = (int) $_REQUEST['id'];
		if(!$track_id) return;

		$track = Track::model()->with('artist')->findByPK($track_id);
		if(!$track) return;

		$request = "http://ws.audioscrobbler.com/2.0/?".
			"method=track.getsimilar&format=json".
			"&api_key=".self::LASTFM_API_KEY.
			"&artist=".rawurlencode($track->artist->name).
			"&track=".rawurlencode($track->name);

		$result = json_decode(file_get_contents($request));
		if(isset($result->similartracks->track))
			$suggestions = $result->similartracks->track;
		else return;

		$tracks = array();
		for($i=0; $i<Yii::app()->params['lastfmMaxLookups']; $i++) {
			$track = $this->fetchTrackByArtistTitle(
					$suggestions[$i]->artist->name, $suggestions[$i]->name);
			if($track) $tracks[] = $track;
		}

		$this->renderPartial('searchresult',array('tracks'=>$tracks));
	}

	public function actionCover() {

		$coverUrl = '';
		if(isset($_REQUEST['coverUrl']))
			$coverUrl = $_REQUEST['coverUrl'];

		if(!$coverUrl) return;

		$this->renderPartial('cover',array('coverUrl'=>$coverUrl));
	}

	public function actionMeta() {

		$relpath = Helpers::decodeUrl($_POST['relpath']);

		$track = $this->fetchTrackByRelpath($relpath);

		if($track) $this->renderPartial('meta',array('track'=>$track));
	}

	public function fetchTrackByRelpath($relpath) {

		if(!$relpath) return null;

		$quotedRelpath = Yii::app()->db->quoteValue($relpath);

		$criteria = new CDbCriteria;
		$criteria->with = 'file';
		$criteria->condition = "file.relpath = $quotedRelpath";

		$track = Track::model()->find($criteria);

		return $track;
	}

	public function fetchTrackByArtistTitle($artist, $title) {

		$artist=trim(strtolower($artist));
		$title=trim(strtolower($title));

		if(!$artist || !$title) return null;

		$quotedArtist = Yii::app()->db->quoteValue($artist);
		$quotedTitle = Yii::app()->db->quoteValue($title);

		$criteria = new CDbCriteria;
		$criteria->with = 'artist';
		$criteria->condition = "t.name LIKE $quotedTitle AND artist.name LIKE $quotedArtist";

		$track = Track::model()->find($criteria);

		return $track;
	}

}
