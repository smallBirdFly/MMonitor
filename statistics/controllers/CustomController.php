<?php

namespace statistics\controllers;


use common\components\MMLogger;
use common\utils\HttpResponseUtil;
use statistics\component\AuthFilter;

use statistics\models\Count;
use statistics\models\Month;
use statistics\models\Page;
use statistics\models\Scount;
use statistics\models\Type;
use Yii;
use yii\db\ActiveRecord;
use yii\web\Controller;


class CustomController extends Controller
{
    public $enableCsrfValidation = true;
    public function behaviors()
    {
        return [
            'auth' => [
                'class' => AuthFilter::className(),
                'except' => ['login'],
            ]
        ];
    }

    //添加自定义类型到 type表
    public function actionAdd()
    {
        $request = Yii::$app->request;
        $ty = $request->post('type');
        $description = $request->post('description');
        //echo $ty.'--'.$description;
        if(empty($ty) || empty($description))
        {
            $result['code'] = 201;
            $result['data']['item'][] = 'type和description不能为空';
            HttpResponseUtil::setJsonResponse($result);
            return;
        }
        $count_type = Type::find()->Where(['type'=>$ty])->count();
        if($count_type > 0)
        {
            $result['code'] = 202;
            $result['data']['item'][] = 'type不能重复定义';
            HttpResponseUtil::setJsonResponse($result);
            return;
        }
        $type = new Type();
        $type->type = $ty;
        $type->description = $description;
        if($type->save() > 0)
        {
            $message =  "添加成功";
        }
        else
        {
            $message =  "添加失败";
        }
        $result['code'] = 200;
        $result['data']['item'][] = $message;
        HttpResponseUtil::setJsonResponse($result);
    }
    //查询type表的type 和 description 字段。
    public function actionQuery()
    {
        $types = Type::find()->all();
        foreach($types as $k => $v)
        {
            $type[$k][] = $v['id'];
            $type[$k][] = $v['type'];
            $type[$k][] = $v['description'];
        }
        //var_dump($type);
        $result['code'] = 200;
        $result['data']['item'][] = $type;
        HttpResponseUtil::setJsonResponse($result);
    }

    //按小时自定义查询
    public function actionCustomHoursQuery()
    {
        $logger = MMLogger::getLogger(__FUNCTION__);
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $da = $request->post('day');
        $ty = $request->post('type');
        //echo $appkey.'--'.$day.'<br/>';    //查看接收到的参数

        if($da == 'today')
        {
            $day = 0;
        }
        else if($da == 'yesterday')
        {
            $day = 1;
        }

        //定义 1天 ，1小时，1分钟 的总秒数。【为了优化】
        $d = 86400;
        $h = 3600;
        $m = 60;
        $today = getdate(); //得到一个数组
        $totalSeconds = ($today['hours'])*$h + ($today['minutes'])*$m + ($today['seconds']); //得出当天的总秒数
        //$logger->error($day);
        //day = 0为今天，1为昨天，得到所请求的 当天开始时间
        if($day == 1)
        {
            //表示昨天
            $startTime = $today['0'] - $totalSeconds - $d; //得到昨天 0 时的时间戳
            $endTime = $today['0']-$totalSeconds;
        }
        else if($day == 0)
        {
            //表示今天
            $startTime = $today['0'] - $totalSeconds; //得到今天 0 时的时间戳
            $endTime =  $today['0'] - $totalSeconds + $d;   //得到今天 24：00点的 时间戳
        }
        else
        {
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
            $startTime2 = date('Y-m-d H:i:s', $startTime + $i * $h);
            $endTime2 = date('Y-m-d H:i:s', $startTime + ($i + 1) * $h);
            //得到 00：00-00:59 的时间区间
            $time_interval = date('H:i', $startTime + $i * $h) . '-' . date('H:i', ($startTime + ($i + 1) * $h) - 1);
            //$logger->error($startTime2);    //打印出第二种格式看下是否出错
            //$logger->error($endTime2);
            //$logger->error($time_interval);
            //得到时间区间
            $interval[] = $time_interval;
            //查找所需要查询的信息
            $custom_count[$i] = (int)Scount::find()->Where(['appkey' => $appkey, 'type' => $ty])->andWhere(['>=', 'time', $startTime2])->andWhere(['<', 'time', $endTime2])->count();
        }
        $startTime3 = date('Y-m-d H:i:s',$startTime);
        $endTime3 = date('Y-m-d H:i:s',$endTime);
        $custom_infos = Scount::find()->where(['appkey' => $appkey,'type'=> $ty])->andWhere(['>=','time',$startTime3])->andWhere(['<','time',$endTime3])->all();

        if(empty($custom_infos))
        {
            $custom_info = array();
        }
        else
        {
            foreach($custom_infos as $k=>$v)
            {
                $url = Page::findOne($v['page']);
                $custom_info[$k][] = $url['page_url'];
                $custom_info[$k][] = $v['time'];
                $custom_info[$k][] = $v['message'];
            }
            $warnInfo = array();
        }
        $result['code'] = 200;
        $result['data']['item'][] = $date;
        $result['data']['item'][] = $hours;
        $result['data']['item'][] = $interval;
        $result['data']['item'][] = $custom_count;
        $result['data']['item'][] = $custom_info;
        HttpResponseUtil::setJsonResponse($result);
    }

