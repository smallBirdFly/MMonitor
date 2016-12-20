<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/20 0020
 * Time: 16:36
 */
namespace statistics\controllers;

use common\utils\HttpResponseUtil;
use statistics\models\Daycount;
use statistics\models\Scount;
use Yii;
use yii\rest\Controller;

class ExceptionsController extends Controller
{
    //今天的错误信息
    public function actionTodayError()
    {
        $appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 24; $i++)
        {
            $hours[][] = $i;
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60);
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 + 3600);
            //今天各个小时的错误量
            $errors[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 0])->count();
            $errors[$i][] = date('Y-m-d H:00:00',time());
        }
        $result['code'] = 200;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $errors;
        HttpResponseUtil::setJsonResponse($result);
    }

    //今天的警告信息
    public function actionTodayWarn()
    {
        $appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 24; $i++)
        {
            $hours[][] = $i;
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60);
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 + 3600);
            //今天各个小时的错误量
            $errors[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 0])->count();
            $errors[$i][] = date('Y-m-d H:00:00',time());
        }
        $result['code'] = 200;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $errors;
        HttpResponseUtil::setJsonResponse($result);
    }

    //昨天的错误信息
    public function actionYesterdayError()
    {

    }
}