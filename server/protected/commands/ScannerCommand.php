<?php
class ScannerCommand extends CConsoleCommand {

	const UPDATE_INTERVAL_MS = 250;

	public $defaultAction = 'help';
	public $viewPath;

	private $timeStarted = 0;
	private $timePassed = 0;
	private $filesToScan = 0;
	private $filesScanned = 0;
	private $filesNew = 0;
	private $filesFailed = 0;
	private $currentFile = '';

	private $progressUpdated = 0.0;
	private $logFileHandle;

	public function ScannerCommand() {

		$this->viewPath = Yii::app()->basePath.'/views/commands/scanner/';

	}

	public function actionHelp() {

		$this->renderFile($this->viewPath.'help.php');
	}

    public function actionFull($logfile='') {

    	$this->createDump();
    	die();

		if($logfile) $this->openLogFile($logfile);
		$this->doScan(true);
		$this->closeLogFile();
		echo "\n";
    }

    public function actionUpdate($logfile='') {

    	if($logfile) $this->openLogFile($logfile);
    	$this->doScan(false);
    	$this->closeLogFile();
    	echo "\n";
    }

    public function actionExport() {

    	echo "Creating dump ... ";
    	if($this->createDump()) {
    		echo "DONE\n";
    	}
    	else {
    		echo "FAILED\n";
    	}
    	echo "\n";
    }

    /**
     * Do the scan
     *
     * @param boolean $full are we doing a full scan or not?
     */
    private function doScan($full) {

		$this->timeStarted = time();
		$this->timePassed = 0;

		// Create file list
		echo "\nCreating file list ... ";
		$tmpfile = $this->createFileList(Yii::app()->params['mediaPath']);
		$this->filesToScan = $this->countFiles($tmpfile);
		if(!$this->filesToScan)
			die("\nNo files in media directory. Please check your config!\n\n");
		echo "DONE\n";

		if($full) {
			// Clear collection
			echo "\nClearing current collection ... ";
			$this->clearCollection();
			echo "DONE\n";
		}

		// Process file list
		$this->processList($tmpfile, $full);
		$this->updateProgress(true);

		// Rebuild index
		echo "\nRebuilding the search index ... ";
		$this->rebuildIndex();
		echo "DONE\n";
    }

    /**
     * Redraw the progress view
     * .
     */
    private function updateProgress($force=false) {

    	$microtime = microtime(true);

    	// execute this function only every x milliseconds
    	if(!$force) {
	    	if($microtime-$this->progressUpdated < self::UPDATE_INTERVAL_MS/1000
	    			&& $this->progressUpdated > 0) return;
    	}

    	$this->progressUpdated = $microtime;
		$this->timePassed = time()-$this->timeStarted;

		$finished = false;
		if($this->filesToScan == $this->filesScanned) $finished = true;

		system("clear");
		$this->renderFile($this->viewPath.'progress.php',array(
				'timeStarted'=>strftime("%c",$this->timeStarted),
				'timePassed'=>$this->sec2hms($this->timePassed),
				'filesToScan'=>$this->filesToScan,
				'filesScanned'=>$this->filesScanned,
				'filesNew'=>$this->filesNew,
				'filesFailed'=>$this->filesFailed,
				'currentFile'=>$this->currentFile,
				'finished'=>$finished,
			));
    }

   /**
    * Convert seconds into HH:MM:SS format
    *
    * @param integer $sec Seconds
    */
    private function sec2hms($sec) {

    	$hms = '';

    	$hours = intval(intval($sec) / 3600);
    	$hms.= str_pad($hours, 2, "0", STR_PAD_LEFT).":";
    	$minutes = intval(($sec / 60) % 60);
    	$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT).":";
    	$seconds = intval($sec % 60);
    	$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

