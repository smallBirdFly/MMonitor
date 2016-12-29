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

        //day = 0为今天，1为昨天，得到所请求的 当天开始时间
        if($day==1){
            //表示昨天
            $startTime1 = $today['0'] - $totalSeconds - $d; //得到昨天 0 时的时间戳
        }else{
            //表示今天
            $startTime1 = $today['0'] - $totalSeconds; //得到今天 0 时的时间戳
        }
        $endTime1 = $startTime1 + $d;   //得到昨天 24 时的时间戳
        $date = date('Y-m-d',$startTime1);   //得到今天的 年月日 ，以便后面使用
        //echo '今天-开始时间：'.date('Y-m-d H:i:s',$startTime1).'结束时间：'.date('Y-m-d H:i:s',$endTime1).'<br/>';

        for ($i = 0; $i < 24; $i++)
        {
            $hours[] = $i;
            //按每天24个小时的区间，格式化得到24个时间区间
            $startTime2 = date('Y-m-d H:i:s',$startTime1 + $i * $h);
            $endTime2 = date('Y-m-d H:i:s',$startTime1 + ($i+1) * $h);
            /*$logger->error($startTime2);    //打印出第二种格式看下是否出错
            $logger->error($endTime2);*/
            $exceptions[$i][] = $i;
            $exceptions[$i][] = Scount::find()->Where([ 'appkey'=>$appkey ,'type' => $type ])->andWhere([ '>=', 'time', $startTime2 ])->andWhere([ '<', 'time', $endTime2 ])->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $date;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $exceptions;
        HttpResponseUtil::setJsonResponse($result);
    }
}