<?php

use yii\db\Schema;
use yii\db\Migration;

class m160128_132846_vipcard_publish extends Migration
{
    const TBL_NAME = '{{%vipcard_publish}}';
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable(self::TBL_NAME, [
            'id' => Schema::TYPE_PK,
            'customer_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'vipcard_id' => Schema::TYPE_STRING . ' NOT NULL',
            'shop_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'money' => Schema::TYPE_FLOAT . ' NOT NULL',
        ], $tableOptions);
    }
    public function safeDown()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
