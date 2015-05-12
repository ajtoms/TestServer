<?php

namespace backend\modules\v1

class Module extends \yii\base\Module
{
    public function init()
    {
        parent::init();
        
        //disable sessions to enforce statelessness
        \Yii::$app->user->enableSession = false;
    }
}

