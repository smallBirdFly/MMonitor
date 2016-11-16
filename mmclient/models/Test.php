<?php
namespace mmclient\models;

use yii\db\ActiveRecord;

class Test extends ActiveRecord
{
    public static function tableName()
    {
        return "test";
    }

}