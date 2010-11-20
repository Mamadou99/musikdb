<?php
return array(

	// database connection
	'components'=>array(
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=musikdb',
			'tablePrefix'=>'tbl_',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),
	),

	// application-level parameters
	'params'=>array(

		// backend only
		'accessUrl'=>'http://localhost/musikdb/index.php/accesstoken/valid',
		'allowedExts'=>array('mp3'),
		'mediaPath'=>'/mnt/mp3',
		'exiftoolBin'=>'/usr/bin/exiftool',
		'ffmpegBin'=>'/usr/bin/ffmpeg',
		'coverFile'=>'folder.jpg',

	),
);