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
use statistics\models\Page;
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

    //按照小时分析 得到一天的信息，显示在前台展示页面
    public function actionExceptionHoursShow()
    {
        /*
        参数说明：
            appkey  id
            type = warning 警告
            type = error 错误
            day = today 今天
            day = yesterday 昨天
        */
        //接收到传递过来的参数
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $da = $request->post('day');
        $ty = $request->post('type');
        //echo $appkey.'--'.$type.'--'.$day.'<br/>';    //查看接收到的参数
        if($da == 'today') {
            $day = 0;
        }else if($da == 'yesterday'){
            $day = 1;
        }
        if($ty == 'error') {
            $type = -1 ;
        }else if($ty == 'warning'){
            $type = 0;
        }

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

        }else if($day == 0){
            //表示今天
            $startTime = $today['0'] - $totalSeconds; //得到今天 0 时的时间戳
            $endTime =  $today['0'] - $totalSeconds + $d;   //得到今天 24：00点的 时间戳
        }else{
            $result['code'] = 201;
            $result['data']['item'][] = '不正确的type值';
            HttpResponseUtil::setJsonResponse($result);
            return;
        }
        $date = date('Y-m-d', $startTime+1);   //得到当天的 年月日 ，以便后面使用
        //echo '当天的时间'.$date.'当天开始时间：'.date('Y-m-d H:i:s',$startTime).'当天时间：'.date('Y-m-d H:i:s',$endTime).'<br/>';

        for ($i = 0; $i < 24; $i++)
        {
            $hours[] = $i;
            //按每天24个小时的区间，格式化得到24个时间区间
            $startTime2 = date('Y-m-d H:i:s',$startTime + $i * $h);
            $endTime2 = date('Y-m-d H:i:s',$startTime + ($i+1) * $h);
            /*$logger->error($startTime2);    //打印出第二种格式看下是否出错
            $logger->error($endTime2);*/
            //查找当天的所需查找的信息
            $exceptions[$i] = (int)Scount::find()->Where([ 'appkey'=>$appkey ,'type' => $type ])->andWhere([ '>=', 'time', $startTime2 ])->andWhere([ '<', 'time', $endTime2 ])->count();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $date;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $exceptions;
        HttpResponseUtil::setJsonResponse($result);
    }

    //按照天数分析 得到 7天 或 30天 的信息，显示在前台展示页面
    public function actionExceptionDaysShow(){
        /*
           day = 6 ( 0为今天，一周七天，0 - 6 依次 )
           day = 29( 0为今天， 一月30天 0 - 29 依次)
         */
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $da = $request->post('day');
        $ty = $request->post('type');
        //echo 'appkey为：'.$appkey.'--day为：'.$day.'--type为：'.$type.'<br/>';
        $logger = MMLogger::getLogger(__FUNCTION__);    //为了打印，实例化一个对象
        //$logger->error(变量名);  //用法：调用实例化的对象，调用error方法，在 /runtime/logs/  目录下查看打印的结果

        if($da == 'week') {
            $day = 6;
        }else if($da == 'month'){
            $day = 29;
        }
        if($ty == 'error') {
            $type = -1 ;
        }else if($ty == 'warning'){
            $type = 0;
        }

        //定义 1天 ，1小时，1分钟 的总秒数。【为了优化】
        $d = 86400;
        $h = 3600;
        $m = 60;
        $today = getdate(); //得到一个数组
        $totalSeconds = ($today['hours'])*$h + ($today['minutes'])*$m + ($today['seconds']); //得出当天的总秒数
        //表示今天 $today_startTime
        $to_startTime = $today['0'] - $totalSeconds; //得到今天 0 时的时间戳
        $to_endTime =  $today['0'] - $totalSeconds + $d;   //得到今天 24：00点的 时间戳
        $to_date = date('Y-m-d', $to_startTime+1);   //得到当天的 年月日 ，以便后面使用
        //echo '当天的时间'.$to_date.'--当天开始的时间：'.date('Y-m-d H:i:s',$to_startTime).'--当天结束的时间：'.date('Y-m-d H:i:s',$to_endTime).'<br/>';
        //表示6天前：$seven_before_startTime
        $sb_startTime = $to_startTime - $d * 6;
        $sb_endTime = $to_startTime - $d * 5;
        $sb_date = date('Y-m-d',$sb_startTime + 1);
        //echo '七天前的时间'.$sb_date.'--七天前的开始时间：'.date('Y-m-d H:i:s',$sb_startTime).'--七天前的结束时间：'.date('Y-m-d H:i:s',$sb_endTime).'<br/>';
        //表示29天前：$thirty_before_startTime
        $tb_startTime = $to_startTime - $d * 29;
        $tb_endTime = $to_startTime - $d * 28;
        $tb_date = date('Y-m-d',$tb_startTime + 1);
        //echo '三十天前的时间'.$tb_date.'--三十天前的开始时间：'.date('Y-m-d H:i:s',$tb_startTime).'--三十天前的结束时间：'.date('Y-m-d H:i:s',$tb_endTime).'<br/>';
        if($day == 6){
            for($i = 0; $i < 7; $i++){
                $day_number[] = $i; //得到天数
                //那天的时间 $that_day_startTime
                $td_startTime = date('Y-m-d H:i:s',$sb_startTime + $i * $d);
                $td_endTime = date('Y-m-d H:i:s',$sb_endTime + ( $i ) * $d);
                //echo '那天的开始时间'.$td_startTime.'那天的结束时间'.$td_endTime.'<br/>';
                $day_date[] = date('Y-m-d',$sb_startTime + $i * $d);
                //var_dump($day_date);
                $exceptions[$i] = (int)Scount::find()->Where([ 'appkey'=>$appkey ,'type' => $type ])->andWhere([ '>=', 'time', $td_startTime ])->andWhere([ '<', 'time', $td_endTime ])->count();
            }
        }else if($day == 29){
            for($i = 0; $i < 30; $i++){
                $day_number[] = $i; //得到天数
                //那天的时间 $that_day_startTime
                $td_startTime = date('Y-m-d H:i:s',$tb_startTime + $i * $d);
                $td_endTime = date('Y-m-d H:i:s',$tb_endTime + ( $i ) * $d);
                //echo '那天的开始时间'.$td_startTime.'那天的结束时间'.$td_endTime.'<br/>';
                $day_date[] = date('Y-m-d',$tb_startTime + $i * $d);
                //var_dump($day_date);
                $exceptions[$i] = (int)Scount::find()->Where([ 'appkey'=>$appkey ,'type' => $type ])->andWhere([ '>=', 'time', $td_startTime ])->andWhere([ '<', 'time', $td_endTime ])->count();
            }
        }else{
            $result['code'] = 201;
            $result['data']['item'][] = '不正确的type值';
            HttpResponseUtil::setJsonResponse($result);
            return;
        }
        $result['code'] = 200;
        $result['data']['item'][] = $day_number;
        $result['data']['item'][] = $day_date;
        $result['data']['item'][] = $exceptions;
        HttpResponseUtil::setJsonResponse($result);
    }



    //按照小时分析 得到一天的信息，显示在详细页面
    public function actionExceptionHoursCompare()
    {

        //参数说明：
        //    appkey  id
        //    type = 0 警告
        //    type = -1 错误
        //    day = 0 今天
        //    day = 1 昨天
        $logger = MMLogger::getLogger(__FUNCTION__);    //为了打印，实例化一个对象
        //$logger->error(变量名);  //用法：调用实例化的对象，调用error方法，在 /runtime/logs/  目录下查看打印的结果

        //接收到传递过来的参数
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $da = $request->post('day');
        //echo $appkey.'--'.$day.'<br/>';    //查看接收到的参数
        //$logger->error($appkey.'--'.$day);

        if($da == 'today') {
            $day = 0;
        }else if($da == 'yesterday'){
            $day = 1;
        }

        //定义 1天 ，1小时，1分钟 的总秒数。【为了优化】
        $d = 86400;
        $h = 3600;
        $m = 60;
        $today = getdate(); //得到一个数组
        $totalSeconds = ($today['hours'])*$h + ($today['minutes'])*$m + ($today['seconds']); //得出当天的总秒数
        $logger->error($day);
        //day = 0为今天，1为昨天，得到所请求的 当天开始时间
        if($day == 1){
            //表示昨天
            $startTime = $today['0'] - $totalSeconds - $d; //得到昨天 0 时的时间戳
            $endTime = $today['0']-$totalSeconds;

        }else if($day == 0){
            //表示今天
            $startTime = $today['0'] - $totalSeconds; //得到今天 0 时的时间戳
            $endTime =  $today['0'] - $totalSeconds + $d;   //得到今天 24：00点的 时间戳
        }else{
            $result['code'] = 201;
            $result['data']['item'][] = '不正确的type值';
            HttpResponseUtil::setJsonResponse($result);
            return;
    }
        $date = date('Y-m-d', $startTime+1);   //得到当天的 年月日 ，以便后面使用
        //echo '当天的时间'.$date.'当天开始时间：'.date('Y-m-d H:i:s',$startTime).'当天的结束时间：'.date('Y-m-d H:i:s',$endTime).'<br/>';
        //$logger->error('当天的时间：'.$date.'当天的开始时间：'.date('Y-m-d H:i:s',$startTime).'当天的结束时间：'.date('Y-m-d H:i:s',$endTime));
        for ($i = 0; $i < 24; $i++)
        {
            //得到所有的小时数
            $hours[] = $i;
            //按每天24个小时的区间，格式化得到24个时间区间
            $startTime2 = date('Y-m-d H:i:s',$startTime + $i * $h);
            $endTime2 = date('Y-m-d H:i:s',$startTime + ($i+1) * $h);
            //得到 00：00-00:59 的时间区间
            $time_interval = date('H:i',$startTime + $i * $h).'-'.date('H:i',($startTime + ($i+1) * $h)-1);
            //$logger->error($startTime2);    //打印出第二种格式看下是否出错
            //$logger->error($endTime2);
            //$logger->error($time_interval);
            //得到时间区间
            $interval[] = $time_interval;
            //查找当天的异常
            $err_exceptions[$i] = (int)Scount::find()->Where([ 'appkey'=>$appkey ,'type' => 0 ])->andWhere([ '>=', 'time', $startTime2 ])->andWhere([ '<', 'time', $endTime2 ])->count();
            //查找当天的错误
            $war_exceptions[$i] = (int)Scount::find()->Where([ 'appkey'=>$appkey ,'type' => -1 ])->andWhere([ '>=', 'time', $startTime2 ])->andWhere([ '<', 'time', $endTime2 ])->count();
        }
            $startTime3 = date('Y-m-d H:i:s',$startTime);
            $endTime3 = date('Y-m-d H:i:s',$endTime);
            $errInfos = Scount::find()->where(['appkey' => $appkey,'type'=> 0])->andWhere(['>=','time',$startTime3])->andWhere(['<','time',$endTime3])->all();
            $warnInfos = Scount::find()->where(['appkey' => $appkey,'type'=> -1])->andWhere(['>=','time',$startTime3])->andWhere(['<','time',$endTime3])->all();

        /*$logger->error($startTime3);
        $logger->error($endTime3);
        $logger->error($errInfos);
        $logger->error($warnInfos);
        $len1 = count($errInfos);
        $len2 = count($warnInfos);
        $logger->error($len1);
        $logger->error($len2);
        if(is_array($errInfos) && is_array($warnInfos)){
            $logger->error( '都是数组');
        }else{
            $logger->error( '不全是数组');
        }*/
        if(empty($errInfos) && empty($warnInfos)){
            $errInfo = array();
            $warnInfo = array();
        }else if(!empty($errInfos) && empty($warnInfos)){
            foreach($errInfos as $k=>$err)
            {
                $url = Page::findOne($err['page']);
                $errInfo[$k][] = $url['page_url'];
                $errInfo[$k][] = $err['time'];
                $errInfo[$k][] = $err['message'];
                Yii::error($k);
            }
            $warnInfo = array();
        }else if(empty($errInfos) && !empty($warnInfos)){
            $errInfo = array();
            foreach($warnInfos as $j=>$warn)
            {
                $url = Page::findOne($warn['page']);
                $warnInfo[$j][] = $url['page_url'];
                $warnInfo[$j][] = $warn['time'];
                $warnInfo[$j][] = $warn['message'];
            }
        }else if(!empty($errInfos) && !empty($warnInfos)) {
            foreach($errInfos as $k=>$err)
            {
                $url = Page::findOne($err['page']);
                $errInfo[$k][] = $url['page_url'];
                $errInfo[$k][] = $err['time'];
                $errInfo[$k][] = $err['message'];
                Yii::error($k);
            }
            foreach($warnInfos as $j=>$warn)
            {
                $url = Page::findOne($warn['page']);
                $warnInfo[$j][] = $url['page_url'];
                $warnInfo[$j][] = $warn['time'];
                $warnInfo[$j][] = $warn['message'];
            }
        }
        $result['code'] = 200;
        $result['data']['item'][] = $date;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $interval;
        $result['data']['item'][] = $err_exceptions;
        $result['data']['item'][] = $war_exceptions;
        $result['data']['item'][] = $errInfo;
        $result['data']['item'][] = $warnInfo;
        HttpResponseUtil::setJsonResponse($result);
    }


    //按照天数分析 得到所传递day的天数的信息，显示在详细页面
    public function actionExceptionDaysCompare(){
         //day = 6 ( 0为今天，一周七天，0 - 6 依次 )
         //day = 29( 0为今天， 一月30天 0 - 29 依次)

        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $da = $request->post('day');
        //echo 'appkey为：'.$appkey.'--day为：'.$day.'<br/>';
        $logger = MMLogger::getLogger(__FUNCTION__);    //为了打印，实例化一个对象
        //$logger->error(变量名);  //用法：调用实例化的对象，调用error方法，在 /runtime/logs/  目录下查看打印的结果

        if($da == 'week') {
            $day = 6;
        }else if($da == 'month'){
            $day = 29     ;
        }


        //定义 1天 ，1小时，1分钟 的总秒数。【为了优化】
        $d = 86400;
        $h = 3600;
        $m = 60;
        $today = getdate(); //得到一个数组
        $totalSeconds = ($today['hours'])*$h + ($today['minutes'])*$m + ($today['seconds']); //得出当天的总秒数
        //表示今天 $today_startTime
        $to_startTime = $today['0'] - $totalSeconds; //得到今天 0 时的时间戳
        $to_endTime =  $today['0'] - $totalSeconds + $d;   //得到今天 24：00点的 时间戳
        $to_date = date('Y-m-d', $to_startTime+1);   //得到当天的 年月日 ，以便后面使用
        //echo '当天的时间'.$to_date.'--当天开始的时间：'.date('Y-m-d H:i:s',$to_startTime).'--当天结束的时间：'.date('Y-m-d H:i:s',$to_endTime).'<br/>';
        //表示6天前：$seven_before_startTime
        $sb_startTime = $to_startTime - $d * 6;
        $sb_endTime = $to_startTime - $d * 5;
        $sb_date = date('Y-m-d',$sb_startTime + 1);
        //echo '七天前的时间'.$sb_date.'--七天前的开始时间：'.date('Y-m-d H:i:s',$sb_startTime).'--七天前的结束时间：'.date('Y-m-d H:i:s',$sb_endTime).'<br/>';
        //表示29天前：$thirty_before_startTime
        $tb_startTime = $to_startTime - $d * 29;
        $tb_endTime = $to_startTime - $d * 28;
        $tb_date = date('Y-m-d',$tb_startTime + 1);
        //echo '三十天前的时间'.$tb_date.'--三十天前的开始时间：'.date('Y-m-d H:i:s',$tb_startTime).'--三十天前的结束时间：'.date('Y-m-d H:i:s',$tb_endTime).'<br/>';

        if($day == 6) {
            $day_name = $sb_date.'-'.$to_date;
            //echo $day_name;
            for($i = 0; $i < 7; $i++){
                    //那天的时间 $that_day_startTime
                    $td_startTime = date('Y-m-d H:i:s', $sb_startTime + $i * $d);
                    $td_endTime = date('Y-m-d H:i:s', $sb_endTime + ($i) * $d);
                    //echo '那天的开始时间'.$td_startTime.'那天的结束时间'.$td_endTime.'<br/>';
                $day_dates[] = date('Y-m-d', $sb_startTime + $i * $d);  //得到时间数组
                    $thatDay_err_data[] = (int)Scount::find()->Where(['appkey' => $appkey, 'type' => '-1'])->andWhere(['>=', 'time', $td_startTime])->andWhere(['<', 'time', $td_endTime])->count();
                    $thatDay_war_data[] = (int)Scount::find()->Where(['appkey' => $appkey, 'type' => '0'])->andWhere(['>=', 'time', $td_startTime])->andWhere(['<', 'time', $td_endTime])->count();

            }
            $sb_startTime2 = date('Y-m-d H:i:s',$sb_startTime);
            $to_endTime2 = date('Y-m-d H:i:s',$to_endTime);
            //echo $sb_startTime2.'---'.$to_endTime2;
            $errInfos = Scount::find()->where(['appkey' => $appkey,'type'=> 0])->andWhere(['>=','time',$sb_startTime2])->andWhere(['<','time',$to_endTime2])->all();
            //var_dump($errInfos);
            $warnInfos = Scount::find()->where(['appkey' => $appkey,'type'=> -1])->andWhere(['>=','time',$sb_startTime2])->andWhere(['<','time',$to_endTime2])->all();
            if(empty($errInfos) && empty($warnInfos)) {
                $errInfo = array();
                $warnInfo = array();
            }else if(!empty($errInfos) && empty($warnInfos)){
                foreach($errInfos as $k=>$err) {
                    $url = Page::findOne($err['page']);
                    $errInfo[$k][] = $url['page_url'];
                    $errInfo[$k][] = $err['time'];
                    $errInfo[$k][] = $err['message'];
                    Yii::error($k);
                }
                $warnInfo = array();
            }else if(empty($errInfos) && !empty($warnInfos)){
                $errInfo = array();
                foreach($warnInfos as $j=>$warn) {
                    $url = Page::findOne($warn['page']);
                    $warnInfo[$j][] = $url['page_url'];
                    $warnInfo[$j][] = $warn['time'];
                    $warnInfo[$j][] = $warn['message'];
                }
            }else if(!empty($errInfos) && !empty($warnInfos)) {
                foreach($errInfos as $k=>$err) {
                    $url = Page::findOne($err['page']);
                    $errInfo[$k][] = $url['page_url'];
                    $errInfo[$k][] = $err['time'];
                    $errInfo[$k][] = $err['message'];
                    Yii::error($k);
                }
                foreach($warnInfos as $j=>$warn) {
                    $url = Page::findOne($warn['page']);
                    $warnInfo[$j][] = $url['page_url'];
                    $warnInfo[$j][] = $warn['time'];
                    $warnInfo[$j][] = $warn['message'];
                }
            }
        }else if($day == 29){
            $day_name = $tb_date.'-'.$to_date;
            //echo $day_name;
            for($i = 0; $i < 30; $i++){
                $day_number[] = $i; //得到天数
                //那天的时间 $that_day_startTime
                $td_startTime = date('Y-m-d H:i:s',$tb_startTime + $i * $d);
                $td_endTime = date('Y-m-d H:i:s',$tb_endTime + ( $i ) * $d);
                //echo '那天的开始时间'.$td_startTime.'那天的结束时间'.$td_endTime.'<br/>';
                $day_dates[] = date('Y-m-d',$tb_startTime + $i * $d);
                //var_dump($day_date);
                $thatDay_data[$i][] = date('Y-m-d',$sb_startTime + $i * $d);
                $thatDay_err_data[] = (int)Scount::find()->Where([ 'appkey'=>$appkey ,'type' =>'-1' ])->andWhere([ '>=', 'time', $td_startTime ])->andWhere([ '<', 'time', $td_endTime ])->count();
                $thatDay_war_data[] = (int)Scount::find()->Where([ 'appkey'=>$appkey ,'type' =>'0' ])->andWhere([ '>=', 'time', $td_startTime ])->andWhere([ '<', 'time', $td_endTime ])->count();
            }
            $td_startTime2 = date('Y-m-d H:i:s',$tb_startTime);
            $to_endTime2 = date('Y-m-d H:i:s',$to_endTime);
            //echo $tb_startTime2.'---'.$to_endTime2;
            $errInfos = Scount::find()->where(['appkey' => $appkey,'type'=> 0])->andWhere(['>=','time',$td_startTime2])->andWhere(['<','time',$to_endTime2])->all();
            //var_dump($errInfos);
            $warnInfos = Scount::find()->where(['appkey' => $appkey,'type'=> -1])->andWhere(['>=','time',$td_startTime2])->andWhere(['<','time',$to_endTime2])->all();
            if(empty($errInfos) && empty($warnInfos)){
                $errInfo = array();
                $warnInfo = array();
            }else if(!empty($errInfos) && empty($warnInfos)){
                foreach($errInfos as $k=>$err)
                {
                    $url = Page::findOne($err['page']);
                    $errInfo[$k][] = $url['page_url'];
                    $errInfo[$k][] = $err['time'];
                    $errInfo[$k][] = $err['message'];
                    Yii::error($k);
                }
                $warnInfo = array();
            }else if(empty($errInfos) && !empty($warnInfos)){
                $errInfo = array();
                foreach($warnInfos as $j=>$warn)
                {
                    $url = Page::findOne($warn['page']);
                    $warnInfo[$j][] = $url['page_url'];
                    $warnInfo[$j][] = $warn['time'];
                    $warnInfo[$j][] = $warn['message'];
                }
            }else if(!empty($errInfos) && !empty($warnInfos)) {
                foreach($errInfos as $k=>$err)
                {
                    $url = Page::findOne($err['page']);
                    $errInfo[$k][] = $url['page_url'];
                    $errInfo[$k][] = $err['time'];
                    $errInfo[$k][] = $err['message'];
                    Yii::error($k);
                }
                foreach($warnInfos as $j=>$warn)
                {
                    $url = Page::findOne($warn['page']);
                    $warnInfo[$j][] = $url['page_url'];
                    $warnInfo[$j][] = $warn['time'];
                    $warnInfo[$j][] = $warn['message'];
                }
            }
        }else{
                $result['code'] = 201;
                $result['data']['item'][] = '不正确的type值';
                HttpResponseUtil::setJsonResponse($result);
                return;
        }
        $result['code'] = 200;
        $result['data']['item'][] = $day_name;
        $result['data']['item'][] = $day_dates;
        $result['data']['item'][] = $thatDay_err_data;
        $result['data']['item'][] = $thatDay_war_data;
        $result['data']['item'][] = $errInfo;
        $result['data']['item'][] = $warnInfo;
        HttpResponseUtil::setJsonResponse($result);
    }

    //异常按小时统计
    public function actionExceptionHoursStatistics( ) {
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $da = $request->post('day');
        $ty = $request->post('type');
        $logger = MMLogger::getLogger(__FUNCTION__);
        if($da == 'today'){
            $day = 0;
        }else if($da == 'yesterday'){
            $day = 1;
        }
        if($ty == 'error'){
            $type = -1;
        }else if($ty == 'warning'){
            $type = 0;
        }


        $day_date = date('Y-m-d',time() - 86400 * $day);
        //echo '当天的时间'.$day_date;
        if(($day == 1 || $day == 0) && ($type == -1 || $type == 0)){
            //表示昨天
            $startTime = $day_date;
            $endTime = date('Y-m-d',strtotime($startTime) + 86400);
        }else{
            $result['code'] = 201;
            $result['data']['item'][] = '不正确的type值';
            HttpResponseUtil::setJsonResponse($result);
            return;
        }
        for($i = 0; $i < 24; $i++) {
            if ($i < 10) {
                $hours[] = '0' . $i . ':00 - 0' . $i . ':59';
            } else {
                $hours[] = $i . ':00 - ' . $i . ':59';
            }
            //各个小时的错误量
            $resExc[$i] = Scount::find()->where(['appkey' => $appkey, 'type' => $type, 'hour' => $i])->andWhere(['>=', 'time', $startTime])->andWhere(['<', 'time', $endTime])->count();
            //$logger->error($resExc);
        }
        //$logger->error($resExc);
        $resExcInfos = Scount::find()->where(['appkey' => $appkey, 'type' => $type])->andWhere(['>=', 'time', $startTime])->andWhere(['<', 'time', $endTime])->all();
        if(empty($resExcInfos)) {
            $resExcInfo = array();
        }else{
            foreach($resExcInfos as $k=>$v)
            {
                $url = Page::findOne($v['page']);
                $resExcInfo[$k][] = $url['page_url'];
                $resExcInfo[$k][] = $v['time'];
                $resExcInfo[$k][] = $v['message'];
            }
        }
        $result['code'] = 200;
        $result['data']['item'][] = $day_date;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $resExc;
        $result['data']['item'][] = $resExcInfo;
        HttpResponseUtil::setJsonResponse($result);
    }

    //异常按天统计
    public function actionExceptionDaysStatistics() {
        $logger = MMLogger::getLogger(__FUNCTION__);
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $da = $request->post('day');
        $ty = $request->post('type');
        $today =  date('Y-m-d', time());
        //echo $day_date;
        if($da == 'week'){
            $day = 6;
        }else if($da == 'month') {
            $day = 29;
        }

        if($ty == 'error') {
            $type = -1;
        }else if($ty == 'warning') {
            $type = 0;
        }

        if(($day == 6 || $day == 29) && ($type == -1 || $type == 0)) {

        }else{
            $result['code'] = 201;
            $result['data']['item'][] = '不正确的type值';
            HttpResponseUtil::setJsonResponse($result);
            return;
        }
        if($day == 6) {
            for($i = 7; $i > 0; $i--) {
                $td_startTime = date('Y-m-d H:i:s',(strtotime($today) - ($i-1) * 86400));
                $td_endTime = date('Y-m-d H:i:s',(strtotime($today) - ($i-2) * 86400));
                //$logger->error($td_startTime.'--'.$td_endTime);
                $day_dates[] = date('Y-m-d',strtotime($td_startTime));
                //$logger->error($day_dates);
                $resExc[7-$i] = Scount::find()->where(['appkey'=>$appkey, 'type'=>$type])->andWhere(['>=', 'time', $td_startTime])->andWhere(['<', 'time', $td_endTime])->count();
            }
            $sb_startTime = date('Y-m-d H:i:s',(strtotime($today) - 6 * 86400));
            $to_endTime = date('Y-m-d H:i:s',(strtotime($today)+ 86400));
            //$logger->error($sb_startTime.'--'.$to_endTime);
            $resExcInfos = Scount::find()->where(['appkey'=>$appkey, 'type'=>$type])->andWhere(['>=', 'time', $sb_startTime])->andWhere(['<', 'time', $to_endTime])->all();
            if(empty($resExc)) {
                $resExcInfo = array();
            }else{
                foreach($resExcInfos as $k=>$v)
                {
                    $url = Page::findOne($v['page']);
                    $resExcInfo[$k][] = $url['page_url'];
                    $resExcInfo[$k][] = $v['time'];
                    $resExcInfo[$k][] = $v['message'];
                }
            }
            $result['code'] = 200;
            $result['data']['item'][] = $day_dates;
            $result['data']['item'][] = $resExc;
            $result['data']['item'][] = $resExcInfo;
            HttpResponseUtil::setJsonResponse($result);
        }else{
            for($i = 30; $i > 0; $i--) {
                $td_startTime = date('Y-m-d H:i:s',(strtotime($today) - ($i-1) * 86400));
                $td_endTime = date('Y-m-d H:i:s',(strtotime($today) - ($i-2) * 86400));
                //$logger->error($td_startTime.'--'.$td_endTime);
                $day_dates[] = date('Y-m-d',strtotime($td_startTime));
                //$logger->error($day_dates);
                $resExc[30-$i] = Scount::find()->where(['appkey'=>$appkey, 'type'=>$type])->andWhere(['>=', 'time', $td_startTime])->andWhere(['<', 'time', $td_endTime])->count();
            }
            $th_startTime = date('Y-m-d H:i:s',(strtotime($today) - 29 * 86400));
            $to_endTime = date('Y-m-d H:i:s',(strtotime($today)+ 86400));
            $logger->error($th_startTime.'--'.$to_endTime);
            $resExcInfos = Scount::find()->where(['appkey'=>$appkey, 'type'=>$type])->andWhere(['>=', 'time', $th_startTime])->andWhere(['<', 'time', $to_endTime])->all();
            if(empty($resExc)) {
                $resExcInfo = array();
            }else{
                foreach($resExcInfos as $k=>$v)
                {
                    $url = Page::findOne($v['page']);
                    $resExcInfo[$k][] = $url['page_url'];
                    $resExcInfo[$k][] = $v['time'];
                    $resExcInfo[$k][] = $v['message'];
                }
            }
            $result['code'] = 200;
            $result['data']['item'][] = $day_dates;
            $result['data']['item'][] = $resExc;
            $result['data']['item'][] = $resExcInfo;
            HttpResponseUtil::setJsonResponse($result);
        }
    }








    //昨天或今天按照小时分析
    public function actionExceptionHour()
    {
        $appkey = Yii::$app->request->post('appkey');
        //需要分析的天数
        $type = Yii::$app->request->post('type');
        if($type == 'today')
        {
            $date = 0;
        }
        else if($type == 'yesterday')
        {
            $date = 1;
        }
        else
        {
            $result['code'] = 201;
            $result['data']['item'][] = '不正确的type值';
            HttpResponseUtil::setJsonResponse($result);
            return;
        }
        $day = date('Y-m-d',time()-86400*$date);
        $startDay = $day;
        $endDay = date('Y-m-d',strtotime($startDay)+86400);
        //echo '开始时间'.$startDay.'--结束时间'.$endDay;
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
            //各个小时的错误量
            $resErr[] = Scount::find()->where(['appkey' => $appkey,'type'=> 0,'hour'=>$i])->andWhere(['>=','time',$startDay])->andWhere(['<','time',$endDay])->count();
            //各个小时的警告量
            $resWar[] = Scount::find()->where(['appkey' => $appkey,'type'=> -1,'hour'=>$i])->andWhere(['>=','time',$startDay])->andWhere(['<','time',$endDay])->count();
        }
        $errInfos = Scount::find()->where(['appkey' => $appkey,'type'=> 0])->andWhere(['>=','time',$startDay])->andWhere(['<','time',$endDay])->all();
        $warnInfos = Scount::find()->where(['appkey' => $appkey,'type'=> -1])->andWhere(['>=','time',$startDay])->andWhere(['<','time',$endDay])->all();

        foreach($errInfos as $k=>$err)
        {
            $url = Page::findOne($err['page']);
            $errInfo[$k][] = $url->page_url;
            $errInfo[$k][] = $err->time;
            $errInfo[$k][] = $err->message;
            Yii::error($k);
        }
        foreach($warnInfos as $j=>$warn)
        {
            $url = Page::findOne($warn['page']);
            $warnInfo[$j][] = $url['page_url'];
            $warnInfo[$j][] = $warn->time;
            $warnInfo[$j][] = $warn->message;
        }
        $result['code'] = 200;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $resErr;
        $result['data']['item'][] = $resWar;
        $result['data']['item'][] = $errInfo;
        $result['data']['item'][] = $warnInfo;
        HttpResponseUtil::setJsonResponse($result);
    }

    //按照天数分析最近7或者30天的异常和错误
    public function actionExceptionDays()
    {
        $appkey = Yii::$app->request->post('appkey');
        //需要分析的天数
        $type = Yii::$app->request->post('type');
        if($type == 'week')
        {
            $date = 7;
        }
        else if($type == 'month')
        {
            $date = 30;
        }
        else
        {
            $result['code'] = 201;
            $result['data']['item'][] = '不正确的type值';
        }
        for($i = 1; $i <= $date; $i++)
        {
            $days[] = date('Y-m-d', time() - 86400 * ($date-$i));
            //当前时间
            $startTime = date('Y-m-d', time() - 86400 * ($date-$i));
            $endTime = date('Y-m-d', strtotime($startTime) + 86400);
            //echo '当前时间的开始时间'.$startTime.'---当前时间的结束时间'.$endTime.'<br/>';
            //每天的错误量
            //错误数量和信息
            $resErr[] = Scount::find()->where(['appkey' => $appkey,'type'=> 0])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
            //print_r($resErr);
            //警告数量和信息
            $resWarn[] = Scount::find()->where(['appkey' => $appkey,'type'=> -1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
        }
//        var_dump($days);
//        var_dump($resErr);
//        var_dump($resWarn);
        $startDay = date('Y-m-d', time() - 86400 * ($date-1));
        $endDay = date('Y-m-d H:i:s', time());
        //echo 'sadadas当前时间的开始时间'.$startDay.'---当前时间的结束时间'.$endDay.'<br/>';
        $errInfos = Scount::find()->where(['appkey' => $appkey,'type'=> 0])->andWhere(['>=','time',$startDay])->andWhere(['<','time',$endDay])->all();
        //var_dump($errInfos);
        $warnInfos = Scount::find()->where(['appkey' => $appkey,'type'=> -1])->andWhere(['>=','time',$startDay])->andWhere(['<','time',$endDay])->all();
        foreach($errInfos as $k=>$err)
        {
            $url = Page::findOne($err['page']);
            $errInfo[$k][] = $url->page_url;
            $errInfo[$k][] = $err->time;
            $errInfo[$k][] = $err->message;
            Yii::error($k);
        }
        foreach($warnInfos as $j=>$warn)
        {
            $url = Page::findOne($warn['page']);
            $warnInfo[$j][] = $url['page_url'];
            $warnInfo[$j][] = $warn->time;
            $warnInfo[$j][] = $warn->message;
        }
        $result['code'] = 200;
        $result['data']['item'][] = $days;
        $result['data']['item'][] = $resErr;
        $result['data']['item'][] = $resWarn;
        $result['data']['item'][] = $errInfo;
        $result['data']['item'][] = $warnInfo;
        HttpResponseUtil::setJsonResponse($result);
    }

    public function actionTest()
    {
        //得出当天的总秒数
//        echo time()%86400+8*3600;
        echo strtotime(date('Y-m-d',time()));
    }

}