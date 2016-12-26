<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/19 0019
 * Time: 9:40
 */
namespace statistics\controllers;

use common\utils\HttpResponseUtil;
use statistics\models\Daycount;
use statistics\models\Scount;
use statistics\models\Webs;
use statistics\component\AuthFilter;

use Yii;

class AnalyseController extends \yii\web\Controller
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
//    今日和昨天访问量
    public function actionToday()
    {
        Yii::error('123');
        $appkey = Yii::$app->request->post('appkey');
        $startTime = date('Y-m-d',time());
        $endTime = date('Y-m-d',time() + 86400);
//      今天的记录
        $pv = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
        $ip = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
        $result['code'] = 200;
        $result['data']['domain'] = Webs::find()->where(['appkey'=>Yii::$app->request->post('appkey')])->one();
        $result['data']['today']['pv'] = $pv;
        $result['data']['today']['ip'] = $ip;
//       昨天的记录
        $startTime = date('Y-m-d',time() - 86400);
        $endTime = date('Y-m-d',time());
        $pv = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();;
        $ip = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
        $result['data']['yesterday']['pv'] = $pv;
        $result['data']['yesterday']['ip'] = $ip;
        HttpResponseUtil::setJsonResponse($result);
    }

//按照小时分析pv
/*
 *  @ $start比较的日期
 *  @ $past被比较的日期
 * */
    public function actionCompareHours()
    {
        $request = Yii::$app->request;
        Yii::error($request->post());
        $start = $request->post('startTime');
        $past = $request->post('endTime');
        $type = $request->post('type');
        $appkey = $request->post('appkey');
        for ($i = 0; $i < 24; $i++)
        {
            $hours[] = $i;
            //比较时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60- $start * 86400);
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 + 3600 - $start * 86400);
//            被比较的时间
            $cstartTime = date('Y-m-d H:i:s',strtotime($startTime)-86400 * $past);
            $cendTime = date('Y-m-d H:i:s',strtotime($endTime)-86400 * $past);
//            判断是否为求独立访问量
            if($type == 'pv')
            {
                //要比较日期各个小时的访问量
                $ress[$i] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->count();
                //统计昨天各个小时的pv量
                $resp[$i] = Scount::find()->where(['>=','time',$cstartTime])->andWhere(['<','time',$cendTime])->andWhere(['appkey'=>$appkey,'type'=>1])->count();
            }
            else if($type == 'ip')
            {
                //要比较日期各个小时的独立访问量
                $ress[$i] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->groupBy('ip')->count();
                //统计昨天各个小时的ip量
                $resp[$i] = Scount::find()->where(['>=','time',$cstartTime])->andWhere(['<','time',$cendTime])->andWhere(['appkey'=>$appkey,'type'=>1])->groupBy('ip')->count();
            }
            else
            {
                $ress[][] = array();
                $resp[][] = array();
            }

        }
        $date[] = date('Y-m-d',strtotime($startTime));
        $date[] = date('Y-m-d',strtotime($cstartTime));
        Yii::error($startTime);
        Yii::error($cstartTime);
        $result['code'] = 200;
        $result['data']['item'][] = $date;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $ress;
        $result['data']['item'][] = $resp;
        HttpResponseUtil::setJsonResponse($result);
    }

//按照日期分析
    public function actionCompareDays()
    {
        $request = Yii::$app->request;
        $date = $request->post('date');
        $type = $request->post('type');
        $appkey = $request->post('appkey');
        for ($i = 0; $i <= $date; $i++)
        {
            $days[] = date('Y-m-d',time()-86400*($date - $i));
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) - ($date - $i) * 60 * 60 * 24 );
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d"))  - ($date - $i)* 60 * 60 * 24  + 86400);
            if($type == 'pv')
            {
                //每天的访问量
                $pv[$i] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->count();
            }
            else if($type == 'ip')
            {
                //每天独立的访问量
                $pv[$i] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->groupBy('ip')->count();
            }
            else
            {

            }
        }
        $date[] = date("Y-m-d",strtotime($startTime));
        $date[] = date("Y-m-d",strtotime($endTime));
        $result['code'] = 200;
        $result['data']['item'][] = $date;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $pv;
        HttpResponseUtil::setJsonResponse($result);
    }

    //今天和昨天pv相比
    //今天和昨天ip相比
    //今天和7天前pv相比
    //今天和7天前ip相比
    //昨天和前天pv相比
    //昨天和前天ip相比
    //昨天和7天前pv相比
    //昨天和7天前ip相比

