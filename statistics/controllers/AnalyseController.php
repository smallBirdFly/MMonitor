<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/19 0019
 * Time: 9:40
 */
namespace statistics\controllers;

use common\components\MMLogger;
use common\utils\HttpResponseUtil;
use statistics\models\Daycount;
use statistics\models\Page;
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

//    获取所有的appkey
    public function actionAppkey()
    {
        $appkeys = Webs::find()->asArray()->all();
        foreach($appkeys as $i=>$appkey)
        {
            $arr[$i][] = $appkey['appkey'];
            $arr[$i][] = $appkey['domain_name'];
        }
        $result['code'] = 200;
        $result['data']['item'] = $arr;
        HttpResponseUtil::setJsonResponse($result);
    }

    //入口
    public function actionEntry()
    {
        $appkey = Yii::$app->request->post('appkey');
        $webs = Page::find()->where(['appkey'=>$appkey])->all();
        $count = Scount::find()->where(['appkey'=>$appkey,'type'=>1])->count();
        foreach($webs as $i=>$web)
        {
            $href[$i][] = $web['page_url'];
            $num = Scount::find()->where(['appkey'=>$appkey,'page'=>$web['id'],'type'=>1])->count();
            $href[$i][] = $num;
            $href[$i][] = round($num/$count*100,2);
        }
        $result['code'] = 200;
        $result['data']['item'][] = $href;
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
//        Yii::error($request->post());
        $start = $request->post('startTime');
        $end = $request->post('endTime');
        $type = $request->post('type');
        $appkey = $request->post('appkey');
        for ($i = 0; $i < 24; $i++)
        {
            if ($i < 10)
            {
                $hours[] = '0' . $i . ':00 - 0' . $i . ':59';
            }
            else
            {
                $hours[] = $i . ':00 - ' . $i . ':59';
            }
            //比较时间
            $startTime = date('Y-m-d H:i:s', strtotime($start) + 3600 * $i);
            $endTime = date('Y-m-d H:i:s', strtotime($startTime) + 3600);
//            被比较的时间
            $cstartTime = date('Y-m-d H:i:s',strtotime($end) + 3600 * $i);
            $cendTime = date('Y-m-d H:i:s',strtotime($cstartTime) + 3600);
//            判断是否为求独立访问量
            if($type == 'pv')
            {
                //要比较日期各个小时的访问量
                $ress[$i] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey,'type' => 1])->count();
                //统计昨天各个小时的pv量
                $resp[$i] = Scount::find()->where(['>=','time',$cstartTime])->andWhere(['<','time',$cendTime])->andWhere(['appkey'=>$appkey,'type'=>1])->count();
            }
            else if($type == 'ip')
            {
                //要比较日期各个小时的独立访问量
                $ress[$i] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey,'type' => 1,'visit'=>1])->count();
                //统计昨天各个小时的ip量
                $resp[$i] = Scount::find()->where(['>=','time',$cstartTime])->andWhere(['<','time',$cendTime])->andWhere(['appkey'=>$appkey,'type'=>1,'visit'=>1])->count();
            }
            else
            {
                $ress[][] = array();
                $resp[][] = array();
            }

        }
        $date[] = date('Y-m-d',strtotime($startTime));
        $date[] = date('Y-m-d',strtotime($cstartTime));
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
        $day = $request->post('startTime');
        $date = round((time() - strtotime($day)) / 86400);
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
        $result['code'] = 200;
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
        if($startTime == $endTime)
        {
            $date[] = $startTime;
            $date[] = $endTime;
        }
        else
        {
            $date[] = $startTime.' - '.$endTime;
        }
