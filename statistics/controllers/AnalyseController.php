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
use Yii;

class AnalyseController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

//    今日和昨天访问量
    public function actionToday()
    {
        $appkey = Yii::$app->request->post('appkey');
        $startTime = date('Y-m-d',time());
        $endTime = date('Y-m-d',time() + 86400);
         Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
//      今天的记录
        $pv = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
        $ip = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
        $result['code'] = 200;
        $result['data']['domain'] = Webs::find()->where(['appkey'=>Yii::$app->request->post('appkey')])->one();
        $result['data']['today']['pv'] = $pv;
        $result['data']['today']['ip'] = $ip;
//       昨天的记录
        $pv = $this->CountPv(date('Y-m-d',time()-86400),date('Y-m-d',time()));
        $ip = $this->CountIp(date('Y-m-d',time()-86400),date('Y-m-d',time()));
        $result['data']['yesterday']['pv'] = $pv;
        $result['data']['yesterday']['ip'] = $ip;
        HttpResponseUtil::setJsonResponse($result);
    }

//按照小时分析pv
/*
 *  @ $start比较的日期
 *  @ $past被比较的日期
 * */
    private function CompareHours($start,$past,$alone = null)
    {
        $appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 24; $i++)
        {
            $hours[][] = $i;
            //比较时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60- $start * 86400);
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 + 3600 - $start * 86400);
//            被比较的时间
            $cstartTime = date('Y-m-d H:i:s',strtotime($startTime)-86400 * $past);
            $cendTime = date('Y-m-d H:i:s',strtotime($endTime)-86400 * $past);
//            判断是否为求独立访问量
            if(is_null($alone))
            {
                //要比较日期各个小时的访问量
                $ress[$i][] = date('Y-m-d',time());
                $ress[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->count();
                //统计昨天各个小时的pv量
                $resp[$i][] = date('Y-m-d',time()-86400* $past);
                $resp[$i][] = Scount::find()->where(['>=','time',$cstartTime])->andWhere(['<','time',$cendTime])->andWhere(['appkey'=>$appkey,'type'=>1])->count();
            }
            else
            {
                //要比较日期各个小时的独立访问量
                $ress[$i][] = date('Y-m-d',time());
                $ress[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->groupBy('ip')->count();
                //统计昨天各个小时的ip量
                $resp[$i][] = date('Y-m-d',time()-86400* $past);
                $resp[$i][] = Scount::find()->where(['>=','time',$cstartTime])->andWhere(['<','time',$cendTime])->andWhere(['appkey'=>$appkey,'type'=>1])->groupBy('ip')->count();
            }

        }
        $result['code'] = 200;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $ress;
        $result['data']['item'][] = $resp;
        HttpResponseUtil::setJsonResponse($result);
    }

    //今天和昨天pv相比
    public function actionTodayYesterdayPv()
    {
       $this->CompareHours(0,1);
        /* $appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 24; $i++)
        {
            $hours[][] = $i;
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60);
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 + 3600);
            //今天各个小时的访问量
            $pvt[$i][] = date('Y-m-d',time());
            $pvt[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->count();
            //统计昨天各个小时的pv量
            $pvy[$i][] = date('Y-m-d',time()-86400);
            $pvy[$i][] = Scount::find()->where(['>=','time',date('Y-m-d H:i:s',strtotime($startTime)-86400)])->andWhere(['<','time',date('Y-m-d H:i:s',strtotime($endTime)-86400)])->andWhere(['appkey'=>$appkey,'type'=>1])->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $pvt;
        $result['data']['item'][] = $pvy;
        HttpResponseUtil::setJsonResponse($result);*/
    }

    //今天和昨天ip相比
    public function actionTodayYesterdayIp()
    {
        $this->CompareHours(0,1,true);
        /*$appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 24; $i++)
        {
            $hours[][] = $i;
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60);
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 + 3600);
            //今天各个小时的访问量
            $pvt[$i][] = date('Y-m-d',time());
            $pvt[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->groupBy('ip')->count();
            //统计昨天各个小时的pv量
            $pvy[$i][] = date('Y-m-d',time()-86400);
            $pvy[$i][] = Scount::find()->where(['>=','time',date('Y-m-d H:i:s',strtotime($startTime)-86400)])->andWhere(['<','time',date('Y-m-d H:i:s',strtotime($endTime)-86400)])->andWhere(['appkey'=>$appkey,'type'=>1])->groupBy('ip')->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $pvt;
        $result['data']['item'][] = $pvy;
        HttpResponseUtil::setJsonResponse($result);*/
    }

