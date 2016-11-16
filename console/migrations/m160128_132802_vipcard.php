<?php

use yii\db\Schema;
use yii\db\Migration;

class m160128_132802_vipcard extends Migration
{
    const TBL_NAME = '{{%vipcard}}';
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable(self::TBL_NAME, [
            'id' => Schema::TYPE_PK,
            'shop_id' => Schema::TYPE_STRING . ' NOT NULL',     //所属商店
            'vipcard_name' => Schema::TYPE_STRING . ' NOT NULL',
            'vipcard_subname' => Schema::TYPE_STRING . ' NOT NULL',
            'vipcard_money' => Schema::TYPE_STRING . ' NOT NULL',
            'vipcard_discount' => Schema::TYPE_FLOAT . ' NOT NULL',
            'vipcard_image' => Schema::TYPE_STRING . ' NOT NULL',
        ], $tableOptions);
    }
    public function safeDown()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
