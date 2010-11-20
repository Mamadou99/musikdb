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

		'transcodingBitrates'=>array(
			array('ab'=>0,'desc'=>'(unrestricted)'),
			array('ab'=>192000,'desc'=>'192 kbit/s'),
			array('ab'=>160000,'desc'=>'160 kbit/s'),
			array('ab'=>128000,'desc'=>'128 kbit/s'),
			array('ab'=>96000,'desc'=>'96 kbit/s'),
			array('ab'=>64000,'desc'=>'64 kbit/s (YouTube party)'),
		),
		'accesstokenValidityPeriod'=>1860,
		'accesstokenRefreshPeriod'=>1800,
		'lastfmMaxLookups'=>50,
		'searchLimit'=>50,
		'crossfadeTime'=>8000,
	),
);