//今天和7天前pv相比
    public function actionTodayWeekPv()
    {
        $this->CompareHours(0,6);
       /*$appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 24; $i++)
        {
            $hours[][] = $i;
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60);
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 + 3600);
            //今天各个小时的访问量
            $pvt[$i][] = date('Y-m-d',time());
            $pvt[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->count();
            //统计昨天各个小时的pv量
            $pvy[$i][] = date('Y-m-d',time()-86400*6);
            $pvy[$i][] = Scount::find()->where(['>=','time',date('Y-m-d H:i:s',strtotime($startTime)-86400*6)])->andWhere(['<','time',date('Y-m-d H:i:s',strtotime($endTime)-86400*6)])->andWhere(['appkey'=>$appkey,'type'=>1])->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $pvt;
        $result['data']['item'][] = $pvy;
        HttpResponseUtil::setJsonResponse($result);*/
    }

//今天和7天前ip相比
    public function actionTodayWeekIp()
    {
        $this->CompareHours(0,6,true);
      /* $appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 24; $i++)
        {
            $hours[][] = $i;
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60);
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 + 3600);
            //今天各个小时的访问量
            $pvt[$i][] = date('Y-m-d',time());
            $pvt[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->groupBy('ip')->count();
            //统计昨天各个小时的pv量
            $pvy[$i][] = date('Y-m-d',time()-86400*6);
            $pvy[$i][] = Scount::find()->where(['>=','time',date('Y-m-d H:i:s',strtotime($startTime)-86400*6)])->andWhere(['<','time',date('Y-m-d H:i:s',strtotime($endTime)-86400*6)])->andWhere(['appkey'=>$appkey,'type'=>1])->groupBy('ip')->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $pvt;
        $result['data']['item'][] = $pvy;
        HttpResponseUtil::setJsonResponse($result);*/
    }
    //昨天和前天pv相比
    public function actionYesterdayYesterdayPv()
    {
        $this->CompareHours(1,2);
       /* $appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 24; $i++)
        {
            $hours[][] = $i;
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 - 86400);
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 + 3600 - 86400);
            //今天各个小时的访问量
            $pvt[$i][] = date('Y-m-d',time());
            $pvt[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->count();
            //统计昨天各个小时的pv量
            $pvy[$i][] = date('Y-m-d',time()-86400*2);
            $pvy[$i][] = Scount::find()->where(['>=','time',date('Y-m-d H:i:s',strtotime($startTime)-86400*2)])->andWhere(['<','time',date('Y-m-d H:i:s',strtotime($endTime)-86400)])->andWhere(['appkey'=>$appkey,'type'=>1])->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $pvt;
        $result['data']['item'][] = $pvy;
        HttpResponseUtil::setJsonResponse($result);*/
    }

    //昨天和前天ip相比
    public function actionYesterdayYesterdayIp()
    {
        $this->CompareHours(1,2,true);
        /*$appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 24; $i++)
        {
            $hours[][] = $i;
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 - 86400);
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 + 3600 - 86400);
            //今天各个小时的访问量
            $pvt[$i][] = date('Y-m-d',time());
            $pvt[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->groupBy('ip')->count();
            //统计昨天各个小时的pv量
            $pvy[$i][] = date('Y-m-d',time()-86400 * 2);
            $pvy[$i][] = Scount::find()->where(['>=','time',date('Y-m-d H:i:s',strtotime($startTime)-86400 * 2)])->andWhere(['<','time',date('Y-m-d H:i:s',strtotime($endTime)-86400)])->andWhere(['appkey'=>$appkey,'type'=>1])->groupBy('ip')->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $pvt;
        $result['data']['item'][] = $pvy;
        HttpResponseUtil::setJsonResponse($result);*/
    }

//昨天和7天前pv相比
    public function actionYesterdayWeekPv()
    {
        $this->CompareHours(1,7);
        /*$appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 24; $i++)
        {
            $hours[][] = $i;
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 - 86400);
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 + 3600 - 86400);
            //今天各个小时的访问量
            $pvt[$i][] = date('Y-m-d',time());
            $pvt[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->count();
            //统计昨天各个小时的pv量
            $pvy[$i][] = date('Y-m-d',time()-86400*7);
            $pvy[$i][] = Scount::find()->where(['>=','time',date('Y-m-d H:i:s',strtotime($startTime)-86400*7)])->andWhere(['<','time',date('Y-m-d H:i:s',strtotime($endTime)-86400*6)])->andWhere(['appkey'=>$appkey,'type'=>1])->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $pvt;
        $result['data']['item'][] = $pvy;
        HttpResponseUtil::setJsonResponse($result);*/
    }

