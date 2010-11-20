<?php
$this->breadcrumbs=array(
	'Release Log Entries'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List ReleaseLogEntry', 'url'=>array('index')),
	array('label'=>'Manage ReleaseLogEntry', 'url'=>array('admin')),
);
?>

<h1>Create ReleaseLogEntry</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>