//最近7天的pv
//最近7天独立访问的ip
//最近30天的访问量
//最近30天独立访问量

    /*
     * 按照小时分析pv和ip
     *  @ $startTime开始日期
     *  @ $endTime结束日期
     * */
    public function actionTrendHours()
    {
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $startTime = $request->post('startTime');
        $endTime = $request->post('endTime');
        //当前时间
        for($i = 0; $i < 24; $i++)
        {
            if ($i < 10)
            {
                $hours[][] = '0' . $i . ':00 - 0' . $i . ':59';
            }
            else
            {
                $hours[][] = $i . ':00 - ' . $i . ':59';
            }
            //每天各个小时的访问量
            $res[$i][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'hour'=>$i])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
            //每天各个小时的独立访问量
            $res[$i][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'hour'=>$i])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
        }
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }
    //今天按照小时分析pv和ip
    //昨日按照小时分析pv和ip
    //最近7天按照小时分析pv和ip
    //最近30天按照小时分析
    //某个时间区间按照小时分析
    //某一天按照小时分析
    //某一段时间按照小时分析

    //按照天数分析pv和ip $startDay开始的日期，$endDay结束的日期
    public function actionTrendDay()
    {
        $request = Yii::$app->request;
        $startDay = $request->post('startTime');
        $endDay = $request->post('endTime');
        $date = (strtotime($endDay)-strtotime($startDay)) / (86400) + 1;
        $appkey = $request->post('appkey');
        for($i = 1; $i <= $date; $i++)
        {
            $days[] = date('Y-m-d',time()-86400*($date-$i));
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) - 86400*($date-$i));
            $endTime = date('Y-m-d', time() - 86400*($date-1-$i));
            //7天每天的访问量
            $res[$i-1][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
            //7天每天的独立访问量
            $res[$i-1][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
        }
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }
    //最近7天按照天数分析pv和ip
    //最近30天按照天分析
    //最近时间区间内按照天数分析

    //按照7天分析数据
    public function actionTrendWeek()
    {
        $request = Yii::$app->request;
        $startDay = $request->post('startTime');
        $endDay = $request->post('endTime');
        $date = (strtotime($endDay)-strtotime($startDay)) / (86400) + 1;
        //需要循环的此时
        $count = intval($date%7 == 0 ?  $date/7 :$date/7+1);
        $appkey = $request->post('appkey');
//        总数
        $spv = 0;
        $sip = 0;
        for($i = 0; $i <  $count; $i++)
        {
            if($i*7+6 < $date)
            {
                //日期区间的显示，开始时间
                $weeks[] = date('Y-m-d',strtotime($startDay)+ $i*7 * 86400).' - '.date('Y-m-d',strtotime($startDay) + ($i*7+6) * 86400);
                $pv = 0;
                $ip = 0;
                for($j = 0; $j < 7; $j++)
                {
                    $startTime = date('Y-m-d',strtotime($startDay)+ ($i*7+$j) * 86400);
                    $endTime = date('Y-m-d',strtotime($startDay)+ ($i*7+$j) * 86400 + 86400);
                    //每天的访问量
                    $pv += Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
//                每天的独立ip数
                    $ip += Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
                }
                Yii::error($pv);
                Yii::error($ip);
            }
            else
            {
                //日期区间的显示，开始时间
                $weeks[] = date('Y-m-d',strtotime($startDay)+ $i*7 * 86400).' - '.date('Y-m-d',strtotime($endDay));
                $pv = 0;
                $ip = 0;
                for($j = 0; $j < $date % 7; $j++)
                {
                    $startTime = date('Y-m-d',strtotime($startDay)+ ($i*7+$j) * 86400);
                    $endTime = date('Y-m-d',strtotime($startDay)+ ($i*7+$j) * 86400 + 86400);
                    //每天的访问量
                    $pv += Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
//                每天的独立ip数
                    $ip += Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
                }
                Yii::error($pv);
                Yii::error($ip);
            }
            //7天每天的访问量
            $res[$i][] = $pv;
            //7天每天的独立访问量
            $res[$i][] = $ip;
            $spv += $pv;
            $sip += $ip;
        }
        $sum[] = $spv;
        $sum[] = $sip;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $weeks;
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }
    //最近30天按照星期分析

    //区间时间内按照星期分析
    private function CompareTrendHour($startTime,$endTime)
    {
        $startTime = date('Y-m-d',strtotime($startTime));
        $endTime = date('Y-m-d',strtotime($endTime));
        $appkey = Yii::$app->request->post('appkey');
        //当前时间
        for($i = 0; $i < 24; $i++)
        {
            if ($i < 10)
            {
                $hours[][] = '0' . $i . ':00 - 0' . $i . ':59';
            }
            else
            {
                $hours[][] = $i . ':00 - ' . $i . ':59';
            }
            //时间1各个小时的访问量
            $res1[$i][] = $startTime;
            $res1[$i][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'hour'=>$i])->andWhere(['like','time',$startTime])->count();
            //时间2各个小时的访问量
            $res2[$i][] = $endTime;
            $res2[$i][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'hour'=>$i])->andWhere(['like','time',$endTime])->count();
        }
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['like','time',$startTime])->count();
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['like','time',$endTime])->count();
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $res1;
        $result['data']['item'][] = $res2;
        HttpResponseUtil::setJsonResponse($result);
    }

    //比较两个时间按照小时分析ip/pv
    public function actionCompareHourPv()
    {
        $request = Yii::$app->request;
        $startTime = date('Y-m-d',strtotime($request->post('compareDay')));
        $endTime = date('Y-m-d',strtotime($request->post('comparedDay')));
        $appkey = $request->post('appkey');
        //当前时间
        for($i = 0; $i < 24; $i++)
        {
            if ($i < 10)
            {
                $hours[][] = '0' . $i . ':00 - 0' . $i . ':59';
            }
            else
            {
                $hours[][] = $i . ':00 - ' . $i . ':59';
            }
            //时间1各个小时的访问量
            $res1[$i][] = $startTime;
            $res1[$i][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'hour'=>$i])->andWhere(['like','time',$startTime])->count();
            //时间2各个小时的访问量
            $res2[$i][] = $endTime;
            $res2[$i][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'hour'=>$i])->andWhere(['like','time',$endTime])->count();
        }
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['like','time',$startTime])->count();
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['like','time',$endTime])->count();
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $res1;
        $result['data']['item'][] = $res2;
        HttpResponseUtil::setJsonResponse($result);
    }

    public function actionCompareHourIp()
    {
        $request = Yii::$app->request;
        $startTime = date('Y-m-d',strtotime($request->post('compareDay')));
        $endTime = date('Y-m-d',strtotime($request->post('comparedDay')));
        $appkey = $request->post('appkey');
        //当前时间
        for($i = 0; $i < 24; $i++)
        {
            if ($i < 10)
            {
                $hours[][] = '0' . $i . ':00 - 0' . $i . ':59';
            }
            else
            {
                $hours[][] = $i . ':00 - ' . $i . ':59';
            }
            //时间1各个小时的访问量
            $res1[$i][] = $startTime;
            $res1[$i][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'hour'=>$i])->andWhere(['like','time',$startTime])->count();
            //时间2各个小时的访问量
            $res2[$i][] = $endTime;
            $res2[$i][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'hour'=>$i])->andWhere(['like','time',$endTime])->count();
        }
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['like','time',$startTime])->count();
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['like','time',$endTime])->count();
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $res1;
        $result['data']['item'][] = $res2;
        HttpResponseUtil::setJsonResponse($result);
    }

    //比较两个时间按照天分析ip/pv
    public function actionRangeDateDay()
    {
        $requrest = Yii::$app->request;
        $compareStartDay = $requrest->post('compareStartDay');
        $compareEndDay = $requrest->post('compareEndDay');
        $comparedStartDay = $requrest->post('comparedStartDay');
        $appkey = Yii::$app->request->post('appkey');
        $date = (strtotime($compareEndDay) - strtotime($compareStartDay)) / 86400;
        for ($i = 0; $i <= $date; $i++)
        {
            $days[] = date('Y-m-d',time()-86400*($date - $i));
            //比较的数据
            $startTime = date('Y-m-d H:i:s',strtotime($compareStartDay) + $i * 86400);
            $endTime = date('Y-m-d H:i:s',strtotime($compareStartDay) + $i * 86400 + 86400);
            $res1[$i][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['<=','time',$startTime])->andWhere(['>','time',$endTime]);
            //被比较的数据
            $startTime = date('Y-m-d H:i:s',strtotime($comparedStartDay) + $i * 86400);
            $endTime = date('Y-m-d H:i:s',strtotime($comparedStartDay) + $i * 86400 + 86400);
            $res2[$i][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['<=','time',$startTime])->andWhere(['>','time',$endTime]);

        }
        $result['code'] = 200;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $res1;
        $result['data']['item'][] = $res2;
        HttpResponseUtil::setJsonResponse($result);
    }

    //比较两个时间按照星期分析ip/pv
    public function actionRangeDateWeek()
    {
        $requrest = Yii::$app->request;
        $compareStartDay = $requrest->post('compareStartDay');
        $compareEndDay = $requrest->post('compareEndDay');
        $comparedStartDay = $requrest->post('comparedStartDay');
        $date = (strtotime($compareEndDay)-strtotime($compareStartDay)) / (86400) + 1;
        //需要循环的此时
        $count = intval($date%7 == 0 ?  $date/7 :$date/7+1);
        $appkey = Yii::$app->request->post('appkey');
//        总数
        $spv = 0;
        $sip = 0;
        for($i = 0; $i <  $count; $i++)
        {
            if($i*7+6 < $date)
            {
                //日期区间的显示，开始时间
                $weeks[] = date('Y-m-d',strtotime($compareStartDay)+ $i*7 * 86400).' - '.date('Y-m-d',strtotime($compareStartDay) + ($i*7+6) * 86400);
                $pv = 0;
                $ip = 0;
                for($j = 0; $j < 7; $j++)
                {
                    $startTime = date('Y-m-d',strtotime($compareStartDay)+ ($i*7+$j) * 86400);
                    $endTime = date('Y-m-d',strtotime($compareStartDay)+ ($i*7+$j) * 86400 + 86400);
                    //每天的访问量
                    $pv += Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
//                每天的独立ip数
                    $ip += Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
                }
                Yii::error($pv);
                Yii::error($ip);
            }
            else
            {
                //日期区间的显示，开始时间
                $weeks[] = date('Y-m-d',strtotime($compareStartDay)+ $i*7 * 86400).' - '.date('Y-m-d',strtotime($compareStartDay));
                $pv = 0;
                $ip = 0;
                for($j = 0; $j < $date % 7; $j++)
                {
                    $startTime = date('Y-m-d',strtotime($compareStartDay)+ ($i*7+$j) * 86400);
                    $endTime = date('Y-m-d',strtotime($compareStartDay)+ ($i*7+$j) * 86400 + 86400);
                    //每天的访问量
                    $pv += Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
//                每天的独立ip数
                    $ip += Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
                }
                Yii::error($pv);
                Yii::error($ip);
            }
            //7天每天的访问量
            $res1[$i][] = $pv;
            //7天每天的独立访问量
            $res2[$i][] = $ip;
            $spv += $pv;
            $sip += $ip;
        }
        $sum[] = $spv;
        $sum[] = $sip;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $weeks;
        $result['data']['item'][] = $res1;
        $result['data']['item'][] = $res2;
        HttpResponseUtil::setJsonResponse($result);
    }















    public function actionTest()
    {
        $startDay = Yii::$app->request->post('startTime');
        $endDay = Yii::$app->request->post('endTime');
        $date = (strtotime($endDay)-strtotime($startDay)) / (86400) + 1;
        Yii::error($date);
        $appkey = Yii::$app->request->post('appkey');
        for($i = 0; $i <  $date; $i+=7)
        {
            //日期区间的显示，开始时间
            $weeks[] = date('Y-m-d',strtotime($startDay)+ $i * 86400).' - '.date('Y-m-d',($i+6) < $date ? strtotime($startDay) + ($i+6) * 86400 : strtotime($endDay));
            $startTime = date('Y-m-d',strtotime($startDay)+ $i * 86400);
            $endTime = date('Y-m-d',($i+6) < $date ? strtotime($startDay) + ($i+6) * 86400 : strtotime($endDay));
            //7天每天的访问量
            $res[$i/7][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
            //7天每天的独立访问量
            $res[$i/7][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
        }
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $weeks;
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }
}
