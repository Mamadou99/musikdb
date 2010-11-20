<?php
$this->breadcrumbs=array(
	'Userprofiles',
);

$this->menu=array(
	array('label'=>'Create Userprofile', 'url'=>array('create')),
	array('label'=>'Manage Userprofile', 'url'=>array('admin')),
);
?>

<h1>Userprofiles</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
