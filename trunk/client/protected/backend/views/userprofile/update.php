<?php
$this->breadcrumbs=array(
	'Userprofiles'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Userprofile', 'url'=>array('index')),
	array('label'=>'Create Userprofile', 'url'=>array('create')),
	array('label'=>'View Userprofile', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Userprofile', 'url'=>array('admin')),
);
?>

<h1>Update Userprofile <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>