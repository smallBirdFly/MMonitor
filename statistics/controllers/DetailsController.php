<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/01/10 0010
 * Time: 15:25
 */
namespace statistics\controllers;

use common\utils\HttpResponseUtil;
use statistics\component\AuthFilter;
use statistics\models\Month;
use Yii;
use yii\web\Controller;

class DetailsController extends Controller
{
    public $layout = 'blank';

    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'auth' => [
                'class' => AuthFilter::className(),
                'except' => ['login'],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    //某个小时的详细信息
    public function actionTrendHourDetails()
    {
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $startTime = $request->post('startTime');
        $endTime = $request->post('endTime');
        //小时数
        $hour = $request->post('hour');
        $type = $request->post('type');
        if ($startTime <= date('Y-m-d', time() - 86400 * 29))
        {
            $result['code'] = 202;
            $result['data']['message'] = '不正确的时间';
            return HttpResponseUtil::setJsonResponse($result);
        }
        $endTime = date('Y-m-d', strtotime($endTime) + 86400);
        if ($type == 'pv')
        {
            $res = Month::find()->where(['appkey' => $appkey, 'type' => 1, 'HOUR(time)' => $hour])->andWhere(['>=', 'time', $startTime])->andWhere(['<', 'time', $endTime])->all();
        }
        else if ($type = 'ip')
        {
            $res = Month::find()->where(['appkey' => $appkey, 'type' => 1, 'HOUR(time)' => $hour, 'visit' => 1])->andWhere(['>=', 'time', $startTime])->andWhere(['<', 'time', $endTime])->all();
        }
//        var_dump($res);die;
        $result['code'] = 200;
        $result['data']['item'][] = count($res);
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

//    某一天的详细信息
    public function actionTrendDayDetails()
    {
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        //日期数
        $day = $request->post('day');
        $type = $request->post('type');
        if ($type == 'pv')
        {
            $res = Month::find()->where(['appkey' => $appkey, 'type' => 1])->andWhere(['like', 'time', $day])->all();
        }
        else if ($type = 'ip')
        {
            $res = Month::find()->where(['appkey' => $appkey, 'type' => 1, 'visit' => 1])->andWhere(['like', 'time', $day])->all();
        }
//        var_dump($res);die;
        $result['code'] = 200;
        $result['data']['item'][] = count($res);
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

//    某一周的详细信息
    public function actionTrendWeekDetails()
    {
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $startDay = $request->post('startTime');
        $endDay = $request->post('endTime');
        $type = $request->post('type');
        $date = (strtotime($endDay) - strtotime($startDay)) / 86400 + 1;
        for($i = 0; $i < $date; $i++)
        {
            $startTime = date('Y-m-d',strtotime($startDay) + 86400 * $i);
            $endTime = date('Y-m-d',strtotime($startTime) + 86400);
            if ($type == 'pv')
            {
                $tmp = Month::find()->where(['appkey' => $appkey, 'type' => 1])->andWhere(['>=', 'time', $startTime])->andWhere(['<','time',$endTime])->all();
                if(!empty($tmp))
                {
                    $res[] = $tmp;
                }
            }
            else if ($type = 'ip')
            {
                $tmp = Month::find()->where(['appkey' => $appkey, 'type' => 1, 'visit' => 1])->andWhere(['>=', 'time', $startTime])->andWhere(['<','time',$endTime])->all();
                if(!empty($tmp))
                {
                    $res[] = $tmp;
                }
            }
        }
//        var_dump($res);die;
        $result['code'] = 200;
        $result['data']['item'][] = count($res);
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

//    某个比较时间内的详细信息
    public function actionChourPvDetails()
    {
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $compareStartDay = $request->post('compareStartDay');
        $compareEndDay = $request->post('compareEndDay');
        $comparedStartDay = $request->post('comparedStartDay');
        $comparedEndDay = date('Y-m-d',strtotime($comparedStartDay) + strtotime($compareEndDay) - strtotime($compareStartDay));
        $hour = $request->post('hour');

        if ($compareStartDay > date('Y-m-d', time() - 86400 * 29) || $comparedStartDay > date('Y-m-d', time() - 86400 * 29))
        {
            $result['code'] = 202;
            $result['data']['message'] = '不正确的时间';
            return HttpResponseUtil::setJsonResponse($result);
        }
        $res1 = Month::find()->where(['appkey'=>$appkey,'type'=>1,'HOUR(time)'=>$hour])->andWhere(['>=','time',$compareStartDay])->andWhere(['<','time',date('Y-m-d',strtotime($compareEndDay)+86400)])->all();
        $res2 = Month::find()->where(['appkey'=>$appkey,'type'=>1,'HOUR(time)'=>$hour])->andWhere(['>=','time',$comparedStartDay])->andWhere(['<','time',date('Y-m-d',strtotime($comparedEndDay)+86400)])->all();

        $result['code'] = 200;
        $result['data']['item'][] = $res1;
        $result['data']['item'][] = $res2;
    }

    //    某个比较时间内的详细信息
    public function actionChourIpDetails()
    {
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $compareStartDay = $request->post('compareStartDay');
        $compareEndDay = $request->post('compareEndDay');
        $comparedStartDay = $request->post('comparedStartDay');
        $comparedEndDay = date('Y-m-d',strtotime($comparedStartDay) + strtotime($compareEndDay) - strtotime($compareStartDay));
        $hour = $request->post('hour');

        if ($compareStartDay > date('Y-m-d', time() - 86400 * 29) || $comparedStartDay > date('Y-m-d', time() - 86400 * 29))
        {
            $result['code'] = 202;
            $result['data']['message'] = '不正确的时间';
            return HttpResponseUtil::setJsonResponse($result);
        }
        $res1 = Month::find()->where(['appkey'=>$appkey,'type'=>1,'HOUR(time)'=>$hour,'visit'=>1])->andWhere(['>=','time',$compareStartDay])->andWhere(['<','time',date('Y-m-d',strtotime($compareEndDay)+86400)])->all();
        $res2 = Month::find()->where(['appkey'=>$appkey,'type'=>1,'HOUR(time)'=>$hour,'visit'=>1])->andWhere(['>=','time',$comparedStartDay])->andWhere(['<','time',date('Y-m-d',strtotime($comparedEndDay)+86400)])->all();

        $result['code'] = 200;
        $result['data']['item'][] = $res1;
        $result['data']['item'][] = $res2;
    }

    //比较两个时间上pv量的信息
    public function actionCdayPvDetails()
    {
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $compareStartDay = $request->post('compareStartDay');
        $compareEndDay = $request->post('compareEndDay');
        $comparedStartDay = $request->post('comparedStartDay');
        $comparedEndDay = date('Y-m-d',strtotime($comparedStartDay) + strtotime($compareEndDay) - strtotime($compareStartDay));

        if ($compareStartDay > date('Y-m-d', time() - 86400 * 29) || $comparedStartDay > date('Y-m-d', time() - 86400 * 29))
        {
            $result['code'] = 202;
            $result['data']['message'] = '不正确的时间';
            return HttpResponseUtil::setJsonResponse($result);
        }
    }


    public function actionTest()
    {
    }
}