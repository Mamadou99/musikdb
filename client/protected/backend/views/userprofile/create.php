<?php
$this->breadcrumbs=array(
	'Userprofiles'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Userprofile', 'url'=>array('index')),
	array('label'=>'Manage Userprofile', 'url'=>array('admin')),
);
?>

<h1>Create Userprofile</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>