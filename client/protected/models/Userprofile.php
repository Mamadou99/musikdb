<?php

/**
 * This is the model class for table "{{userprofile}}".
 *
 * The followings are the available columns in table '{{userprofile}}':
 * @property integer $id
 * @property integer $user_id
 * @property integer $server_id
 * @property integer $crossfadeTime
 * @property integer $transcodingBitrate
 * @property boolean $alwaysTranscode
 * @property integer $openPopup
 *
 * The followings are the available model relations:
 */
class Userprofile extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return Userprofile the static model class
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
		return '{{userprofile}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_id, openPopup', 'required'),
			array('user_id, server_id, crossfadeTime, transcodingBitrate, openPopup', 'numerical', 'integerOnly'=>true),
			array('crossfadeTime', 'default', 'setOnEmpty'=>true, 'value'=>Yii::app()->params['crossfadeTime']),
			array('alwaysTranscode, openPopup', 'boolean'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, user_id, server_id, crossfadeTime, transcodingBitrate, openPopup', 'safe', 'on'=>'search'),
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
			'user'=>array(self::BELONGS_TO, 'User', 'user_id'),
			'server'=>array(self::BELONGS_TO, 'Server', 'server_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'server_id' => 'Server',
			'crossfadeTime' => 'Crossfade Time (ms)',
			'transcodingBitrate' => 'Transcoding Bitrate',
			'alwaysTranscode' => 'Always transcode',
			'openPopup' => 'Open Popup',
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
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('server_id',$this->server_id);
		$criteria->compare('crossfadeTime',$this->crossfadeTime);
		$criteria->compare('transcodingBitrate',$this->transcodingBitrate);
		$criteria->compare('alwaysTranscode',$this->alwaysTranscode);
		$criteria->compare('openPopup',$this->openPopup);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}