<?php

namespace backend\controllers;

use yii\rest\ActiveController;

class PostController extends ActiveController
{
	public $modelClass = 'common\models\Post';
}