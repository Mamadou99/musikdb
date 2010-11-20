<?php

class CollectionController extends BackendController
{
	/**
	 * @var string the default layout for the views. Defaults to 'column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='column2';
	public $defaultAction='rescan';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_model;

	private $currentFile=0;
	private $totalFiles=0;
	private $currentProgress=0;
	private $verbose=0;

	/**
	 * Displays a particular model.
	 */
	public function actionRescan()
	{
		header("Content-Type: text/plain");
		setlocale(LC_ALL, "en_US.UTF-8"); // needed for escapeshellargs() to work correctly

		if($_SERVER['HTTP_USER_AGENT'] != 'scan') {
			die("You have to set your User Agent String to \"scan\" in order to run this script.\n".
				"Check if there is already another instance running before doing so.\n");
		}
		if(isset($_GET['verbose'])) $this->verbose = (int) $_GET['verbose'];

		$dir = '/';

		echo "Creating list ... ";
		$this->flush();
		$tmpfile = $this->createFileList($dir);
		echo "Done!\n";

		echo "Counting entries ... ";
		$this->flush();
		$this->totalFiles = $this->countFiles($tmpfile);
		echo $this->totalFiles."\n";
		if($this->totalFiles==0) die("No files to scan. Stopping.\n");

		if(isset($_GET['full'])) {
			echo "Clearing collection ... ";
			$this->flush();
			$this->clearCollection();
			echo "Done!\n";
		}

		echo "Processing list ...\n";
		$this->flush();
		$this->processList($tmpfile);

		echo "Added ".Track::model()->count()." Tracks.\n";

		echo "NOTE: You may want to run collection/rebuildindex now in order ".
			"to update the fulltext search index.";
	}


	public function actionRebuildindex() {

		echo "Rebuilding index ... ";
		$this->flush();
		$this->rebuildIndex();
		echo "Done!";
	}


	private function rebuildIndex() {

		Index::model()->deleteAll();

		$total = Track::model()->count();
		$count = 0;
		$offset = 0;
		$limit = 500;

		while($offset < $total) {

			// Reset time limit
			set_time_limit(30);

			$offset = $count * $limit;
			$count++;

			$criteria = new CDbCriteria();
			$criteria->limit = $limit;
			$criteria->offset = $offset;

			$tracks = Track::model()->findAll($criteria);

			foreach($tracks as $track) {

				$artist = Artist::model()->findByPK($track->artist_id);
				$release = Release::model()->findByPK($track->release_id);

				$index = new Index;
				$index->track_id = $track->id;
				$index->meta = $artist->name.' '.$release->name.' '.$track->name.' ';
				$index->save();
			}

		}
	}


	/**
	 * Create a list of the files we want to add
	 */
	private function createFileList($relpath) {

		$baseDir = Yii::app()->params['mediaPath'];
		$tmpfile = tempnam(sys_get_temp_dir(), 'collectionfilelist_');

		// TODO: Don't hardcode extension
		shell_exec("cd ".escapeshellarg($baseDir)."; ".
			"find ./".escapeshellarg($relpath)." -type f -name *.mp3 > ".
			escapeshellarg($tmpfile));

		return $tmpfile;
	}


	/**
	 * Add all files named in tmpfile
	 */
	private function processList($tmpfile) {

		$baseDir = Yii::app()->params['mediaPath'];

		for($i=0; $i<$this->totalFiles; $i++) {

			// Reset time limit
			set_time_limit(30);

			$this->currentFile++;

			$relpath = shell_exec("cd ".escapeshellarg($baseDir)."; ".
				"sed -ne '".$this->currentFile."p' ".escapeshellarg($tmpfile));
			$relpath = ltrim(rtrim($relpath),'./');

			$this->addFile($relpath);
			$this->updateProgress();
		}
	}