//昨天和7天前ip相比
    public function actionYesterdayWeekIp()
    {
        $this->CompareHours(1,7,true);
        /*  $appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 24; $i++)
        {
            $hours[][] = $i;
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 - 86400);
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 + 3600 - 86400);
            //今天各个小时的访问量
            $pvt[$i][] = date('Y-m-d',time());
            $pvt[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->groupBy('ip')->count();
            //统计昨天各个小时的pv量
            $pvy[$i][] = date('Y-m-d',time()-86400*7);
            $pvy[$i][] = Scount::find()->where(['>=','time',date('Y-m-d H:i:s',strtotime($startTime)-86400*7)])->andWhere(['<','time',date('Y-m-d H:i:s',strtotime($endTime)-86400*6)])->andWhere(['appkey'=>$appkey,'type'=>1])->groupBy('ip')->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $pvt;
        $result['data']['item'][] = $pvy;
        HttpResponseUtil::setJsonResponse($result);*/
    }

//按照日期分析
    private function Days($date,$alone = null)
    {
        $appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i <= $date; $i++)
        {
            $days[] = date('Y-m-d',time()-86400*($date - $i));
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) - ($date - $i) * 60 * 60 * 24 );
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d"))  - ($date - $i)* 60 * 60 * 24  + 86400);
            if(is_null($alone))
            {
                //每天的访问量
                $pv[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->count();
            }
            else
            {
                //每天独立的访问量
                $pv[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->groupBy('ip')->count();
            }

        }
        $result['code'] = 200;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $pv;
        HttpResponseUtil::setJsonResponse($result);
    }

//    最近7天的pv
    public function actionWeekPv()
    {
        $this->Days(6);
        /*$appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 7; $i++)
        {
            $days[] = date('Y-m-d',time()-86400*(6-$i));
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) - (6-$i) * 60 * 60 * 24 );
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d"))  - (6-$i)* 60 * 60 * 24  + 86400);
            //每天各个小时的访问量
            $pv[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $pv;
        HttpResponseUtil::setJsonResponse($result);*/
    }

//    最近7天独立访问的ip
    public function actionWeekIp()
    {
        $this->Days(6,true);
       /* $appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 7; $i++)
        {
            $days[] = date('Y-m-d',time()-86400*(6-$i));
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) - (6-$i) * 60 * 60 * 24 );
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d"))  - (6-$i)* 60 * 60 * 24  + 86400);
            //每天各个小时的访问量
            $ip[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->groupBy('ip')->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $ip;
        HttpResponseUtil::setJsonResponse($result);*/
    }

//最近30天的访问量
    public function actionMonthPv()
    {
        $this->Days(30);
       /* $appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 30; $i++)
        {
            $days[] = date('Y-m-d',time()-86400*(29-$i));
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) - (29-$i) * 60 * 60 * 24 );
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d"))  - (29-$i)* 60 * 60 * 24  + 86400);
            //每天各个小时的访问量
            $pv[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $pv;
        HttpResponseUtil::setJsonResponse($result);*/
    }

    //最近30天独立访问量
    public function actionMonthIp()
    {
        $this->Days(30,true);
       /* $appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 30; $i++)
        {
            $days[] = date('Y-m-d',time()-86400*(29-$i));
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) - (29-$i) * 60 * 60 * 24 );
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d"))  - (29-$i)* 60 * 60 * 24  + 86400);
            //每天各个小时的访问量
            $ip[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->groupBy('ip')->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $ip;
        HttpResponseUtil::setJsonResponse($result);*/
    }


    //今天按照小时分析pv和ip
    public function actionTodayHour()
    {
        $appkey = Yii::$app->request->post('appkey');
        //当前时间
        $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")));
        $endTime = date('Y-m-d H:i:s', time());
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
        $sum[] = Scount::find()->where(['>=','time',date('Y-m-d',time())])->andWhere(['appkey'=>$appkey,'type'=> 1])->count();
        $sum[] = Scount::find()->where(['>=','time',date('Y-m-d',time())])->andWhere(['appkey'=>$appkey,'type'=> 1])->groupBy('ip')->count();
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

    //昨日按照小时分析pv和ip
    public function actionYesterdayHour()
    {
        $appkey = Yii::$app->request->post('appkey');
        //当前时间
        $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) - 86400);
        $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")));
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

    //最近7天按照小时分析pv和ip
    public function actionWeekHour()
    {
        $appkey = Yii::$app->request->post('appkey');
        //当前时间
        $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) - 86400*6);
        $endTime = date('Y-m-d H:i:s', time());
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
            //7天各个小时的访问量
            $res[$i][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'hour'=>$i])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
            //7天各个小时的独立访问量
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

