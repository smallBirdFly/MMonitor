<?php

use yii\db\Schema;
use yii\db\Migration;

class m160128_132917_record extends Migration
{
    const TBL_NAME = '{{%record}}';
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable(self::TBL_NAME, [
            'id' => Schema::TYPE_PK,
            'customer_id' => Schema::TYPE_STRING . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'shop_id' => Schema::TYPE_STRING . ' NOT NULL',
            'detail' => Schema::TYPE_STRING . ' NOT NULL',
            'money' => Schema::TYPE_FLOAT . ' NOT NULL',
        ], $tableOptions);
    }
    public function safeDown()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