	/**
	 * Add a file to the collection
	 */
	private function addFile($relpath) {

		$fullpath = Yii::app()->params['mediaPath'].'/'.$relpath;

		$filemtime = filemtime($fullpath);
		$filesize = filesize($fullpath);

		// Try to fetch file model
		$quotedRelpath = Yii::app()->db->quoteValue($relpath);
		$criteria = new CDbCriteria;
		$criteria->condition = "relpath = $quotedRelpath";
		$model = File::model()->find($criteria);

		// Check if the file was modified
		if($model && $model->mtime==$filemtime) {
			if($this->verbose >= 2) {
				$this->log("Skipping file {$this->currentFile}: $relpath");
			}
			return;
		}

		// Log
		if($this->verbose >= 1) {
			$this->log("Adding file {$this->currentFile}: $relpath");
		}

		$meta = $this->getMetaData($relpath);

		// Artist
		$artist = Artist::model()->findByAttributes(array('name'=>$meta['artist']));
		if($artist===null) {
			$artist = new Artist;
			$artist->name = $meta['artist'];
			$artist->save();
		}

		// Release
		$release = Release::model()->findByAttributes(array('name'=>$meta['album']));
		if($release===null) {
			$release = new Release;
			$release->artist_id = $artist->id;
			$release->name = $meta['album'];
			$release->year = $meta['year'];
			$release->save();
		}

		// Track
		$track = new Track;
		$track->artist_id = $artist->id;
		$track->release_id = $release->id;
		$track->name = $meta['title'];
		$track->number = (int) $meta['number'];
		$track->save();

		// File
		$file = new File;
		$file->track_id = $track->id;
		$file->name = basename($relpath);
		$file->mtime = (int) $filemtime;
		$file->size = (int) $filesize;
		$file->length = (int) $meta['length'];
		$file->bitrate = (int) $meta['bitrate'];
		$file->samplerate = (int) $meta['samplerate'];
		$file->mode = $meta['mode'];
		$file->relpath = $relpath;
		$file->save();
	}


	/**
	 * Count file entries in tmpfile
	 */
	public function countFiles($tmpfile) {

		return (int) shell_exec("sed -n '$=' ".escapeshellarg($tmpfile));
	}


	/**
	 * Get meta data using exiftools
	 */
	public function getMetaData($relpath) {

		$tags = array(
			'AudioBitrate',
			'SampleRate',
			'Duration',
			'Artist',
			'Title',
			'Album',
			'Track',
			'Year'
		);

		$fullpath = Yii::app()->params['mediaPath'].'/'.$relpath;
		$options = '-j -'.implode($tags, ' -');
		$command = Yii::app()->params['exiftoolBin']." $options ".' '.escapeshellarg($fullpath);
		if($this->verbose==2) $this->log("shell_exec: $command");
		$data = reset(json_decode(shell_exec($command)));
		$meta = array();

		// Convert length from MM:SS format into pure seconds
		$length_parts = explode(':',$data->Duration);
		$minutes = (int) $length_parts[0];
		$seconds = (int) $length_parts[1];
		$meta['length'] = $minutes*60 + $seconds;

		// Decide wether it's CBR or VBR
		if((int) $data->AudioBitrate % 32000 == 0)
			$meta['mode'] = 'cbr';
		else
			$meta['mode'] = 'vbr';

		// Split Track/Total into two variables
		list($meta['number'], $meta['total']) = explode('/',$data->Track);

		$meta['bitrate'] = (int) $data->AudioBitrate;
		$meta['samplerate'] = (int) $data->SampleRate;
		$meta['artist'] = $data->Artist;
		$meta['title'] = $data->Title;
		$meta['album'] = $data->Album;
		$meta['year'] = $data->Year;

		return $meta;
	}


	/**
	 * Clears the whole collection
	 */
	public function clearCollection() {

		File::model()->deleteAll();
		Track::model()->deleteAll();
		Release::model()->deleteAll();
		Artist::model()->deleteAll();
	}


	/**
	 * Show progress
	 */
	private function updateProgress() {

		$percent = ($this->currentFile / ($this->totalFiles+1)) * 100;
		if($this->currentProgress < floor($percent)) {
			$this->currentProgress = floor($percent);
			echo "   ... {$this->currentProgress}%\n";
		}
		$this->flush();
	}


	/*
	 * Show log
	 */
	private function log($message) {

		echo date("Y-m-d H:i:s")."  ".trim($message)."\n";
		$this->flush();
	}


	/**
	 * Do a real flush
	 */
	public function flush() {
		ob_flush();
		flush();
	}

}
