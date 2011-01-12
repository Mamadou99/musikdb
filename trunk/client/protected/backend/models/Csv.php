<?php

class Csv extends CModel
{
	public $input;
	public $execTagging;
	public $execCollection;

	/**
	 * Returns the static model of the specified AR class.
	 * @return Release the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('input', 'required'),
			array('execTagging', 'boolean'),
			array('execCollection', 'boolean'),
		);
	}

	public function attributeNames()
	{
		return array(
			'input',
			'execTagging',
			'execCollection',
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'input' => 'CSV',
			'execTagging' => 'I have executed the Tagging Delete Script',
			'execCollection' => 'I have executed the Collection Delete Script',
		);
	}
}