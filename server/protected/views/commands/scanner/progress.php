---------------------------------------------------------------------
<?php
if($finished) $titleString = 'COLLECTION SCAN FINISHED';
else $titleString = 'COLLECTION SCAN IN PROGRESS';

echo '|'.str_pad($titleString ,67, " ", STR_PAD_BOTH)."|\n" ?>
---------------------------------------------------------------------

    Started at: <?php echo $timeStarted."\n" ?>
  Time elapsed: <?php echo $timePassed."\n" ?>

 Files to scan: <?php echo $filesToScan."\n" ?>
 Files scanned: <?php
 	echo $filesScanned;
 	echo " (".number_format(round($filesScanned/$filesToScan*100,2),2)."%)\n";
?>
     New files: <?php echo $filesNew."\n" ?>

  Current file: <?php echo $currentFile."\n" ?>

