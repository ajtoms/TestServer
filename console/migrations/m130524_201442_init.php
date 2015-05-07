<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function safeUp()
    {

        $this->execute("CREATE SCHEMA test");

        $this->createTable('test.user', [
            'id' => Schema::TYPE_PK,
            'username' => Schema::TYPE_STRING . ' NOT NULL',
            'auth_key' => Schema::TYPE_STRING . '(32) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . ' NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING,
            'email' => Schema::TYPE_STRING . ' NOT NULL',

            'status' => Schema::TYPE_SMALLINT . ' NOT NULL DEFAULT 10',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
        ]);

        $this->createTable("test.post", [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING . " NOT NULL",
            'body' => Schema::TYPE_STRING . " NOT NULL",
            'photo' => Schema::TYPE_STRING,
            'author' => Schema::TYPE_INTEGER . " NOT NULL",
            'time_created' => Schema::TYPE_TIMESTAMP . " DEFAULT NOW()",
        ]);
        $this->addForeignKey("post_author_fkey", "test.post", "author", "test.user", "id");

        $this->createTable("test.user_liked_posts", [
            'user_id' => Schema::TYPE_INTEGER,
            'post_id' => Schema::TYPE_INTEGER,
            'time_liked' => Schema::TYPE_TIMESTAMP . " DEFAULT NOW()",
        ]);
        $this->addPrimaryKey("user_liked_posts_pkey", "test.user_liked_posts", "user_id, post_id");
    }

    public function safeDown()
    {
        $this->dropTable('test.user_liked_posts');
        $this->dropTable('test.post');
        $this->dropTable('test.user');
        $this->execute('DROP SCHEMA test');
    }
}
