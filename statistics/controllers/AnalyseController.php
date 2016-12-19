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
    public function actionHome()
    {

    }

//    今日和昨天访问量
    public function actionToday()
    {
        $appkey = Yii::$app->request->post('appkey');
//        今天的记录
        $pv = Scount::find()->where(['>=','time',date('Y-m-d',time())])->andWhere(['appkey'=>$appkey])->andWhere(['type'=>1])->count();
        $ip = Scount::find()->where(['>=','time',date('Y-m-d',time())])->andWhere(['appkey'=>$appkey])->andWhere(['type'=>1])->groupBy('ip')->count();
        $result['code'] = 200;
        $result['data']['domain'] = Webs::find()->where(['appkey'=>$appkey])->one();
        $result['data']['today']['pv'] = $pv;
        $result['data']['today']['ip'] = $ip;
//        昨天的记录
        $pv = Scount::find()->where(['>=','time',date('Y-m-d',time()-86400)])->andWhere(['appkey'=>$appkey])->andWhere(['<=','time',date('Y-m-d',time())])->andWhere(['type'=>1])->count();
        $ip = Scount::find()->where(['>=','time',date('Y-m-d',time()-86400)])->andWhere(['appkey'=>$appkey])->andWhere(['<=','time',date('Y-m-d',time())])->andWhere(['type'=>1])->groupBy('ip')->count();
        $result['data']['yesterday']['pv'] = $pv;
        $result['data']['yesterday']['ip'] = $ip;
        HttpResponseUtil::setJsonResponse($result);
    }

    //过去7天访问量
    public function actionWeek()
    {
        $pv = $this->pastDay(Daycount::find(),7)->andWhere(['type'=>1])->count();
        $ip = $this->pastDay(Daycount::find(),7)->andWhere(['type'=>1])->groupBy('ip')->count();
        $result['code'] = 200;
        $result['pv'] = $pv;
        $result['ip'] = $ip;
        HttpResponseUtil::setJsonResponse($result);
    }

//    过去30天访问量
    public function actionMonth()
    {
        $pv = $this->pastDay(Daycount::find(),30)->andWhere(['type'=>1])->count();
        $ip = $this->pastDay(Daycount::find(),30)->andWhere(['type'=>1])->groupBy('ip')->count();
        $result['code'] = 200;
        $result['pv'] = $pv;
        $result['ip'] = $ip;
        HttpResponseUtil::setJsonResponse($result);
    }

    //今天和昨天pv
    public function actionHoursPv()
    {
        $appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 24; $i++)
        {
            $hours[] = $i;
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60);
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 + 3600);
            //今天各个小时的访问量
            $pvt[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<=', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->count();
            $pvt[$i][] = date('Y-m-d',time());
            //统计昨天各个小时的pv量
            $pvy[$i][] = Daycount::find()->where(['>=','time',date('Y-m-d H:i:s',$startTime-86400)])->andWhere(['<=','time',$endTime-86400])->andWhere(['appkey'=>$appkey])->andWhere(['type'=>1])->count();
            $pvy[$i][] = date('Y-m-d',time()-86400);
        }
        $result['code'] = 200;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $pvt;
        $result['data']['item'][] = $pvy;
        HttpResponseUtil::setJsonResponse($result);
    }

    //今天和昨天的独立访问量
    public function actionHoursIp()
    {
        $appkey = Yii::$app->request->post('appkey');
        for($i = 0; $i < 24; $i++)
        {
            $hours[] = $i;
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60);
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 + 3600);
            //今天各个小时的独立访问量
            $ipt[$i][] = Scount::find()->where(['>=','time',$startTime])->andWhere(['<=','time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type'=>1])->groupBy('ip')->count();
            $ipt[$i][] = date('Y-m-d',time());
            //统计昨天各个小时的独立访问量
            $ipy[$i][] = Scount::find()->where(['>=','time',$startTime])->andWhere(['<=','time',$endTime-86400])->andWhere((['appkey'=>$appkey]))->andWhere(['type'=>1])->groupBy('ip')->count();
            $ipy[$i][] = date('Y-m-d',time()-86400);
        }
        $result['code'] = 200;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $ipt;
        $result['data']['item'][] = $ipy;
        HttpResponseUtil::setJsonResponse($result);
    }

//    最近7天的pv
    public function actionWeekPv()
    {
        $appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 7; $i++)
        {
            $days[] = date('Y-m-d',time()-86400*(6-$i));
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) - (6-$i) * 60 * 60 * 24 );
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d"))  - (6-$i)* 60 * 60 * 24  + 86400);
            //每天各个小时的访问量
            $pv[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<=', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $pv;
        HttpResponseUtil::setJsonResponse($result);
    }

