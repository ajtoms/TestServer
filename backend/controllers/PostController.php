<?php

namespace backend\controllers;

use Yii;
use yii\rest\ActiveController;
use common\models\Like;
use yii\filters\auth\HttpBasicAuth;

class PostController extends ActiveController
{
	public $modelClass = 'common\models\Post';
    
    public function behaviors()
	{
	    $behaviors = parent::behaviors();
	    $behaviors['authenticator'] = [
	        'class' => HttpBasicAuth::className(),
	    ];
	    return $behaviors;
	}

	public function actionGetLikes()
	{
		$request = Yii::$app->request;

		$postId = $request->get("id");

		$likes = Like::find()
			->where(["post_id" => $postId])
			->all();

		return $likes;
	}

	public function actionCreateLike()
	{
		$request = Yii::$app->request;

		$postId = $request->get("id");
		$newLike = new Like ();
		$newLike->user_id = 1; //AHHHH HARD CODING BAD! NUUUUUU :(
		$newLike->post_id = $postId;
		$newLike->save();

		return $newLike;
	}

	public function actionDeleteLike()
	{
		$request = Yii::$app->request;

		$userId = 1;  //AHHHH HARD CODING BAD! NUUUUUU :(
		$postId = $request->get("id");

		$delLike = Like::find()
			->where(["post_id" => $postId, "user_id" => $userId])
			->all()[0];

		$delLike->delete();

		return "Success";
	}
}