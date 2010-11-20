<?php
class BackendController extends Controller
{

	public function filters()
	{
		return array(
			'accessControl',
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('error'),
				'users'=>array('*'),
			),
			array('allow',
				'expression'=>'$user->isAdmin()',
			),
			array('deny',
				'users'=>array('*'),
			),
		);
	}
}
