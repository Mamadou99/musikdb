<?php
$this->breadcrumbs=array(
	'Release Log Entries',
);

$this->menu=array(
	array('label'=>'Create ReleaseLogEntry', 'url'=>array('create')),
	array('label'=>'Manage ReleaseLogEntry', 'url'=>array('admin')),
	array('label'=>'Import ReleaseLogEntry', 'url'=>array('importcsv')),
);
?>

<h1>Release Log Entries</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