//    最近7天按照星期分析pv和ip
    public function actionWeekDay()
    {
        $appkey = Yii::$app->request->post('appkey');
        //当前时间
        $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) - 86400*6);
        $endTime = date('Y-m-d H:i:s', time());
        for($i = 1; $i <= 7; $i++)
        {
            $days[] = date('Y-m-d',time()-86400*(7-$i));
            //7天每天的访问量
            $res[$i-1][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'week'=>$i])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
            //7天每天的独立访问量
            $res[$i-1][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'week'=>$i])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
        }
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

    //最近30天按照小时分析
    public function actionMonthHour()
    {
        $appkey = Yii::$app->request->post('appkey');
        //当前时间
        $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) - 86400*29);
        $endTime = date('Y-m-d H:i:s', time());
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
            //7天各个小时的访问量
            $res[$i][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'hour'=>$i])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
            //7天各个小时的独立访问量
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

    //最近30天按照天分析
    public function actionMonthDay()
    {
        $appkey = Yii::$app->request->post('appkey');
        for($i = 1; $i <= 30; $i++)
        {
            $days[] = date('Y-m-d',time()-86400*(30-$i));
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) - 86400*(30-$i));
            $endTime = date('Y-m-d', time() - 86400*(29-$i));
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

    //最近30天按照星期分析
    public function actionMonthWeek()
    {
        $appkey = Yii::$app->request->post('appkey');
        for($i = 1; $i <= 5; $i++)
        {
            $weeks[] = date('Y-m-d',time()- (29-(7*($i-1))) * 86400).' - '.date('Y-m-d',time()- ((29-(7*$i-1))<0 ? 0 : (29-(7*$i-1))) * 86400);
            $startTime = date('Y-m-d',time()- (29-(7*($i-1))) * 86400);
            $endTime = date('Y-m-d',time()- ((29-(7*$i-1))<0 ? 0 : (29-(7*$i-1))) * 86400);
            //7天每天的访问量
            $res[$i-1][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
            //7天每天的独立访问量
            $res[$i-1][] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
        }
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
        $sum[] = Scount::find()->where(['appkey' => $appkey,'type'=> 1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $weeks;
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

//    具体的某一天的分析
    public function actionDate()
    {
        $date = Yii::$app->request->post('date');
        $appkey = Yii::$app->request->post('appkey');
        $startTime = date('Y-m-d',strtotime($date));
        $endTime = date('Y-m-d',strtotime($date)-86400);
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
        $sum[] = Scount::find()->where(['>=','time',date('Y-m-d',time())])->andWhere(['appkey'=>$appkey,'type'=> 1])->count();
        $sum[] = Scount::find()->where(['>=','time',date('Y-m-d',time())])->andWhere(['appkey'=>$appkey,'type'=> 1])->groupBy('ip')->count();
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

//某个时间区间按照小时分析
    public function actionRange()
    {
        $appkey = Yii::$app->request->post('appkey');
        $startTime = Yii::$app->request->post('startTime');
        $endTime = Yii::$app->request->post('endTime');
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
        $sum[] = Scount::find()->where(['>=','time',date('Y-m-d',time())])->andWhere(['appkey'=>$appkey,'type'=> 1])->count();
        $sum[] = Scount::find()->where(['>=','time',date('Y-m-d',time())])->andWhere(['appkey'=>$appkey,'type'=> 1])->groupBy('ip')->count();
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }















    public function actionTest()
    {
        var_dump(json_decode('{"status":0,"msg":"","data":{"timeSpan":["2016\/12\/19","2016\/12\/18"],"fields":["simple_date_title","time","pv_count"],"items":[[[0],[1],[2],[3],[4],[5],[6],[7],[8],[9],[10],[11],[12],[13],[14],[15],[16],[17],[18],[19],[20],[21],[22],[23]],[["2016\/12\/19",23],["2016\/12\/19",14],["2016\/12\/19",4],["2016\/12\/19",4],["2016\/12\/19",1],["2016\/12\/19",6],["2016\/12\/19",4],["2016\/12\/19",15],["2016\/12\/19",93],["2016\/12\/19",162],["2016\/12\/19",188],["2016\/12\/19",15],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"]],[["2016\/12\/18",31],["2016\/12\/18",10],["2016\/12\/18",6],["2016\/12\/18",10],["2016\/12\/18",10],["2016\/12\/18",1],["2016\/12\/18",6],["2016\/12\/18",11],["2016\/12\/18",52],["2016\/12\/18",66],["2016\/12\/18",87],["2016\/12\/18",77],["2016\/12\/18",44],["2016\/12\/18",44],["2016\/12\/18",84],["2016\/12\/18",100],["2016\/12\/18",66],["2016\/12\/18",51],["2016\/12\/18",26],["2016\/12\/18",35],["2016\/12\/18",59],["2016\/12\/18",60],["2016\/12\/18",31],["2016\/12\/18",32]],[]]}}'));
    }
}
