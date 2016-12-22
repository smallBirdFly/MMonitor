<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/20 0020
 * Time: 11:53
 */
namespace statistics\controllers;

use common\utils\HttpResponseUtil;
use statistics\models\Page;
use statistics\models\Scount;
use Yii;
use yii\web\Controller;

class InterviewedController extends Controller
{
    public $enableCsrfValidation = false;

    public function actionInterview()
    {
        $request = Yii::$app->request;
        $appkey = $request->post('appkey');
        $starDay = strtotime($request->post('startDay'));
        $endDay = strtotime($request->post('endDay'));
        if($starDay == $endDay)
        {
            $startTime = date('Y-m-d',$starDay);
            $endTime = date('Y-m-d',$endDay + 86400);
            $pages = Scount::find()->where(['type'=>1,'appkey'=>$appkey])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('page')->all();
            //访问总数
            $sumPv = 0;
            // 用户总数
            $sumIp = 0;
            foreach($pages as $i=>$page)
            {
                $page_url = Page::findOne($page->page);
                $name[][] = $page_url->page_url;
                $pv = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'page'=>$page_url->id])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                $res[$i][] = $pv;
                $sumPv += $pv;
                $ip = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'page'=>$page_url->id])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
                $res[$i][] = $ip;
                $sumIp += $ip;
            }
        }
        else
        {
            //当前时间
            $startTime = date('Y-m-d', $starDay);
            $endTime = date('Y-m-d', $endDay + 86400);
            $pages = Scount::find()->where(['type'=>1,'appkey'=>$appkey])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('page')->all();
            //访问总数
            $sumPv = 0;
//        用户总数
            $sumIp = 0;
            foreach($pages as $i=>$page)
            {
                $page_url = Page::findOne($page->page);
                $name[][] = $page_url->page_url;
                $pv = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'page'=>$page_url->id])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                $res[$i][] = $pv;
                $sumPv += $pv;
                $ip = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'page'=>$page_url->id])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
                $res[$i][] = $ip;
                $sumIp += $ip;
            }

        }
        $sum[] = $sumPv;
        $sum[] = $sumIp;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $name;
        $result['data']['item'][] = $res;
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
            $pages = Scount::find()->where(['type'=>1,'appkey'=>$appkey])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('page')->all();
            //访问总数
            $sumPv = 0;
            // 用户总数
            $sumIp = 0;
            foreach($pages as $i=>$page)
            {
                $page_url = Page::findOne($page->page);
                $name[][] = $page_url->page_url;
                $pv = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'page'=>$page_url->id])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->count();
                $res1[$i][] = $pv;
                $sumPv += $pv;
                $ip = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'page'=>$page_url->id])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('ip')->count();
                $res1[$i][] = $ip;
                $sumIp += $ip;
                $cpv = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'page'=>$page_url->id])->andWhere(['>=','time',$cstartTime])->andWhere(['<','time',$cendTime])->count();
                $cip = Scount::find()->where(['appkey' => $appkey,'type'=> 1,'page'=>$page_url->id])->andWhere(['>=','time',$cstartTime])->andWhere(['<','time',$cendTime])->groupBy('ip')->count();
                $res2[$i][] = $cpv;
                $res2[$i][] = $cip;
            }
        }
        else
        {
            //比较某些天和某些天的
            $startTime = date('Y-m-d',$compareStartDay);
            $endTime = date('Y-m-d',$compareEndDay + 86400);
            $cstartTime = date('Y-m-d',$comparedStartDay);
            $cendTime = date('Y-m-d',$comparedStartDay + $compareEndDay - $compareStartDay + 86400);
            $pages = Scount::find()->where(['type'=>1,'appkey'=>$appkey])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('page')->all();
            //访问总数
            $sumPv = 0;
            // 用户总数
            $sumIp = 0;
            foreach($pages as $i=>$page)
            {
                $page_url = Page::findOne($page->page);
                $name[][] = $page_url->page_url;
                $pv = Scount::find()->where(['appkey' => $appkey, 'type' => 1, 'page' => $page_url->id])->andWhere(['>=', 'time', $startTime])->andWhere(['<', 'time', $endTime])->count();
                $res1[$i][] = $pv;
                $sumPv += $pv;
                $ip = Scount::find()->where(['appkey' => $appkey, 'type' => 1, 'page' => $page_url->id])->andWhere(['>=', 'time', $startTime])->andWhere(['<', 'time', $endTime])->groupBy('ip')->count();
                $res1[$i][] = $ip;
                $sumIp += $ip;
                $cpv = Scount::find()->where(['appkey' => $appkey, 'type' => 1, 'page' => $page_url->id])->andWhere(['>=', 'time', $cstartTime])->andWhere(['<', 'time', $cendTime])->count();
                $cip = Scount::find()->where(['appkey' => $appkey, 'type' => 1, 'page' => $page_url->id])->andWhere(['>=', 'time', $cstartTime])->andWhere(['<', 'time', $cendTime])->groupBy('ip')->count();
                $res2[$i][] = $cpv;
                $res2[$i][] = $cip;
            }
        }
        $sum[] = $sumPv;
        $sum[] = $sumIp;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $name;
        $result['data']['item'][] = $res1;
        $result['data']['item'][] = $res2;
        HttpResponseUtil::setJsonResponse($result);
    }

}