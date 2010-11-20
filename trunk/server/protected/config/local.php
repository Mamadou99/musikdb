<?php
return array(

	// database connection
	'components'=>array(
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=musikdb',
			'tablePrefix'=>'tbl_',
			'emulatePrepare' => true,
			'username' => 'musikdb',
			'password' => 'musikdb',
			'charset' => 'utf8',
		),
	),

	// application-level parameters
	'params'=>array(

		// backend only
		'accessUrl'=>'http://localhost:8888/Sites/musikdb-client/index.php/accesstoken/valid',
		'allowedExts'=>array('mp3'),
		'mediaPath'=>'/mnt/medien/SORTED',
		'exiftoolBin'=>'/usr/local/bin/exiftool',
		'ffmpegBin'=>'/opt/local/bin/ffmpeg',
		'coverFile'=>'folder.jpg',
	),
);
