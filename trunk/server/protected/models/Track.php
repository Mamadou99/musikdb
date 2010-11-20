<?php

class Track extends CActiveRecord
{
	/**
	 * The followings are the available columns in table '{{track}}':
	 * @var integer $id
	 * @var integer $artist_id
	 * @var integer $release_id
	 * @var string $name
	 * @var integer $number
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
		return '{{track}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('artist_id, release_id, name, number', 'required'),
			array('artist_id, release_id, number', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, artist_id, release_id, name, number', 'safe', 'on'=>'search'),
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
			'file'=>array(self::HAS_ONE, 'File', 'track_id'),
			'artist'=>array(self::BELONGS_TO, 'Artist', 'artist_id'),
			'release'=>array(self::BELONGS_TO, 'Release', 'release_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Id',
			'artist_id' => 'Artist',
			'release_id' => 'Release',
			'name' => 'Name',
			'number' => 'Number',
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

		$criteria->compare('artist_id',$this->artist_id);

		$criteria->compare('release_id',$this->release_id);

		$criteria->compare('name',$this->name,true);

		$criteria->compare('number',$this->number);

		return new CActiveDataProvider('Track', array(
			'criteria'=>$criteria,
		));
	}
}