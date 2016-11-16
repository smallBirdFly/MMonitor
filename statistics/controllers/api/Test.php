<?php

namespace mmclient\controllers\api;

use common\components\Constant;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class TestController extends Controller
{
    public $layout = 'blank';
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'test' => ['post'],
                ],
            ],
        ];
    }


    public function actionTest()
    {
        $request = Yii::$app->request;

        $result = [
            "code" => 200
        ];

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;
        $response->data = $result;
    }
}
