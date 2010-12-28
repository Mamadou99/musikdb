<?php

class MusikDB {

	private $path = "";
	private $mode = "";
	private $ownerID = 0;
	private $date = "";
	
	private $index = array();
	private $indexCSV = "";
	
	private $mp3infoCmd = 'mp3info -r a -p "%r\t" *.mp3';

	public function MusikDB($path, $mode, $ownerID) {

		if(!is_readable($path)) {
			exit;
		}
		if($mode != "album" && $mode != "va") {
			exit;
		}

		$this->path = $path;
		$this->mode = $mode;
		$this->ownerID = $ownerID;
		$this->date = date("Y-m-d");
	}


	public function createIndex() {

		// Stop if path isn't set
		if(!$this->path) return;

		$dirlist = $this->recursiveDirScan($this->path);
		$sep = ",";


		// Create a list of artists and albums which can be
		// linked using an ID

		$letters = array();
		$artists = array();
		$albums = array();
		$bitrates = array();
		$letterID = 0;
		$artistID = 0;
		$albumID = 0;

		$csv = "";
		
		/*
		 * Album mode
		 */
		if($this->mode == 'album') {
			foreach($dirlist as $letter) {
				foreach($letter['content'] as $artist) {
					$artistID++;
					$letters[$artistID] = $letter['name'];
					$artists[$artistID] = $artist['name'];
					foreach($artist['content'] as $album) {
						$albumID++;
						$albums[$artistID][$albumID] = $album['name'];
						$bitrates[$artistID][$albumID] = $this->getAvgBitrate(
							'Alben'.DIRECTORY_SEPARATOR.
							$letter['name'].DIRECTORY_SEPARATOR.
							$artist['name'].DIRECTORY_SEPARATOR.
							$album['name']
						);
					}
				}
			}
			asort($artists);
			asort($albums);

			// Create CSV
			foreach($artists as $artistID => $artist) {
				foreach($albums[$artistID] as $albumID => $album) {
					$csv.=
						'"Album"'.$sep.
						'"'.$this->esc($artist).'"'.$sep.
						'"'.$this->esc($album).'"'.$sep.
						$this->ownerID.$sep.
						$this->date.$sep.
						$bitrates[$artistID][$albumID]."\n";
				}
			}
		}

		/*
		 * VA mode
		 */
		if($this->mode == 'va') {
			foreach($dirlist as $letter) {
				foreach($letter['content'] as $album) {
					$albumID++;
					$letters[$albumID] = $letter['name'];
					$albums[$albumID] = $album['name'];
					$bitrates[$albumID] = $this->getAvgBitrate(
						'VA'.DIRECTORY_SEPARATOR.
						$letter['name'].DIRECTORY_SEPARATOR.
						$album['name']
					);
				}
			}
			asort($albums);

			// Create CSV
			foreach($albums as $albumID => $album) {
				$csv.=
					'"VA"'.$sep.
					'""'.$sep.
					'"'.$this->esc($album).'"'.$sep.
					$this->ownerID.$sep.
					$this->date.$sep.
					$bitrates[$albumID]."\n";
			}
		}
		
		
		$this->indexCSV = $csv;
	}


	public function getIndexCSV() {
		return $this->indexCSV;
	}


	private function esc($string) {
	
		$string = str_replace('"', '\\"', $string);
		$string = str_replace('\\', '\\\\"', $string);
		
		return $string;
	}

	private function recursiveDirScan($dir) {
		
		$dirlist = opendir($dir);
		while ($file = readdir ($dirlist)) {
			if (substr($file, 0, 1) != '.') {
				$newpath = $dir.'/'.$file;
				$level = explode('/',$newpath);
				if (is_dir($newpath)) {
					$mod_array[] = array(
						'level' => count($level)-1,
						'path' => $newpath,
						'name' => end($level),
						'type' => 'dir',
						'mod_time' => filemtime($newpath),
						'content' => $this->recursiveDirScan($newpath)
					);
				}
				else {
					$mod_array[] = array(
						'level' => count($level)-1,
						'path' => $newpath,
						'name' => end($level),
						'type' => 'file',
						'mod_time' => filemtime($newpath),
						'size' => filesize($newpath)
					);
				}
			}
		}
		closedir($dirlist);
		return $mod_array;
	}
	
	private function getAvgBitrate($dir) {
	
		$return = exec($cmd="cd \"$dir\" && ".$this->mp3infoCmd);
		$values = explode("\t",trim($return));

		$avgBitrate = 0;
		$total = 0;
		foreach($values as $value) {
			$total+= (float) $value;
		}
		$avgBitrate = round($total/count($values),3)*1000;
		
		return $avgBitrate;
	}
	
}

?>
