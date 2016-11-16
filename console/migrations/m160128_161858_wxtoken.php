<?php

use yii\db\Schema;
use yii\db\Migration;

class m160128_161858_wxtoken extends Migration
{
    const TBL_NAME = '{{%wxtoken}}';
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable(self::TBL_NAME, [
            'id' => Schema::TYPE_PK,
            'expire' => Schema::TYPE_INTEGER . ' NOT NULL',
            'value' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);

        $this->createIndex('expire', self::TBL_NAME, ['expire'],true);
    }
    public function safeDown()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