    	return $hms;
    }

	/**
	 * Create a list of the files we want to add
	 */
	private function createFileList($baseDir) {

		if(!is_readable($baseDir)) return;

		$tmpfile = tempnam(sys_get_temp_dir(), 'musikdbscanner_');

		// Create and execute unix find command
		// exclude hidden files and directories
		$nameParams = array();
		foreach(Yii::app()->params['allowedExts'] as $ext) $nameParams[] = "-iname \"*.$ext\" ! -iname \".*\"";
		$command = "cd ".escapeshellarg($baseDir)."; ".
			'find -L . \( ! -regex \'.*/\..*\' \) -type f \\( '.
			implode(' -o ', $nameParams)." \\) > ".escapeshellarg($tmpfile);

		shell_exec($command);

		return $tmpfile;
	}

	/**
	 * Count file entries in tmpfile
	 */
	private function countFiles($tmpfile) {

		return (int) shell_exec("sed -n '$=' ".escapeshellarg($tmpfile));
	}

	/**
	 * Add all files named in tmpfile
	 *
	 * @param string $tmpfile File that contains the filelist
	 * @param boolean $full Is this a fullscan?
	 */
	private function processList($tmpfile, $full=false) {

		$baseDir = Yii::app()->params['mediaPath'];

		for($i=0; $i < $this->filesToScan; $i++) {

			$command = "cd ".escapeshellarg($baseDir)."; ".
				"sed -ne '".($this->filesScanned+1)."p' ".escapeshellarg($tmpfile);
			$relpath = ltrim(rtrim(shell_exec($command)),'./');

			$this->addFile($relpath, $full);
			$this->filesScanned++;
			$this->updateProgress();
		}
	}

	/**
	 * Adds a file to the database
	 *
	 * @param string $relpath
	 * @param boolean $full Is this a fullscan?
	 */
	private function addFile($relpath, $full) {

		$this->currentFile = $relpath;

		$fullpath = Yii::app()->params['mediaPath'].'/'.$relpath;
		$filemtime = filemtime($fullpath);
		$filesize = filesize($fullpath);

		// When running an update scan, skip existing files
		if(!$full) {

			// Try to fetch file model
			$quotedRelpath = Yii::app()->db->quoteValue($relpath);
			$criteria = new CDbCriteria;
			$criteria->condition = "relpath = $quotedRelpath";
			$model = File::model()->find($criteria);

			// Check if the file was modified
			if($model && $model->mtime==$filemtime) {
				return;
			}
		}

		// Fetch meta data
		$meta = $this->getMetaData($relpath);

		// Artist
		$artist = Artist::model()->findByAttributes(array('name'=>$meta['artist']));
		if($artist===null) {
			$artist = new Artist;
			$artist->name = $meta['artist'];
			if(!$artist->save()) {
				$this->reportFileError($relpath, $artist->getErrors());
				return;
			}
		}

		// Release
		$release = Release::model()->findByAttributes(array('name'=>$meta['album']));
		if($release===null) {
			$release = new Release;
			$release->artist_id = $artist->id;
			$release->name = $meta['album'];
			$release->year = $meta['year'];
			if(!$release->save()) {
				$this->reportFileError($relpath, $release->getErrors());
				return;
			}
		}

		// Track
		$track = new Track;
		$track->artist_id = $artist->id;
		$track->release_id = $release->id;
		$track->name = $meta['title'];
		$track->number = (int) $meta['number'];
		if(!$track->save()) {
			$this->reportFileError($relpath, $track->getErrors());
			return;
		}

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
		$file->format = $meta['format'];
		$file->relpath = $relpath;
		if($file->save()) $this->filesNew++;
		else $this->reportFileError($relpath, $file->getErrors());
	}

	/**
	 *
	 */
	private function reportFileError($relpath, $errorArray) {

		$this->filesFailed++;

		$details = str_replace("\n","",print_r($errorArray, true));
		$this->logError("Could not add file \"$relpath\" ($details)");
	}

	/**
	 * Get meta data using external library
	 */
	private function getMetaData($relpath) {

		$fullpath = Yii::app()->params['mediaPath'].'/'.$relpath;

		// Analyze file using getID3 class
		require_once Yii::app()->basePath.'/../3rd-party/getid3/getid3.php';
		$engine = new getID3;
		$fileinfo = $engine->analyze($fullpath);

		// audio info

		$meta['length'] = (isset($fileinfo['playtime_seconds'])) ? (int) round($fileinfo['playtime_seconds']) : 0;
		$meta['bitrate'] = (isset($fileinfo['audio']['bitrate'])) ? (int) $fileinfo['audio']['bitrate'] : null;
		$meta['samplerate'] = (isset($fileinfo['audio']['sample_rate'])) ? (int) $fileinfo['audio']['sample_rate'] : null;
		$meta['mode'] = (isset($fileinfo['audio']['bitrate_mode'])) ? $fileinfo['audio']['bitrate_mode'] : null;
		$meta['format'] = (isset($fileinfo['audio']['dataformat'])) ? $fileinfo['audio']['dataformat'] : null;

		// tags

		// always prefer id3v2 (if not found use the first tag which is there)
		$tags = array();
		if(isset($fileinfo['tags']['id3v2']))
			$tags = $fileinfo['tags']['id3v2'];
		elseif(isset($fileinfo['tags']) && count($fileinfo['tags']))
			$tags = reset($fileinfo['tags']);

		// Split Track/Total into two variables
		$meta['total'] = 0;
		$meta['number'] = 0;
		if(isset($tags['track_number'])) {
			$trackParts = explode('/',$tags['track_number'][0]);
			if(isset($trackParts[0])) $meta['number'] = (int) $trackParts[0];
			if(isset($trackParts[1])) $meta['total'] = (int) $trackParts[1];
		}

		$meta['artist'] = (isset($tags['artist'])) ? $tags['artist'][0] : '';
		$meta['title'] = (isset($tags['title'])) ? $tags['title'][0] : '';
		$meta['album'] = (isset($tags['album'])) ? $tags['album'][0] : '';
		$meta['year'] = (isset($tags['year'])) ? $tags['year'][0] : 0;

		return $meta;
	}

	/**
	 * Rebuild the search index
	 *
	 */
	private function rebuildIndex() {

		Metastring::model()->deleteAll();

		$total = Track::model()->count();
		$count = 0;
		$offset = 0;
		$limit = 500;

		while($offset < $total) {

			$offset = $count * $limit;
			$count++;

			$criteria = new CDbCriteria();
			$criteria->limit = $limit;
			$criteria->offset = $offset;

			$tracks = Track::model()->findAll($criteria);

			foreach($tracks as $track) {

				$artist = Artist::model()->findByPK($track->artist_id);
				$release = Release::model()->findByPK($track->release_id);

				$metastring = new Metastring;
				$metastring->track_id = $track->id;
				$metastring->meta = $artist->name.' '.$release->name.' '.$track->name.' ';
				$metastring->save();
			}

		}
	}

	/**
	 * Creates a DB dump containing the collection tables
	 *
	 * @return boolean true on success
	 */
	private function createDump() {

		if(!is_executable(Yii::app()->params['mysqldumpBin'])) return false;

		$connectionStr = Yii::app()->db->connectionString;
		if(!substr($connectionStr,0,5) == 'mysql') return false;

		// tables needed for the collection
		$tables = array('artist','file','metastring','release','track');
		$tablePrefix = Yii::app()->db->tablePrefix;

		// parameters for mysqldump
		$commandParamsArr = array(
			'username' => array('-u',''),
			'password' => array('-p',''),
			'host' => array('-h',''),
			'dbname' => array('',''),
		);

		// convert connectionString to an array
		$params=explode(';',substr($connectionStr,6));
		foreach($params as $param) {
			$kv = explode('=',$param);
			$commandParamsArr[$kv[0]][1]=$kv[1];
		}

		$commandParamsArr['username'][1] = Yii::app()->db->username;
		$commandParamsArr['password'][1] = Yii::app()->db->password;

		// create parameter string
		$commandParams = '';
		foreach($commandParamsArr as $param) {
			if($param[1]) $commandParams.= ' '.$param[0].trim(escapeshellarg($param[1]));
		}
		$commandParams.= " $tablePrefix".implode(" $tablePrefix",$tables);
		$filename = 'musikdbcollection_'.date('Y-d-m_H-i-s').'.sql.gz';

		$command = Yii::app()->params['mysqldumpBin']." --add-drop-table $commandParams | ".
			"gzip -c > $filename";

		shell_exec($command);
		if(!@filesize($filename) > 0) return false;

		return true;
	}

	/**
	 * Clear the whole collection
	 */
	private function clearCollection() {

		File::model()->deleteAll();
		Track::model()->deleteAll();
		Release::model()->deleteAll();
		Artist::model()->deleteAll();
		Metastring::model()->deleteAll();
	}


	/**
	 * Try to create the logfile
	 * .
	 * @param string $logfile path
	 */
	private function openLogFile($logfile) {

		$handle = @fopen($logfile, 'w+');
		if(!$handle) die("\nLog file could not be written.\n");
		$this->logFileHandle = $handle;
	}

	/**
	 * Closes the log file (if opened before)
	 *
	 */
	private function closeLogFile() {

		if($this->logFileHandle) fclose($this->logFileHandle);
	}

	/**
	 * Log an error to the log file (if any)
	 *
	 * @param string $msg error message
	 */
	private function logError($msg) {

		if(!$this->logFileHandle) return;

		$date = date("d.m.Y H:i:s");
		fwrite($this->logFileHandle, "[$date] ERROR: $msg\n");
	}


}