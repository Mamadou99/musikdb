<?php

/**
 * This is the model class for table "{{releaselogentry}}".
 *
 * The followings are the available columns in table '{{releaselogentry}}':
 * @property integer $id
 * @property string $type
 * @property string $title
 * @property string $artist
 * @property integer $user_id
 * @property string $date
 * @property integer $avg_bitrate
 * @property string $musicbrainz_albumid
 *
 * The followings are the available model relations:
 */
class ReleaseLogEntry extends CActiveRecord
{
	public $_delete_cmd;

	/**
	 * Returns the static model of the specified AR class.
	 * @return ReleaseLogEntry the static model class
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
		return '{{releaselogentry}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, title, user_id, date', 'required'),
			array('user_id, avg_bitrate', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>5),
			array('title, artist', 'length', 'max'=>255),
			array('musicbrainz_albumid', 'length', 'max'=>48),
			array('title','releaseLogEntryUnique'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, type, title, artist, user_id, date, avg_bitrate, musicbrainz_albumid', 'safe', 'on'=>'search'),
		);
	}

	public function releaseLogEntryUnique($attribute, $params)
	{
		if($this->artist===null) {
			$model=ReleaseLogEntry::model()->find('title=:title',
				array(':title'=>$this->title));
		}
		else {
			$model=ReleaseLogEntry::model()->find('title=:title AND artist=:artist',
				array(':title'=>$this->title, ':artist'=>$this->artist));
		}

		if($model) {
			$this->addError('title','This release is already in the database: <em>'.
				"{$model->artist} &ndash; {$model->title} (ID: {$model->id})</em>");
			$this->_delete_cmd='rm -r "./'.strtoupper(substr($model->artist,0,1)).
				'/'.$model->artist.'/'.$model->title.'"';
		}
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => 'Type',
			'title' => 'Title',
			'artist' => 'Artist',
			'user_id' => 'User',
			'date' => 'Date',
			'avg_bitrate' => 'Avg Bitrate',
			'musicbrainz_albumid' => 'Musicbrainz Albumid',

			'csvdata' => 'CSV Data',
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
		$criteria->compare('type',$this->type,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('artist',$this->artist,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('date',$this->date,true);
		$criteria->compare('avg_bitrate',$this->avg_bitrate);
		$criteria->compare('musicbrainz_albumid',$this->musicbrainz_albumid,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}