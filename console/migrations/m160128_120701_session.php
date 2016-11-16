<?php

use yii\db\Schema;
use yii\db\Migration;

class m160128_120701_session extends Migration
{
    const TBL_NAME = '{{%session}}';
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable(self::TBL_NAME, [
            'id' => Schema::TYPE_STRING . '(64) PRIMARY KEY',
            'expire' => Schema::TYPE_INTEGER . ' NOT NULL',
            'data' => Schema::TYPE_BINARY . ' NOT NULL',
        ], $tableOptions);
    }
    public function safeDown()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
