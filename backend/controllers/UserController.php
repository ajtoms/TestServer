<?php

namespace backend\controllers;

use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;

class UserController extends ActiveController
{
    public $modelClass = 'common\models\User';
    
    public function behaviors()
	{
	    $behaviors = parent::behaviors();
	    $behaviors['authenticator'] = [
	        'class' => HttpBasicAuth::className(),
	        'except' => ['login', 'create'],
	    ];
	    return $behaviors;
	}
	
	public function actions()
	{
		$actions = parent::actions();
		
		$actions['login'] = [
			'class' => "backend\actions\LoginAction",
			'modelClass' => 'common\models\User'];
		
		return $actions;
	}
}