    //按天自定义查询
    public function actionCustomDaysQuery()
    {
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $da = $request->post('day');
        $ty = $request->post('type');
        //echo 'appkey为：'.$appkey.'--day为：'.$day.'<br/>';
        $logger = MMLogger::getLogger(__FUNCTION__);    //为了打印，实例化一个对象
        //$logger->error(变量名);  //用法：调用实例化的对象，调用error方法，在 /runtime/logs/  目录下查看打印的结果

        if($da == 'week')
        {
            $day = 6;
        }
        else if($da == 'month')
        {
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

        if($day == 6)
        {
            $day_name = $sb_date.'-'.$to_date;
            //echo $day_name;
            for($i = 0; $i < 7; $i++)
            {
                //那天的时间 $that_day_startTime
                $td_startTime = date('Y-m-d H:i:s', $sb_startTime + $i * $d);
                $td_endTime = date('Y-m-d H:i:s', $sb_endTime + ($i) * $d);
                //echo '那天的开始时间'.$td_startTime.'那天的结束时间'.$td_endTime.'<br/>';
                $day_dates[] = date('Y-m-d', $sb_startTime + $i * $d);  //得到时间数组
                $thatDay_custom_count[] = (int)Scount::find()->Where(['appkey' => $appkey, 'type' => $ty])->andWhere(['>=', 'time', $td_startTime])->andWhere(['<', 'time', $td_endTime])->count();
            }
            $sb_startTime2 = date('Y-m-d H:i:s',$sb_startTime);
            $to_endTime2 = date('Y-m-d H:i:s',$to_endTime);
            //echo $sb_startTime2.'---'.$to_endTime2;
            //var_dump($errInfos);
            $thatDay_custom_Infos = Scount::find()->where(['appkey' => $appkey,'type'=> $ty])->andWhere(['>=','time',$sb_startTime2])->andWhere(['<','time',$to_endTime2])->all();
            if(empty($thatDay_custom_Infos))
            {
                $thatDay_custom_Info = array();
            }
            else
            {
                foreach($thatDay_custom_Infos as $k=>$v)
                {
                    $url = Page::findOne($v['page']);
                    $thatDay_custom_Info[$k][] = $url['page_url'];
                    $thatDay_custom_Info[$k][] = $v['time'];
                    $thatDay_custom_Info[$k][] = $v['message'];
                }
            }
        }
        else if($day == 29)
        {
            $day_name = $tb_date.'-'.$to_date;
            //echo $day_name;
            for($i = 0; $i < 30; $i++)
            {
                $day_number[] = $i; //得到天数
                //那天的时间 $that_day_startTime
                $td_startTime = date('Y-m-d H:i:s',$tb_startTime + $i * $d);
                $td_endTime = date('Y-m-d H:i:s',$tb_endTime + ( $i ) * $d);
                //echo '那天的开始时间'.$td_startTime.'那天的结束时间'.$td_endTime.'<br/>';
                $day_dates[] = date('Y-m-d',$tb_startTime + $i * $d);
                //var_dump($day_date);
                $thatDay_data[$i][] = date('Y-m-d',$sb_startTime + $i * $d);
                $thatDay_custom_count[] = (int)Scount::find()->Where([ 'appkey'=>$appkey ,'type' =>$ty ])->andWhere([ '>=', 'time', $td_startTime ])->andWhere([ '<', 'time', $td_endTime ])->count();
            }
            $td_startTime2 = date('Y-m-d H:i:s',$tb_startTime);
            $to_endTime2 = date('Y-m-d H:i:s',$to_endTime);
            //echo $tb_startTime2.'---'.$to_endTime2;
            $thatDay_custom_Infos = Scount::find()->where(['appkey' => $appkey,'type'=> $ty])->andWhere(['>=','time',$td_startTime2])->andWhere(['<','time',$to_endTime2])->all();
            if(empty($thatDay_custom_Infos))
            {
                $thatDay_custom_Info = array();
            }
            else
            {
                foreach($thatDay_custom_Infos as $k=>$v)
                {
                    $url = Page::findOne($v['page']);
                    $thatDay_custom_Info[$k][] = $url['page_url'];
                    $thatDay_custom_Info[$k][] = $v['time'];
                    $thatDay_custom_Info[$k][] = $v['message'];
                }
            }
        }
        else
        {
            $result['code'] = 201;
            $result['data']['item'][] = '不正确的type值';
            HttpResponseUtil::setJsonResponse($result);
            return;
        }
        $result['code'] = 200;
        $result['data']['item'][] = $day_name;
        $result['data']['item'][] = $day_dates;
        $result['data']['item'][] = $thatDay_custom_count;
        $result['data']['item'][] = $thatDay_custom_Info;
        HttpResponseUtil::setJsonResponse($result);
    }



}
