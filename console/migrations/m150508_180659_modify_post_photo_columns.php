<?php

use yii\db\Schema;
use yii\db\Migration;

class m150508_180659_modify_post_photo_columns extends Migration
{
    public function safeUp()
    {
        $this->execute("ALTER TABLE test.post RENAME photo TO photo_orig_path;");
        $this->execute("ALTER TABLE test.post ADD COLUMN photo_160_path TEXT");
        $this->execute("ALTER TABLE test.post ADD COLUMN photo_320_path TEXT");
        $this->execute("ALTER TABLE test.post ADD COLUMN photo_640_path TEXT");
        $this->execute("ALTER TABLE test.post ADD COLUMN photo_1280_path TEXT");
    }

    public function safeDown()
    {
        $this->execute("ALTER TABLE test.post RENAME photo_orig_path TO photo;");
        $this->execute("ALTER TABLE test.post DROP COLUMN IF EXISTS photo_160_path");
        $this->execute("ALTER TABLE test.post DROP COLUMN IF EXISTS photo_320_path");
        $this->execute("ALTER TABLE test.post DROP COLUMN IF EXISTS photo_640_path");
        $this->execute("ALTER TABLE test.post DROP COLUMN IF EXISTS photo_1280_path");
    }
    
}
