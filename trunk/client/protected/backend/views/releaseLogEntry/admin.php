<?php
$this->breadcrumbs=array(
	'Release Log Entries'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List ReleaseLogEntry', 'url'=>array('index')),
	array('label'=>'Create ReleaseLogEntry', 'url'=>array('create')),
	array('label'=>'Import ReleaseLogEntry', 'url'=>array('importcsv')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('release-log-entry-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Release Log Entries</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'release-log-entry-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'type',
		'title',
		'artist',
		'user_id',
		'date',
		'avg_bitrate',
		/*
		'musicbrainz_albumid',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>