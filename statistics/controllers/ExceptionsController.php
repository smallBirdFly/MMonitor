<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/20 0020
 * Time: 16:36
 */
namespace statistics\controllers;

use common\utils\HttpResponseUtil;
use statistics\component\AuthFilter;
use statistics\models\Daycount;
use statistics\models\Scount;
use Yii;
use yii\rest\Controller;

class ExceptionsController extends Controller
{
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        return [
            'auth' => [
                'class' => AuthFilter::className(),
                'except' => ['login'],
            ]
        ];
    }

    //按照小时分析
    public function actionExceptionHours()
    {
        $request = Yii::$app->request;
        $type = $request->post('type');
        $appkey = $request->post('appkey');
        $day = $request->post('day');
        for ($i = 0; $i < 24; $i++)
        {
            $hours[] = $i;
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime($day) + $i * 60 * 60);
            $endTime = date('Y-m-d H:i:s', strtotime($day) + $i * 60 * 60 + 3600);
            //今天各个小时的错误量
            $exceptions[$i] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => $type])->count();
        }
        $result['code'] = 200;
        $date = date('Y-m-d',time()-86400*$day);
        $result['data']['item'][] = $date;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $exceptions;
        HttpResponseUtil::setJsonResponse($result);
    }

    public function actionExceptionDays()
    {
        $request = Yii::$app->request;
        $type = $request->post('type');
        $appkey = $request->post('appkey');
        $date = $request->post('day');
        for ($i = 0; $i < $date; $i++)
        {
            $days[] = date('Y-m-d',time()-86400*($date - $i - 1));
            //当前时间
            $startTime = date('Y-m-d',time()-86400*($date - $i - 1));
            $endTime = date('Y-m-d', time()-86400*($date - $i -2));
            $exceptions[$i] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => $type])->count();
        }
        Yii::error($days);
        $result['code'] = 200;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $exceptions;
        HttpResponseUtil::setJsonResponse($result);
    }


}