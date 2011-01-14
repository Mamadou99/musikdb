<?php

class File extends CActiveRecord
{
	/**
	 * The followings are the available columns in table '{{file}}':
	 * @var integer $id
	 * @var integer $track_id
	 * @var string $name
	 * @var integer $mtime
	 * @var double $size
	 * @var integer $length
	 * @var integer $bitrate
	 * @var integer $samplerate
	 * @var string $mode
	 * @var string $format
	 * @var string $relpath
	 */

	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{file}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('track_id, name, mtime, size, length, format, relpath', 'required'),
			array('track_id, mtime, length, bitrate, samplerate', 'numerical', 'integerOnly'=>true),
			array('size', 'numerical'),
			array('name', 'length', 'max'=>255),
			array('mode', 'length', 'max'=>3),
			array('format', 'length', 'max'=>8),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, track_id, name, mtime, size, length, bitrate, samplerate, mode, relpath', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'track'=>array(self::BELONGS_TO, 'Track', 'track_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'track_id' => 'Track',
			'name' => 'Name',
			'mtime' => 'Mtime',
			'size' => 'Size',
			'length' => 'Length',
			'bitrate' => 'Bitrate',
			'samplerate' => 'Samplerate',
			'mode' => 'Mode',
			'format' => 'Format',
			'relpath' => 'Relpath',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);

		$criteria->compare('track_id',$this->track_id);

		$criteria->compare('name',$this->name,true);

		$criteria->compare('mtime',$this->mtime);

		$criteria->compare('size',$this->size);

		$criteria->compare('length',$this->length);

		$criteria->compare('bitrate',$this->bitrate);

		$criteria->compare('samplerate',$this->samplerate);

		$criteria->compare('mode',$this->mode,true);

		$criteria->compare('format',$this->format,true);

		$criteria->compare('relpath',$this->relpath,true);

		return new CActiveDataProvider('File', array(
			'criteria'=>$criteria,
		));
	}
}