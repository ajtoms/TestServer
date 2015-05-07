<?php

namespace common\models;

use Yii;
use yii\web\Link;
use yii\web\Linkable;
use yii\helpers\Url;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "test.post".
 *
 * @property integer $id
 * @property string $title
 * @property string $body
 * @property string $photo
 * @property integer $author
 * @property string $time_created
 *
 * @property User $author0
 */
class Post extends ActiveRecord implements Linkable
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'test.post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'body', 'author'], 'required'],
            [['author'], 'integer'],
            [['time_created'], 'safe'],
            [['title', 'body', 'photo'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'body' => 'Body',
            'photo' => 'Photo',
            'author' => 'Author',
            'time_created' => 'Time Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor0()
    {
        return $this->hasOne(User::className(), ['id' => 'author']);
    }

    public function getLastThreeLikes(){
        $likes = Like::find()
            ->where(['post_id' => $this->id])
            ->orderBy('time_liked DESC')
            ->limit(3)
            ->all();
        return $likes;
    }

    public function getLikeCount(){
        $count = Like::find()
            ->where(['post_id' => $this->id])
            ->count();
        return intval($count);
    }

    public function getLinks()
    {
        return [
            Link::REL_SELF => Url::to(['post/view', 'id' => $this->id], true),
        ];
    }

    public function fields()
    {
        return [
            'title',
            'body',
            'photo',
            'author',
            'like_count' => function($model) {
                return $model->getLikeCount();
            },
            'last_three_likes' => function($model){
                return $model->getLastThreeLikes();
            },
            'time_created',
        ];
    }

}