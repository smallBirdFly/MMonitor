<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/20 0020
 * Time: 11:53
 */
namespace statistics\controllers;

use common\utils\HttpResponseUtil;
use statistics\component\AuthFilter;
use statistics\models\Page;
use statistics\models\Scount;
use Yii;
use yii\web\Controller;

class InterviewedController extends Controller
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

    public function actionInterview()
    {
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $starDay = strtotime($request->post('startTime'));
        $endDay = strtotime($request->post('endTime'));
//        Yii::error($request->post());
        if($starDay == $endDay)
        {
            $startTime = date('Y-m-d',$starDay);
            $endTime = date('Y-m-d',$endDay + 86400);
            $pages = Page::find()->where(['appkey'=>$appkey])->all();
            //访问总数
            $sumPv = 0;
            // 用户总数
            $sumIp = 0;
            foreach($pages as $i=>$page)
            {
                $name = $page->page_url;
                $pv = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'page'=>$page->id])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                $res[$i][] = $name;
                $res[$i][] = $pv;
                $sumPv += $pv;
                $ip = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'page'=>$page->id,'visit'=>1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                $res[$i][] = $ip;
                $sumIp += $ip;
            }
            $date[]= $request->post('startTime');
        }
        else
        {
            //当前时间
            $startTime = date('Y-m-d', $starDay);
            $endTime = date('Y-m-d', $endDay + 86400);
            $pages = Page::find()->where(['appkey'=>$appkey])->all();
            //访问总数
            $sumPv = 0;
//        用户总数
            $sumIp = 0;
            foreach($pages as $i=>$page)
            {
                $name = $page->page_url;
                $pv = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'page'=>$page->id])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                $res[$i][] = $name;
                $res[$i][] = $pv;
                $sumPv += $pv;
                $ip = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'page'=>$page->id,'visit'=>1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                $res[$i][] = $ip;
                $sumIp += $ip;
            }
            $date[]= $request->post('startTime').' - '.$request->post('endTime');;
        }
        $sum[] = $sumPv;
        $sum[] = $sumIp;
        $result['code'] = 200;
        $result['data']['item'][] = $sum;
        $result['data']['item'][] = $res;
        $result['data']['item'][] = $date;
        HttpResponseUtil::setJsonResponse($result);
    }

    public function actionCompareInterview()
    {
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $compareStartDay = strtotime($request->post('compareStartDay'));
        $compareEndDay = strtotime($request->post('compareEndDay'));
        $comparedStartDay = strtotime($request->post('comparedStartDay'));
        if($compareStartDay == $compareEndDay)
        {
            //比较某一天和某一天的
            $startTime = date('Y-m-d',$compareStartDay);
            $endTime = date('Y-m-d',$compareStartDay + 86400);
            $cstartTime = date('Y-m-d',$comparedStartDay);
            $cendTime = date('Y-m-d',$comparedStartDay + 86400);
            $pages = Page::find()->where(['appkey'=>$appkey])->all();
            //访问总数
            $sumPv = 0;
            // 用户总数
            $sumIp = 0;
            foreach($pages as $i=>$page)
            {
                $name = $page->page_url;
                $pv = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'page'=>$page->id])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                $ip = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'page'=>$page->id,'visit'=>1])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                //url
                $res[$i][] = $name;
                //时间1
                $res[$i][] = $request->post('compareStartDay');
                $res[$i][] = $pv;
                $res[$i][] = $ip;
                //时间2
                $cpv = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'page'=>$page->id])->andWhere(['>=','time',$cstartTime])->andWhere(['<','time',$cendTime])->count();
                $cip = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'page'=>$page->id,'visit'=>1])->andWhere(['>=','time',$cstartTime])->andWhere(['<','time',$cendTime])->count();
                $res[$i][] = $request->post('comparedStartDay');
                $res[$i][] = $cpv;
                $res[$i][] = $cip;
                //总数
                $sumPv += $pv;
                $sumIp += $ip;
            }
        }
        else
        {
            //比较某些天和某些天的
            $startTime = date('Y-m-d',$compareStartDay);
            $endTime = date('Y-m-d',$compareEndDay + 86400);
            $cstartTime = date('Y-m-d',$comparedStartDay);
            $cendTime = date('Y-m-d',$comparedStartDay + $compareEndDay - $compareStartDay + 86400);
            $pages = Page::find()->where(['appkey'=>$appkey])->all();
            //访问总数
            $sumPv = 0;
            // 用户总数
            $sumIp = 0;
            foreach($pages as $i=>$page)
            {
                $name = $page->page_url;
                $pv = Scount::find()->where(['appkey' => $appkey, 'type' => 1, 'page' => $page->id])->andWhere(['>=', 'time', $startTime])->andWhere(['<', 'time', $endTime])->count();
                $ip = Scount::find()->where(['appkey' => $appkey, 'type' => 1, 'page' => $page->id,'visit'=>1])->andWhere(['>=', 'time', $startTime])->andWhere(['<', 'time', $endTime])->count();
                //url
                $res[$i][] = $name;
                //时间1
                $res[$i][] = $request->post('compareStartDay').'-'.$request->post('compareEndDay');
                $res[$i][] = $pv;
                $res[$i][] = $ip;
                $cpv = Scount::find()->where(['appkey' => $appkey, 'type' => 1, 'page' => $page->id])->andWhere(['>=', 'time', $cstartTime])->andWhere(['<', 'time', $cendTime])->count();
                $cip = Scount::find()->where(['appkey' => $appkey, 'type' => 1, 'page' => $page->id,'visit'=>1])->andWhere(['>=', 'time', $cstartTime])->andWhere(['<', 'time', $cendTime])->count();
                $res[$i][] = $request->post('comparedStartDay').'-'.date("Y-m-d",$comparedStartDay + $compareEndDay - $compareStartDay);
                $res[$i][] = $cpv;
                $res[$i][] = $cip;
                //总数
                $sumPv += $pv;
                $sumIp += $ip;
            }
        }
        $sum[] = $sumPv;
        $sum[] = $sumIp;
        $result['code'] = 200;
        $result['data']['item'][] = $sum;
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

}