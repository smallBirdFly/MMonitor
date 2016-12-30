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
    //按照小时分析 得到 两天的异常或错误
    public function actionExceptionHours()
    {
        /*
        参数说明：
            appkey  id
            type = 0 警告
            type = -1 错误
            day = 0 今天
            day = 1 昨天
        */
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

        //day = 0为今天，1为昨天，得到所请求的 当天开始时间
        if($day==1){
            //表示昨天
            $to_startTime = $today['0'] - $totalSeconds; //得到今天 0 时的时间戳
            $to_endTime = $to_startTime + $d;   //得到今天 24：00点的 时间戳
            $ye_startTime = $today['0'] - $totalSeconds - $d; //得到昨天 0 时的时间戳
            $ye_endTime = $to_startTime;

        }else{
            //表示今天
            $to_startTime = $today['0'] - $totalSeconds; //得到今天 0 时的时间戳
            $to_endTime = $to_startTime + $d;   //得到今天 24：00点的 时间戳
            $ye_startTime = $today['0'] - $totalSeconds - $d; //得到昨天 0 时的时间戳
            $ye_endTime = $to_startTime;
        }
        $to_date = date('Y-m-d', $to_startTime+1);   //得到今天的 年月日 ，以便后面使用
        $ye_date = date('Y-m-d', $ye_startTime+1);
        /*echo '今天开始时间：'.date('Y-m-d H:i:s',$to_startTime).'结束时间：'.date('Y-m-d H:i:s',$to_endTime).'<br/>';
        echo '昨天开始时间：'.date('Y-m-d H:i:s',$ye_startTime).'昨天结束时间：'.date('Y-m-d H:i:s',$ye_endTime).'<br/>';*/

        for ($i = 0; $i < 24; $i++)
        {
            $hours[] = $i;
            //按每天24个小时的区间，格式化得到24个时间区间
            $to_startTime2 = date('Y-m-d H:i:s',$to_startTime + $i * $h);
            $to_endTime2 = date('Y-m-d H:i:s',$to_startTime + ($i+1) * $h);
            $ye_startTime2 = date('Y-m-d H:i:s',$ye_startTime + $i * $h);
            $ye_endTime2 = date('Y-m-d H:i:s',$ye_startTime + ($i+1) * $h);
            /*$logger->error($to_startTime2);    //打印出第二种格式看下是否出错
            $logger->error($to_endTime2);
            $logger->error($ye_startTime2);
            $logger->error($ye_endTime2);*/
            //查找今天异常或者错误
            $to_exceptions[$i] = $i;
            $to_exceptions[$i] = (int)Scount::find()->Where([ 'appkey'=>$appkey ,'type' => $type ])->andWhere([ '>=', 'time', $to_startTime2 ])->andWhere([ '<', 'time', $to_endTime2 ])->count();
            //查找昨天的异常或者错误
            $ye_exceptions[$i] = $i;
            $ye_exceptions[$i] = (int)Scount::find()->Where([ 'appkey'=>$appkey ,'type' => $type ])->andWhere([ '>=', 'time', $ye_startTime2 ])->andWhere([ '<', 'time', $ye_endTime2 ])->count();
        }
        $result['code'] = 200;
        $result['data']['item']['today'][] = $to_date;
        $result['data']['item']['today'][] = $hours;
        $result['data']['item']['today'][] = $to_exceptions;
        $result['data']['item']['yesterday'][] = $ye_date;
        $result['data']['item']['yesterday'][] = $hours;
        $result['data']['item']['yesterday'][] = $ye_exceptions;
        HttpResponseUtil::setJsonResponse($result);
    }

    //按照小时分析 得到一天的信息
    public function actionExceptionHoursOneDay()
    {
        /*
        参数说明：
            appkey  id
            type = 0 警告
            type = -1 错误
            day = 0 今天
            day = 1 昨天
        */
        //接收到传递过来的参数
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
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

        //day = 0为今天，1为昨天，得到所请求的 当天开始时间
        if($day==1){
            //表示昨天
            $startTime = $today['0'] - $totalSeconds - $d; //得到昨天 0 时的时间戳
            $endTime = $today['0']-$totalSeconds;

        }else{
            //表示今天
            $startTime = $today['0'] - $totalSeconds; //得到今天 0 时的时间戳
            $endTime =  $today['0'] - $totalSeconds + $d;   //得到今天 24：00点的 时间戳
        }
        $date = date('Y-m-d', $startTime+1);   //得到当天的 年月日 ，以便后面使用
        //echo '当天的时间'.$date.'当天开始时间：'.date('Y-m-d H:i:s',$startTime).'当天时间：'.date('Y-m-d H:i:s',$endTime).'<br/>';

        for ($i = 0; $i < 24; $i++)
        {
            $hours[] = $i;
            //按每天24个小时的区间，格式化得到24个时间区间
            $startTime2 = date('Y-m-d H:i:s',$startTime + $i * $h);
            $endTime2 = date('Y-m-d H:i:s',$startTime + ($i+1) * $h);
            $time_interval = date('H:i:s',$startTime + $i * $h).'-'.date('H:i:s',$startTime + ($i+1) * $h);
            /*$logger->error($startTime2);    //打印出第二种格式看下是否出错
            $logger->error($endTime2);
            $logger->error($time_interval);*/
            //得到时间区间
            $interval[] = $time_interval;
            //查找当天的异常
            $err_exceptions[$i] = $i;
            $err_exceptions[$i] = (int)Scount::find()->Where([ 'appkey'=>$appkey ,'type' => '0' ])->andWhere([ '>=', 'time', $startTime2 ])->andWhere([ '<', 'time', $endTime2 ])->count();
            //查找当天的错误
            $war_exceptions[$i] = (int)Scount::find()->Where([ 'appkey'=>$appkey ,'type' => '-1' ])->andWhere([ '>=', 'time', $startTime2 ])->andWhere([ '<', 'time', $endTime2 ])->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $date;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $interval;
        $result['data']['item'][] = $war_exceptions;
        $result['data']['item'][] = $err_exceptions;
        HttpResponseUtil::setJsonResponse($result);
    }
}