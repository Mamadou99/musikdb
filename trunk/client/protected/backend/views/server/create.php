<?php
$this->breadcrumbs=array(
	'Servers'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Server', 'url'=>array('index')),
	array('label'=>'Manage Server', 'url'=>array('admin')),
);
?>

<h1>Create Server</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>