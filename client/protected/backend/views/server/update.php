<?php
$this->breadcrumbs=array(
	'Servers'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Server', 'url'=>array('index')),
	array('label'=>'Create Server', 'url'=>array('create')),
	array('label'=>'View Server', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Server', 'url'=>array('admin')),
);
?>

<h1>Update Server <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>