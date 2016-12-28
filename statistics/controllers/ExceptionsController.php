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
use common\components\MMLogger;
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
        //接收到传递过来的参数
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $type = $request->post('type');
        $day = $request->post('day');
        //echo $appkey.'--'.$type.'--'.$day.'<br/>';    //查看接收到的参数

        $logger = MMLogger::getLogger(__FUNCTION__);    //为了打印，实例化一个对象
        //$logger->error(变量名);  //用法：调用实例化的对象，调用error方法，在 /runtime/logs/  目录下查看打印的结果

        //定义 1天 ，1小时，1分钟 的总秒数。【为了优化】
        $d = 86400;
        $h = 3600;
        $m = 60;
        $today = getdate(); //得到一个数组
        $totalSeconds = ($today['hours'])*$h + ($today['minutes'])*$m + ($today['seconds']); //得出当天的总秒数

        //0为今天，1为昨天，得到所请求的 当天开始时间
        if($type==1){
            //表示昨天
            $startTime1 = $today['0'] - $totalSeconds - $d; //得到昨天 0 时的时间戳
        }else{
            //表示今天
            $startTime1 = $today['0'] - $totalSeconds; //得到今天 0 时的时间戳
        }
        $endTime1 = $startTime1 + $d;   //得到昨天 24 时的时间戳
        $date = date('Y-m-d',$startTime1);   //得到今天的 年月日 ，以便后面使用
        //echo '今天-开始时间：'.date('Y-m-d H:i:s',$startTime1).'结束时间：'.date('Y-m-d H:i:s',$endTime1);

        //以年月日的格式显示 开始时间 和 结束时间，为了便于调试，则另起变量名
        $startTime2 = date('Y-m-d H:i:s',$startTime1);
        $endTime2 = date('Y-m-d H:i:s',$endTime1);
        /*$logger->error($startTime2);    //打印出第二种格式看下是否出错
        $logger->error($endTime2);*/

        for ($i = 0; $i < 24; $i++)
        {
            $hours[] = $i;
            //按每天24个小时的区间，格式化得到24个时间区间
            $startTime3 = date('Y-m-d H:i:s',$startTime1 + $i * $h);
            $endTime3 = date('Y-m-d H:i:s',$endTime1 + ($i+1) * $h);
            /*$logger->error($startTime3);    //打印出第二种格式看下是否出错
            $logger->error($endTime3);*/

            /*//当前时间
            $startTime = date('Y-m-d H:i:s', strtotime($day) + $i * 60 * 60);
            $endTime = date('Y-m-d H:i:s', strtotime($day) + $i * 60 * 60 + 3600);
            //今天各个小时的错误量
            $exceptions[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => $type])->count();
            $exceptions[$i][] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => $type])->count();*/

            /*//用mysql的原生方式查询数据库
            $connection = Yii::$app->db;
            $sql = "select * from scount where type = ".$type."and appkey = "."$appkey";
            $command = $connection->createCommand($sql);
            $res = $command->query($sql);*/

            $exceptions[$i][] = $i;
            $exceptions[$i][] = Scount::find()->where(['>=', 'time', strtotime($startTime3)])->andWhere(['<', 'time', strtotime($endTime3)])->andWhere(['appkey'=>$appkey ,'type' => $type])->count();
        }
        $result['code'] = 200;
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
            $startTime = date('Y-m-d',time()-86400*($date - $i - 1));
            $endTime = date('Y-m-d', time()-86400*($date - $i -2));
            $exceptions[$i] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => $type])->count();
            $exceptions[$i] = Scount::find()->where(['>=', 'time',$startTime])->andWhere(['<', 'time',$endTime])->andWhere(['appkey'=>$appkey])->andWhere(['type' => $type])->count();
        }
        Yii::error($days);
        $result['code'] = 200;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $exceptions;
        HttpResponseUtil::setJsonResponse($result);
    }
}