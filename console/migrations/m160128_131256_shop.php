<?php

use yii\db\Schema;
use yii\db\Migration;

class m160128_131256_shop extends Migration
{
    const TBL_NAME = '{{%shop}}';
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable(self::TBL_NAME, [
            'id' => Schema::TYPE_PK,
            'shop_name' => Schema::TYPE_STRING . ' NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'updated_at' => Schema::TYPE_INTEGER . ' NOT NULL',
            'shop_address' => Schema::TYPE_STRING . ' NOT NULL',
            'shop_tel' => Schema::TYPE_STRING . ' NOT NULL',
            'shop_username' => Schema::TYPE_STRING . ' NOT NULL', //商户登录名
            'password_hash' => Schema::TYPE_STRING . ' NOT NULL', //商户登录密码
        ], $tableOptions);

        $this->createIndex('shop_username', self::TBL_NAME, ['shop_username'],true);
    }
    public function safeDown()
    {
        $this->dropTable(self::TBL_NAME);
    }
}
