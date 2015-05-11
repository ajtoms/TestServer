<?php

namespace backend\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\HttpBasicAuth;
use yii\web\UnauthorizedHttpException;

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
		
		$action['update']['checkAccess'] = [$this, 'checkAccess'];
		
		return $actions;
	}
	
	/**
	* Override this to check if the user is trying to modify their own data
	* or that of another user.
	*/
	public function checkAccess($action, $model = null, $params = [])
	{
		if($action == 'update')
		{
			$userId = Yii::$app->user->identity->id;
			$request = Yii::$app->getRequest();
			$params = $request->getBodyParams();
			
			
			
			//this is a good if-statement, right?
			if((count($params) == 1 && array_key_exists('email', $params)) ||
				(count($params) == 1 && array_key_exists('password', $params)) ||
				(count($params) == 2 && (array_key_exists('email', $params) && array_key_exists('password', $params))))
			{
				if($model->getId() != $userId)
				{
					throw new UnauthorizedHttpException("Not allowed to change someone else's account information.");
				}
			}
			else
			{
				//then we should stop
				throw new UnauthorizedHttpException("Only allowed to change email and password.");
			}
			
		}
		
		return parent::checkAccess($action, $model, $params);
	}
}