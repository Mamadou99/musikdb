<?php
$this->breadcrumbs=array(
	'Release Log Entries'=>array('index'),
	$model->title,
);

$this->menu=array(
	array('label'=>'List ReleaseLogEntry', 'url'=>array('index')),
	array('label'=>'Create ReleaseLogEntry', 'url'=>array('create')),
	array('label'=>'Update ReleaseLogEntry', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete ReleaseLogEntry', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage ReleaseLogEntry', 'url'=>array('admin')),
);
?>

<h1>View ReleaseLogEntry #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'type',
		'title',
		'artist',
		'user_id',
		'date',
		'avg_bitrate',
		'musicbrainz_albumid',
	),
)); ?>
