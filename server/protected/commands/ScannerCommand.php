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
	private $currentFile = '';

	private $progressUpdated = 0.0;

	public function ScannerCommand() {

		$this->viewPath = 'views'.DIRECTORY_SEPARATOR.'commands'.
			DIRECTORY_SEPARATOR.'scanner'.DIRECTORY_SEPARATOR;

		// check if exiftool is available
		if(!is_executable(Yii::app()->params['exiftoolBin']))
			die("\nexiftool is not available. Please check your config!\n\n");
	}

	public function actionHelp() {

		$this->renderFile($this->viewPath.'help.php');
	}

    public function actionFull() {

		$this->doScan(true);
		echo "\n";
    }

    public function actionUpdate() {

    	$this->doScan(false);
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

    	// execute this function only every x milliseconds
    	if(!$force) {
	    	$microtime = microtime(true);
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

		$tmpfile = tempnam(sys_get_temp_dir(), 'collectionfilelist_');

		// TODO: Don't hardcode extension
		$command = "cd ".escapeshellarg($baseDir)."; find -L . -type f -name *.mp3 > ".
			escapeshellarg($tmpfile);
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

		$this->filesNew++;
	}

	/**
	 * Get meta data using exiftool
	 */
	private function getMetaData($relpath) {

		$fullpath = Yii::app()->params['mediaPath'].'/'.$relpath;

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

		$options = '-j -'.implode($tags, ' -');
		$command = Yii::app()->params['exiftoolBin']." $options ".' '.escapeshellarg($fullpath);
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
	 * Rebuild the search index
	 * .
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
	 * Clears the whole collection
	 */
	private function clearCollection() {

		File::model()->deleteAll();
		Track::model()->deleteAll();
		Release::model()->deleteAll();
		Artist::model()->deleteAll();
	}


}