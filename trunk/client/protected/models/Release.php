<?php

class Release extends CActiveRecord
{
	/**
	 * The followings are the available columns in table '{{release}}':
	 * @var integer $id
	 * @var integer $artist_id
	 * @var string $name
	 * @var string $year
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
		return '{{release}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('artist_id, name', 'required'),
			array('artist_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			array('year', 'length', 'max'=>4),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, artist_id, name, year', 'safe', 'on'=>'search'),
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
			'track'=>array(self::HAS_MANY, 'Track', 'release_id'),
			'artist'=>array(self::BELONGS_TO, 'Artist', 'artist_id'),
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
			'name' => 'Name',
			'year' => 'Year',
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

		$criteria->compare('name',$this->name,true);

		$criteria->compare('year',$this->year,true);

		return new CActiveDataProvider('Release', array(
			'criteria'=>$criteria,
		));
	}
}