//        $logger = MMLogger::getLogger(__FUNCTION__);
//        $logger->error($startTime);
//        $logger->error($endTime);
        $endTime = date('Y-m-d',strtotime($endTime) + 86400);
        for($i = 0; $i < 24; $i++)
        {
            if ($i < 10)
            {
                $hours[] = '0' . $i . ':00 - 0' . $i . ':59';
            }
            else
            {
                $hours[] = $i . ':00 - ' . $i . ':59';
            }
            $sql =  Scount::find()->where(['appkey' => $appkey,'type'=> 1,'HOUR(time)'=>$i])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime]);
            //每天各个小时的访问量
            $pv[] =  $sql->count();
            //每天各个小时的独立访问量
            $ip[] =  $sql->andWhere(['visit'=>1])->count();
        }
        $tmp1 = 0;
        $tmp2 = 0;
        foreach($pv as $item)
        {
            $tmp1 += $item;
        }
        foreach($ip as $item)
        {
            $tmp2 += $item;
        }
        $sum[] = $tmp1;
        $sum[] = $tmp2;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $date;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $pv;
        $result['data']['item'][] = $ip;
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
    public function actionTrendDays()
    {
        $request = Yii::$app->request;
        $startDay = $request->post('startTime');
        $endDay = $request->post('endTime');
        $date = (strtotime($endDay) - strtotime($startDay))/86400 + 1;
        $appkey = $request->post('appkey');
//        $logger = MMLogger::getLogger(__FUNCTION__);
        for($i = 0; $i < $date; $i++)
        {
            $days[] = date('Y-m-d', strtotime($startDay) + 86400 * $i);
            //当前时间
            $startTime = date('Y-m-d', strtotime($startDay) + 86400 * $i);
            $endTime = date('Y-m-d', strtotime($startTime) + 86400);
            //7天每天的访问量
            $resPv[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
            //7天每天的独立访问量
            $resIp[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'visit'=>1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();

        }
        $tmp1 = 0;
        $tmp2 = 0;
        foreach($resPv as $item)
        {
            $tmp1 += $item;
        }
        foreach($resIp as $item)
        {
            $tmp2 += $item;
        }
        $sum[] = $tmp1;
        $sum[] = $tmp2;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $resPv;
        $result['data']['item'][] = $resIp;
        HttpResponseUtil::setJsonResponse($result);
    }
    //最近7天按照天数分析pv和ip
    //最近30天按照天分析
    //最近时间区间内按照天数分析

    //按照7天分析数据
    public function actionTrendWeeks()
    {
        $request = Yii::$app->request;
        $startDay = $request->post('startTime');
        $endDay = $request->post('endTime');
        $date = (strtotime($endDay) - strtotime($startDay))/86400 + 1;
        //需要循环的此时
        $count = intval($date%7 == 0 ?  $date/7 :$date/7+1);
        $appkey = $request->post('appkey');
        if($count == 0)
        {
            return;
        }
//        总数
        $spv = 0;
        $sip = 0;
        for($i = 0; $i <  $count; $i++)
        {
            if($i * 7 + 6 < $date)
            {
                //日期区间的显示，开始时间
                $weeks[] = date('Y-m-d',strtotime($startDay) + $i*7*86400).' - '.date('Y-m-d',strtotime($startDay) + $i*7*86400 + 86400 * 6);
                $pv = 0;
                $ip = 0;
                for($j = 0; $j < 7; $j++)
                {
                    $startTime = date('Y-m-d',strtotime($startDay) + ($i*7+$j) * 86400);
                    $endTime = date('Y-m-d',strtotime($startDay) + ($i*7+$j+1) * 86400);
                    //每天的访问量
                    $pv += Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                    //每天的独立ip数
                    $ip += Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
//                    Yii::error($startTime);
//                    Yii::error($endTime);
                }
            }
            else
            {
                //日期区间的显示，开始时间
                $weeks[] = date('Y-m-d',strtotime($startDay) + 86400 * ($i)*7).' - '.$endDay;
                $pv = 0;
                $ip = 0;
                for($j = 0; $j < $date % 7; $j++)
                {
                    $startTime = date('Y-m-d',strtotime($startDay) + ($i*7+$j) * 86400);
                    $endTime = date('Y-m-d',strtotime($startDay) + ($i*7+$j+1) * 86400);
                    //每天的访问量
                    $pv += Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                    //每天的独立ip数
                    $ip += Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
//                    Yii::error($startTime);
//                    Yii::error($endTime);
                }
            }
            //7天每天的访问量
            $resPv[] = $pv;
            //7天每天的独立访问量
            $resIp[] = $ip;
            $spv += $pv;
            $sip += $ip;
        }
        $sum[] = $spv;
        $sum[] = $sip;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $weeks;
        $result['data']['item'][] = $resPv;
        $result['data']['item'][] = $resIp;
        HttpResponseUtil::setJsonResponse($result);
    }
    //最近30天按照星期分析
    //区间时间内按照星期分析
    public function actionTrendMonths()
    {
        $request = Yii::$app->request;
        $startDay = $request->post('startTime');
        $endDay = $request->post('endTime');
        $appkey = $request->post('appkey');
        if(date('Y-m',strtotime($startDay)) == date('Y-m',strtotime($endDay)))
        {
            //同一个月不能分析
            $result['code'] = 201;
            $result['data']['message'] = '时间区间未跨过两个月';
            HttpResponseUtil::setJsonResponse($result);
            return;
        }
        //将日期改成数组
        $startDayA = explode('-',$startDay);
        $endDayA = explode('-',$endDay);
        //中间的月数
        $months =  ($endDayA[0] - $startDayA[0])*12 + $endDayA[1] - $startDayA[1];
        //总数统计
        $sIP = 0;
        $sPv = 0;
        for($i = 0; $i <= $months; $i++)
        {
            $res = $startDayA[0].'-'.$startDayA[1];
            //获取所有的月份
            $month[$i] = date('Y-m',strtotime($res));
            if($startDayA[1] % 12 == 0)
            {
                $startDayA[0]++;
                $startDayA[1] = 1;
            }
            else
            {
                $startDayA[1]++;
            }
            $sql = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['like','time',$month[$i]]);
            $tmp = $sql->count();
            $resPv[] = $tmp;
            $sPv += $tmp;
            $tmps = $sql->andWhere(['visit'=>1])->count();
            $resIp[] = $tmps;
            $sIP += $tmps;
        }
        $sum[] = $sPv;
        $sum[] = $sIP;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $month;
        $result['data']['item'][] = $resPv;
        $result['data']['item'][] = $resIp;
        HttpResponseUtil::setJsonResponse($result);
    }

    //比较两个时间按照小时分析ip/pv
    public function actionCompareHourPv()
    {
        $request = Yii::$app->request;
        $compareStartDay = $request->post('compareStartDay');
        $compareEndDay = $request->post('compareEndDay');
        $comparedStartDay = $request->post('comparedStartDay');
        $comparedEndDay = date('Y-m-d',strtotime($comparedStartDay) + strtotime($compareEndDay) - strtotime($compareStartDay));
        $appkey = $request->post('appkey');
        //当前时间
        if(strtotime($compareEndDay) == strtotime($compareStartDay))
        {
            $date[] = $compareStartDay;
            $date[] = $comparedStartDay;
        }
        else
        {
            $date[] = $compareStartDay.' - '.$compareEndDay;
            $date[] = $comparedStartDay.' - '.$comparedEndDay;
        }
        $compareEndDay = date('Y-m-d',strtotime($compareEndDay) + 86400);
        $comparedEndDay = date('Y-m-d',strtotime($comparedEndDay) + 86400);
        for($i = 0; $i < 24; $i++)
        {
            if ($i < 10)
            {
                $hours[] = '0' . $i . ':00 - 0' . $i . ':59';
            }
            else
            {
                $hours[] = $i . ':00 - ' . $i . ':59';
            }
            //时间1各个小时的访问量
            $res1[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'HOUR(time)'=>$i])->andWhere(['>=','time',$compareStartDay])->andWhere(['<','time',$compareEndDay])->count();
            //时间2各个小时的访问量
            $res2[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'HOUR(time)'=>$i])->andWhere(['>=','time',$comparedStartDay])->andWhere(['<','time',$comparedEndDay])->count();
        }
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$compareStartDay])->andWhere(['<','time',$compareEndDay])->count();
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$comparedStartDay])->andWhere(['<','time',$comparedEndDay])->count();
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $date;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $res1;
        $result['data']['item'][] = $res2;
        HttpResponseUtil::setJsonResponse($result);
    }

    public function actionCompareHourIp()
    {
        $request = Yii::$app->request;
        $compareStartDay = $request->post('compareStartDay');
        $compareEndDay = $request->post('compareEndDay');
        $comparedStartDay = $request->post('comparedStartDay');
        $comparedEndDay = date('Y-m-d',strtotime($comparedStartDay) + strtotime($compareEndDay) - strtotime($compareStartDay));
        $appkey = $request->post('appkey');
        //当前时间
        if(strtotime($compareEndDay) == strtotime($compareStartDay))
        {
            $date[] = $compareStartDay;
            $date[] = $comparedStartDay;
        }
        else
        {
            $date[] = $compareStartDay.' - '.$compareEndDay;
            $date[] = $comparedStartDay.' - '.$comparedEndDay;
        }
        $compareEndDay = date('Y-m-d',strtotime($compareEndDay) + 86400);
        $comparedEndDay = date('Y-m-d',strtotime($comparedEndDay) + 86400);
        for($i = 0; $i < 24; $i++)
        {
            if ($i < 10)
            {
                $hours[] = '0' . $i . ':00 - 0' . $i . ':59';
            }
            else
            {
                $hours[] = $i . ':00 - ' . $i . ':59';
            }
            //时间1各个小时的访问量
            $res1[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'HOUR(time)'=>$i,'visit'=>1])->andWhere(['>=','time',$compareStartDay])->andWhere(['<','time',$compareEndDay])->count();
            //时间2各个小时的访问量
            $res2[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'HOUR(time)'=>$i,'visit'=>1])->andWhere(['>=','time',$comparedStartDay])->andWhere(['<','time',$comparedEndDay])->count();
        }
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'visit'=>1])->andWhere(['>=','time',$compareStartDay])->andWhere(['<','time',$compareEndDay])->count();
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'visit'=>1])->andWhere(['>=','time',$comparedStartDay])->andWhere(['<','time',$comparedEndDay])->count();
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $date;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $res1;
        $result['data']['item'][] = $res2;
        HttpResponseUtil::setJsonResponse($result);
    }

    //比较两个时间按照天分析ip/pv
    public function actionCompareDayPv()
    {
        $request = Yii::$app->request;
        $compareStartDay = $request->post('compareStartDay');
        $compareEndDay = $request->post('compareEndDay');
        $comparedStartDay = $request->post('comparedStartDay');
        $comparedEndDay = date('Y-m-d',strtotime($comparedStartDay) + strtotime($compareEndDay) - strtotime($compareStartDay));
        //当前时间
        if(strtotime($compareEndDay) == strtotime($compareStartDay))
        {
            $date[] = $compareStartDay;
            $date[] = $comparedStartDay;
        }
        else
        {
            $date[] = $compareStartDay.' - '.$compareEndDay;
            $date[] = $comparedStartDay.' - '.$comparedEndDay;
        }
        $compareEndDay = date('Y-m-d',strtotime($compareEndDay) + 86400);
        $appkey = $request->post('appkey');
        $days = (strtotime($compareEndDay) - strtotime($compareStartDay)) / 86400;
        //分析的总数
        $sPv1 = 0;
        $sPv2 = 0;
        for ($i = 0; $i < $days; $i++)
        {
            $day[] = date('Y-m-d',strtotime($compareStartDay)+86400*$i).'与'.date('Y-m-d',strtotime($comparedStartDay)+86400*$i);
            //比较的数据
            $startTime = date('Y-m-d H:i:s',strtotime($compareStartDay) + $i * 86400);
            $endTime = date('Y-m-d H:i:s',strtotime($compareStartDay) + $i * 86400 + 86400);
            $tmp = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
            $res1[] = $tmp;
            $sPv1 += $tmp;
            //被比较的数据
            $startTime = date('Y-m-d H:i:s',strtotime($comparedStartDay) + $i * 86400);
            $endTime = date('Y-m-d H:i:s',strtotime($comparedStartDay) + $i * 86400 + 86400);
            $tmps = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
            $res2[] = $tmps;
            $sPv2 += $tmps;
        }
        $sum[] = $sPv1;
        $sum[] = $sPv2;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $date;
        $result['data']['item'][] = $day;
        $result['data']['item'][] = $res1;
        $result['data']['item'][] = $res2;
        HttpResponseUtil::setJsonResponse($result);
    }

    public function actionCompareDayIp()
    {
        $request = Yii::$app->request;
        $compareStartDay = $request->post('compareStartDay');
        $compareEndDay = $request->post('compareEndDay');
        $comparedStartDay = $request->post('comparedStartDay');
        $comparedEndDay = date('Y-m-d',strtotime($comparedStartDay) + strtotime($compareEndDay) - strtotime($compareStartDay));
        //当前时间
        if(strtotime($compareEndDay) == strtotime($compareStartDay))
        {
            $date[] = $compareStartDay;
            $date[] = $comparedStartDay;
        }
        else
        {
            $date[] = $compareStartDay.' - '.$compareEndDay;
            $date[] = $comparedStartDay.' - '.$comparedEndDay;
        }
        $compareEndDay = date('Y-m-d',strtotime($compareEndDay) + 86400);
        $appkey = $request->post('appkey');
        $days = (strtotime($compareEndDay) - strtotime($compareStartDay)) / 86400;
        //分析的总数
        $sIp1 = 0;
        $sIp2 = 0;
        for ($i = 0; $i < $days; $i++)
        {
            $day[] = date('Y-m-d',strtotime($compareStartDay)+86400*$i).'与'.date('Y-m-d',strtotime($comparedStartDay)+86400*$i);
            //比较的数据
            $startTime = date('Y-m-d H:i:s',strtotime($compareStartDay) + $i * 86400);
            $endTime = date('Y-m-d H:i:s',strtotime($compareStartDay) + $i * 86400 + 86400);
            $tmp = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'visit'=>1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
            $res1[] = $tmp;
            $sIp1 += $tmp;
            //被比较的数据
            $startTime = date('Y-m-d H:i:s',strtotime($comparedStartDay) + $i * 86400);
            $endTime = date('Y-m-d H:i:s',strtotime($comparedStartDay) + $i * 86400 + 86400);
            $tmps = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'visit'=>1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
            $res2[] = $tmps;
            $sIp2 += $tmps;
        }
        $sum[] = $sIp1;
        $sum[] = $sIp2;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $date;
        $result['data']['item'][] = $day;
        $result['data']['item'][] = $res1;
        $result['data']['item'][] = $res2;
        HttpResponseUtil::setJsonResponse($result);
    }

    //比较两个时间按照星期分析ip/pv
    public function actionCompareWeekPv()
    {
        $request = Yii::$app->request;
        $compareStartDay = $request->post('compareStartDay');
        $compareEndDay = $request->post('compareEndDay');
        $comparedStartDay = $request->post('comparedStartDay');
        $comparedEndDay = date('Y-m-d',strtotime($comparedStartDay) + strtotime($compareEndDay) - strtotime($compareStartDay));
        $date = (strtotime($compareEndDay)-strtotime($compareStartDay)) / (86400) + 1;
        //需要循环的此时
        $count = intval($date%7 == 0 ?  $date/7 :$date/7+1);
        $appkey = $request->post('appkey');
        if($count == 0)
        {
            return;
        }
        for($i = 0; $i <  $count; $i++)
        {
            if($i * 7 + 6 < $date)
            {
                //日期区间的显示，开始时间
                $start = date('Y-m-d',strtotime($compareStartDay) + $i*7*86400).' - '.date('Y-m-d',strtotime($compareStartDay) + $i*7*86400 + 86400 * 6);
                //时间1
                $startTime = date('Y-m-d',strtotime($compareStartDay) + $i*7*86400);
                $endTime = date('Y-m-d',strtotime($startTime) + 86400 * 7);
                /*$logger = MMLogger::getLogger(__FUNCTION__);
                $logger->error($startTime);
                $logger->error($endTime);*/
                $res1[] =  Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                //时间2
                $startTime = date('Y-m-d',strtotime($comparedStartDay) + $i*7*86400);
                $endTime = date('Y-m-d',strtotime($startTime) + 86400 * 7);
                $res2[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                $weeks[] = $start.'与'.date('Y-m-d',strtotime($comparedStartDay) + $i*7*86400).' - '.date('Y-m-d',strtotime($comparedStartDay) + $i*7*86400 + 86400 * 6);
            }
            else
            {
                //日期区间的显示，开始时间
                $start = date('Y-m-d',strtotime($compareStartDay) + 86400 * ($i)*7).' - '.$compareEndDay;
                //时间1
                $startTime = date('Y-m-d',strtotime($compareStartDay) + ($i*7) * 86400);
                $endTime = $compareEndDay;
                $res1[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                //时间2
                $startTime = date('Y-m-d',strtotime($comparedStartDay) + ($i*7) * 86400);
                $endTime = $comparedEndDay;
                $res2[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                $weeks[] = $start.'与'.date('Y-m-d',strtotime($comparedStartDay) + 86400 * ($i)*7).' - '.$comparedEndDay;
            }
        }
        $day[] = $compareStartDay .' - '.$compareEndDay;
        $day[] = $comparedStartDay .' - '.$comparedEndDay;
        $tmp1 = 0;
        $tmp2 = 0;
        foreach($res1 as $item)
        {
            $tmp1 += $item;
        }
        foreach($res2 as $item)
        {
            $tmp2 += $item;
        }
        $sum[] = $tmp1;
        $sum[] = $tmp2;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $day;
        $result['data']['item'][] = $weeks;
        $result['data']['item'][] = $res1;
        $result['data']['item'][] = $res2;
        HttpResponseUtil::setJsonResponse($result);
    }

    public function actionCompareWeekIp()
    {
        $request = Yii::$app->request;
        $compareStartDay = $request->post('compareStartDay');
        $compareEndDay = $request->post('compareEndDay');
        $comparedStartDay = $request->post('comparedStartDay');
        $comparedEndDay = date('Y-m-d',strtotime($comparedStartDay) + strtotime($compareEndDay) - strtotime($compareStartDay));
        $date = (strtotime($compareEndDay)-strtotime($compareStartDay)) / (86400) + 1;
        //需要循环的此时
        $count = intval($date%7 == 0 ?  $date/7 :$date/7+1);
        $appkey = $request->post('appkey');
        if($count == 0)
        {
            return;
        }
        for($i = 0; $i <  $count; $i++)
        {
            $compareIp = 0;
            $comparedIp = 0;
            if($i * 7 + 6 < $date)
            {
                //初始化定义区间内独立ip量
                for($j = 0; $j < 7 ; $j++)
                {
                    //时间1
                    $startTime = date('Y-m-d',strtotime($compareStartDay) + $i*7*86400 + 86400*$j);
                    $endTime = date('Y-m-d',strtotime($startTime) + 86400);
                    $compareIp +=  Scount::find()->where(['appkey' => $appkey,'type'=> 1,'visit'=>1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                    //时间2
                    $startTime = date('Y-m-d',strtotime($comparedStartDay) + $i*7*86400 + 86400*$j);
                    $endTime = date('Y-m-d',strtotime($startTime) + 86400);
                    $comparedIp +=  Scount::find()->where(['appkey' => $appkey,'type'=> 1,'visit'=>1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                }
                //日期区间的显示，开始时间
                $start = date('Y-m-d',strtotime($compareStartDay) + $i*7*86400).' - '.date('Y-m-d',strtotime($compareStartDay) + $i*7*86400 + 86400 * 6);
                /*$logger = MMLogger::getLogger(__FUNCTION__);
                $logger->error($startTime);
                $logger->error($endTime);*/
                $weeks[] = $start.'与'.date('Y-m-d',strtotime($comparedStartDay) + $i*7*86400).' - '.date('Y-m-d',strtotime($comparedStartDay) + $i*7*86400 + 86400 * 6);
            }
            else
            {
                for($j = 0; $j < $date % 7; $j++ )
                {
                    //时间1
                    $startTime = date('Y-m-d',strtotime($compareStartDay) + ($i*7+$j) * 86400);
                    $endTime = date('Y-m-d',strtotime($startTime) + 86400);
                    $comparedIp += Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
                    //时间2
                    $startTime = date('Y-m-d',strtotime($comparedStartDay) + ($i*7+$j) * 86400);
                    $endTime = date('Y-m-d',strtotime($startTime) + 86400);
                    $res2[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                }
                //日期区间的显示，开始时间
                $start = date('Y-m-d',strtotime($compareStartDay) + 86400 * ($i)*7).' - '.$compareEndDay;

                $weeks[] = $start.'与'.date('Y-m-d',strtotime($comparedStartDay) + 86400 * ($i)*7).' - '.$comparedEndDay;
            }
            $res1[] = $compareIp;
            $res2[] = $comparedIp;
        }
        $day[] = $compareStartDay .' - '.$compareEndDay;
        $day[] = $comparedStartDay .' - '.$comparedEndDay;
        $tmp1 = 0;
        $tmp2 = 0;
        foreach($res1 as $item)
        {
            $tmp1 += $item;
        }
        foreach($res2 as $item)
        {
            $tmp2 += $item;
        }
        $sum[] = $tmp1;
        $sum[] = $tmp2;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $day;
        $result['data']['item'][] = $weeks;
        $result['data']['item'][] = $res1;
        $result['data']['item'][] = $res2;
        HttpResponseUtil::setJsonResponse($result);
    }

    //比较两个时间按照星期分析ip/pv
    public function actionCompareMonthPv()
    {
        $logger = MMLogger::getLogger(__FUNCTION__);
        $request = Yii::$app->request;
        $compareStartDay = $request->post('compareStartDay');
        $compareEndDay = $request->post('compareEndDay');
        //中间相差的天数
        $days = (strtotime($compareEndDay) - strtotime($compareStartDay)) / 86400;
        $comparedStartDay = $request->post('comparedStartDay');
        //求出比较的结束时间
        $comparedEndDay = date("Y-m-d",strtotime($comparedStartDay) + 86400 * $days);
        $appkey = $request->post('appkey');
        if(date('Y-m',strtotime($compareStartDay)) == date('Y-m',strtotime($compareEndDay)))
        {
            //同一个月不能分析
            $result['code'] = 201;
            $result['data']['message'] = '时间区间未跨过两个月';
            HttpResponseUtil::setJsonResponse($result);
            return;
        }
        //将日期改成数组
        $compareStartDayA = explode('-',$compareStartDay);
        $compareEndDayA = explode('-',$compareEndDay);
        $comparedStartDayA = explode('-',$comparedStartDay);
        //中间的月数
        $months =  ($compareEndDayA[0] - $compareStartDayA[0])*12 + $compareEndDayA[1] - $compareStartDayA[1]+1;
//        $logger->error($months);
        for($i = 0 ; $i < $months; $i++)
        {
            $res = $compareStartDayA[0].'-'.$compareStartDayA[1];
            //获取时间1所有的月份
            $month1[$i] = date('Y-m',strtotime($res));
            if($compareStartDayA[1] % 12 == 0)
            {
                $compareStartDayA[0]++;
                $compareStartDayA[1] = 1;
            }
            else
            {
                $compareStartDayA[1]++;
            }
            $res1[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['like','time',$month1[$i]])->count();

            $res = $comparedStartDayA[0].'-'.$comparedStartDayA[1];
//            $logger->error($res);
            //获取时间1所有的月份
            $month2[$i] = date('Y-m',strtotime($res));
            if($comparedStartDayA[1] % 12 == 0)
            {
                $comparedStartDayA[0]++;
                $comparedStartDayA[1] = 1;
            }
            else
            {
                $comparedStartDayA[1]++;
            }
            $res2[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['like','time',$month2[$i]])->count();
            $month[] = $month1[$i].'与'.$month2[$i];
        }
        $day[] = $compareStartDay .' - '.$compareEndDay;
        $day[] = $comparedStartDay .' - '.$comparedEndDay;
        $tmp1 = 0;
        $tmp2 = 0;
        foreach($res1 as $item)
        {
            $tmp1 += $item;
        }
        foreach($res2 as $item)
        {
            $tmp2 += $item;
        }
        $sum[] = $tmp1;
        $sum[] = $tmp2;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $day;
        $result['data']['item'][]=$month;
        $result['data']['item'][]=$res1;
        $result['data']['item'][]=$res2;
        HttpResponseUtil::setJsonResponse($result);
    }

    public function actionCompareMonthIp()
    {
        $request = Yii::$app->request;
        $compareStartDay = $request->post('compareStartDay');
        $compareEndDay = $request->post('compareEndDay');
        //中间相差的天数
        $days = (strtotime($compareEndDay) - strtotime($compareStartDay)) / 864000;
        $comparedStartDay = $request->post('comparedStartDay');
        //求出比较的结束时间
        $comparedEndDay = date("Y-m-d",strtotime($comparedStartDay) + 86400 * $days);
        $appkey = $request->post('appkey');
        if(date('Y-m',strtotime($compareStartDay)) == date('Y-m',strtotime($compareEndDay)))
        {
            //同一个月不能分析
            $result['code'] = 201;
            $result['data']['message'] = '时间区间未跨过两个月';
            HttpResponseUtil::setJsonResponse($result);
            return;
        }
        //将日期改成数组
        $compareStartDayA = explode('-',$compareStartDay);
        $compareEndDayA = explode('-',$compareEndDay);
        $comparedStartDayA = explode('-',$comparedStartDay);
        //中间的月数
        $months =  ($compareEndDayA[0] - $compareStartDayA[0])*12 + $compareEndDayA[1] - $compareStartDayA[1];
        for($i = 0 ; $i <= $months; $i++)
        {
            $res = $compareStartDayA[0].'-'.$compareStartDayA[1];
            //获取时间1所有的月份
            $month1[$i] = date('Y-m',strtotime($res));
            if($compareStartDayA[1] % 12 == 0)
            {
                $compareStartDayA[0]++;
                $compareStartDayA[1] = 1;
            }
            else
            {
                $compareStartDayA[1]++;
            }
            $res1[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'visit'=>1])->andWhere(['like','time',$month1[$i]])->count();
            $res = $comparedStartDayA[0].'-'.$comparedStartDayA[1];
            //获取时间1所有的月份
            $month2[$i] = date('Y-m',strtotime($res));
            if($comparedStartDayA[1] % 12 == 0)
            {
                $comparedStartDayA[0]++;
                $comparedStartDayA[1] = 1;
            }
            else
            {
                $comparedStartDayA[1]++;
            }
            $month[] = $month1[$i].'与'.$month2[$i];
            $res2[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'visit'=>1])->andWhere(['like','time',$month2[$i]])->count();
        }
        $day[] = $compareStartDay .' - '.$compareEndDay;
        $day[] = $comparedStartDay .' - '.$comparedEndDay;
        $tmp1 = 0;
        $tmp2 = 0;
        foreach($res1 as $item)
        {
            $tmp1 += $item;
        }
        foreach($res2 as $item)
        {
            $tmp2 += $item;
        }
        $sum[] = $tmp1;
        $sum[] = $tmp2;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $day;
        $result['data']['item'][]=$month;
        $result['data']['item'][]=$res1;
        $result['data']['item'][]=$res2;
        HttpResponseUtil::setJsonResponse($result);
    }












    public function actionTest()
    {
        $i = 1;
        $res = Scount::find()->where(['HOUR(time)'=>$i])->all();
        var_dump($res);
    }
}
