<?php
$this->breadcrumbs=array(
	'Servers'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Server', 'url'=>array('index')),
	array('label'=>'Create Server', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('server-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Servers</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'server-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'name',
		'baseUrl',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
