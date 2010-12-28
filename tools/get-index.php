#!/usr/bin/php
<?php

if(count($argv) != 4) {
	echo "Usage: get-index.php [PATH] [album|va] [OWNER_ID]\n\n";
	exit;
}

require_once('MusikDB.class.php');
$musikDB = new MusikDB($argv[1], $argv[2], $argv[3]);
$musikDB->createIndex();
$index = $musikDB->getIndexCSV();

print_r($index);

?>
