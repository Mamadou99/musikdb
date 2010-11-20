<?php

/**
 * This is the model class for table "{{user}}".
 *
 * The followings are the available columns in table '{{user}}':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property integer $is_admin
 * @property string $last_login
 * @property string $created
 *
 * The followings are the available model relations:
 */
class User extends CActiveRecord
{
	public $newPassword;

	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
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
		return '{{user}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username', 'required'),
			array('is_admin', 'boolean'),
			array('username', 'length', 'max'=>8),
			array('last_login, created', 'safe'),
			array('newPassword', 'required', 'on'=>'insert'),
			array('newPassword', 'length', 'max'=>128, 'allowEmpty'=>true, 'on'=>'update'),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, password, is_admin, last_login, created', 'safe', 'on'=>'search'),
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'username' => 'Username',
			'password' => 'Password',
			'is_admin' => 'Is Admin',
			'last_login' => 'Last Login',
			'created' => 'Created',
		);
	}

    public function beforeSave()
    {
    	if(!empty($this->newPassword))
    		$this->password=hash('sha256', $this->newPassword);

    	return true;
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
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('is_admin',$this->is_admin);
		$criteria->compare('last_login',$this->last_login,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}