<?php
$this->breadcrumbs=array(
	'Release Log Entries'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ReleaseLogEntry', 'url'=>array('index')),
	array('label'=>'Create ReleaseLogEntry', 'url'=>array('create')),
	array('label'=>'View ReleaseLogEntry', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ReleaseLogEntry', 'url'=>array('admin')),
);
?>

<h1>Update ReleaseLogEntry <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>