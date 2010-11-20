<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	private $_id;
	private $_record;

	/**
	 * Constructor.
	 * @param string username
	 * @param string password
	 */
	public function __construct($username,$password)
	{
		$this->username=$username;
		$this->password=$password;

		$this->_record=User::model()->findByAttributes(array('username'=>$this->username));
	}


	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		if($this->_record === null)
			$this->errorCode = self::ERROR_USERNAME_INVALID;

		else if($this->_record->password !== hash('sha256', $this->password))
			$this->errorCode = self::ERROR_PASSWORD_INVALID;

		else {
			$this->_id=$this->_record->id;
			$this->_record->last_login = new CDbExpression('NOW()');
		 	$this->_record->update('last_login');

			$this->errorCode=self::ERROR_NONE;
		}

		return !$this->errorCode;
	}

    public function getId()
    {
        return $this->_id;
    }
}