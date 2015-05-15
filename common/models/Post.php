<?php

namespace common\models;

use Intervention\Image\ImageManagerStatic as Image;
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
class Post extends ActiveRecord
{

    public $photo;

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
            [['title', 'body'], 'string', 'max' => 255],
            [['photo'], 'file', 'extensions'=>'jpeg, jpg, png, gif', 
                'mimeTypes' => 'image/jpeg, image/png, image/gif']
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
            'photo_orig_path' => 'Original Photo Filepath',
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

    public function getLikeCount()
    {
        $count = Like::find()
            ->where(['post_id' => $this->id])
            ->count();
        return intval($count);
    }

    public function getLastThreeLikes()
    {
        $likes = Like::find()
            ->where(['post_id' => $this->id])
            ->orderBy('time_liked DESC')
            ->limit(3)
            ->all();
        return $likes;
    }

    public function userHasLiked(){
        $liked = Like::find()
            ->where(['post_id' => $this->id])
            ->count();
        return  intval($liked);
    }

    public function fields()
    {
        return [
            'id',
            'title',
            'body',
            'photo_orig_path',
            'photo_160_path',
            'photo_320_path',
            'photo_640_path',
            'photo_1280_path',
            'author' => function($model) {
                return User::findIdentity($model->author)["username"];
            },
            'like_count' => function($model) {
                return $model->getLikeCount();
            },
            'last_three_likes' => function($model){
                return $model->getLastThreeLikes();
            },
            'user_has_liked' => function($model){
                return $model->userHasLiked();
            },
            'time_created',
        ];
    }

    //////////////////////////////////////////////////////////////////
    //  Photo Logic; Uses library called Intervention or something  //
    //////////////////////////////////////////////////////////////////

    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert))
        {
            $this->savePhoto();
            return true;
        } else {
            return false;
        }
    }

    public function savePhoto()
    {
        if($this->photo)
        {
            $filename = uniqid();
            $basePath = '/photos/';
            $relPath =  $basePath . "original/" . $filename . '.' . $this->photo->extension;

            $absPath = Yii::$app->basePath.'/web'.$relPath;

            $this->photo->saveAs($absPath);
            $this->photo_orig_path = $relPath;

            $img = Image::make($absPath);
            for($i = 160; $i <= 1280; $i=$i*2){
                $attr = "photo_".$i."_path";
                if($img->width() > $i){
                    $img->backup();
                    $relPath = $basePath.$i.'/'.$filename.'.jpg';
                    $absPath = Yii::$app->basePath.'/web'.$relPath;
                    $img->widen($i)
                        ->save($absPath);
                    $this->$attr = $relPath;
                    $img->reset();
                } else {
                    $this->$attr = $relPath;
                }
            }
        }
    }

}