//    最近7天独立访问的ip
    public function actionWeekIp()
    {
        $appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 7; $i++)
        {
            $days[] = date('Y-m-d',time()-86400*(6-$i));
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) - (6-$i) * 60 * 60 * 24 );
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d"))  - (6-$i)* 60 * 60 * 24  + 86400);
            //每天各个小时的访问量
            $ip[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<=', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->groupBy('ip')->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $ip;
        HttpResponseUtil::setJsonResponse($result);
    }

//最近30天的访问量
    public function actionMonthPv()
    {
        $appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 30; $i++)
        {
            $days[] = date('Y-m-d',time()-86400*(29-$i));
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) - (29-$i) * 60 * 60 * 24 );
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d"))  - (29-$i)* 60 * 60 * 24  + 86400);
            //每天各个小时的访问量
            $pv[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<=', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $pv;
        HttpResponseUtil::setJsonResponse($result);
    }

    //最近30天独立访问量
    public function actionMonthIp()
    {
        $appkey = Yii::$app->request->post('appkey');
        for ($i = 0; $i < 30; $i++)
        {
            $days[] = date('Y-m-d',time()-86400*(29-$i));
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) - (29-$i) * 60 * 60 * 24 );
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d"))  - (29-$i)* 60 * 60 * 24  + 86400);
            //每天各个小时的访问量
            $ip[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<=', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => 1])->groupBy('ip')->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $ip;
        HttpResponseUtil::setJsonResponse($result);
    }


    //今天按照小时分析pv和ip
    public function actionTodayHour()
    {
        $appkey = Yii::$app->request->post('appkey');
        for($i = 0; $i < 24; $i++)
        {
            if ($i < 10) {
                $hours[][] = '0' . $i . ':00 - 0' . $i . ':59';
            } else {
                $hours[][] = $i . ':00 - ' . $i . ':59';
            }
            //当前时间
            $startTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60);
            $endTime = date('Y-m-d H:i:s', strtotime(date("Y-m-d")) + $i * 60 * 60 + 3600);
            //每天各个小时的访问量
            $pv[$i][] = Scount::find()->where(['>=', 'time', $startTime])->andWhere(['<=', 'time', $endTime])->andWhere(['appkey' => $appkey,'type'=> 1])->count();
            //每天各个小时的独立访问量
            $pv[$i][] = Scount::find()->where(['>=', 'time', $startTime])->andWhere(['<=', 'time', $endTime])->andWhere(['appkey' => $appkey,'type'=> 1])->groupBy('ip')->count();
        }
        $sum[] = Scount::find()->where(['>=','time',date('Y-m-d',time())])->andWhere(['appkey'=>$appkey,'type'=> 1])->count();
        $sum[] = Scount::find()->where(['>=','time',date('Y-m-d',time())])->andWhere(['appkey'=>$appkey,'type'=> 1])->groupBy('ip')->count();
        $result['code'] = 200;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $pv;
        $result['data']['sum'][] = $sum;
        HttpResponseUtil::setJsonResponse($result);
    }

    //今天按照日分析pv和ip
    public function actionTodayDay()
    {

    }



















    public function actionTest()
    {
        var_dump(json_decode('{"status":0,"msg":"","data":{"timeSpan":["2016\/12\/19","2016\/12\/18"],"fields":["simple_date_title","time","pv_count"],"items":[[[0],[1],[2],[3],[4],[5],[6],[7],[8],[9],[10],[11],[12],[13],[14],[15],[16],[17],[18],[19],[20],[21],[22],[23]],[["2016\/12\/19",23],["2016\/12\/19",14],["2016\/12\/19",4],["2016\/12\/19",4],["2016\/12\/19",1],["2016\/12\/19",6],["2016\/12\/19",4],["2016\/12\/19",15],["2016\/12\/19",93],["2016\/12\/19",162],["2016\/12\/19",188],["2016\/12\/19",15],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"],["2016\/12\/19","--"]],[["2016\/12\/18",31],["2016\/12\/18",10],["2016\/12\/18",6],["2016\/12\/18",10],["2016\/12\/18",10],["2016\/12\/18",1],["2016\/12\/18",6],["2016\/12\/18",11],["2016\/12\/18",52],["2016\/12\/18",66],["2016\/12\/18",87],["2016\/12\/18",77],["2016\/12\/18",44],["2016\/12\/18",44],["2016\/12\/18",84],["2016\/12\/18",100],["2016\/12\/18",66],["2016\/12\/18",51],["2016\/12\/18",26],["2016\/12\/18",35],["2016\/12\/18",59],["2016\/12\/18",60],["2016\/12\/18",31],["2016\/12\/18",32]],[]]}}'));
    }
}
