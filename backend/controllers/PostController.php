<?php

namespace backend\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
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

	////////////////////////////////////////////////////////////////////////////////////////
	//  Need to customize the create action to take user from auth data and save a photo  //
	////////////////////////////////////////////////////////////////////////////////////////

	public function actions()
	{
		$actions = parent::actions();

		$actions["create"] = [
			'class' => 'backend\actions\CreatePostAction',
			'modelClass' => $this->modelClass,
			'checkAccess' => [$this, 'checkAccess'],
		];
		$actions["index"] = [
			'class' => 'backend\actions\IndexPostAction',
			'modelClass' => $this->modelClass,
			'checkAccess' => [$this, 'checkAccess'],
		];

		return $actions;
	}

	public function checkAccess($action, $model = null, $params = [])
	{

		if($action === "delete" || $action === "update")
		{
			$userId = Yii::$app->user->identity->id;
			if($model->author !== $userId)
				throw new ForbiddenHttpException("Permission to modify others' posts denied.");
		}

		return parent::checkAccess($action, $model, $params);
	}

	////////////////////////////////////////////////////////////////
	//  API Calls for Likes; Routed through spoofing in main.php  //
	////////////////////////////////////////////////////////////////

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

		$user = Yii::$app->user->identity;
		$postId = intval($request->get("id"));
		$newLike = new Like ();
		$newLike->user_id = $user->id;
		$newLike->post_id = $postId;
		$newLike->save();

		return $newLike;
	}

	public function actionDeleteLike()
	{
		$request = Yii::$app->request;

		$user = Yii::$app->user->identity;
		$userId = $user->id;
		$postId = $request->get("id");

		$delLike = Like::find()
			->where(["post_id" => $postId, "user_id" => $userId])
			->all()[0];

		$delLike->delete();

		return "Success";
	}
}