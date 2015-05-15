<?php

namespace backend\actions;

use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\UnauthorizedHttpException;
use common\models\User;


/**
* Called when someone tries to do POST users/login.
* This should have a username and password and be able 
* to send back a newly generated access_token for that user.
* 
*/
class LoginAction extends \yii\rest\Action
{
	public function run()
	{	
		$params = Yii::$app->getRequest()->post();
		
		$username = $params['username'];
		$password = $params['password'];
		
		$user = User::findByUsername($username);
		if($user != null && $user->validatePassword($password))
		{
			$user->generateAuthKey();
			$user->save();
			
			//we got here by a post request so we should send back an "entity describing or containing the result of the action"
			//We've made it here so set the OK code (200) and give back username and access token
			$response = Yii::$app->getResponse();
            $response->setStatusCode(200);
            
			return 	[
						'id' => $user->id,
						'username' => $user->getUsername(),
						'access_token' => $user->getAuthKey()
					];
		}
		
		throw new UnauthorizedHttpException('Invalid username or password. Failed to log in.');
	}
	
	
}