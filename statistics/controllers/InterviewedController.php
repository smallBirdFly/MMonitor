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

    //今天访问记录
    public function actionToday()
    {
        $appkey = Yii::$app->request->post('appkey');
        //当前时间
        $startTime = date('Y-m-d', time());
        $endTime = date('Y-m-d', time()-86400);
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
        $sum[] = $sumPv;
        $sum[] = $sumIp;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $name;
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

//    昨天的访问记录
    public function actionYesterday()
    {
        $appkey = Yii::$app->request->post('appkey');
        //当前时间
        $startTime = date('Y-m-d', strtotime("-1 day"));
        $endTime = date('Y-m-d', time());
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
        $sum[] = $sumPv;
        $sum[] = $sumIp;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $name;
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

    //最近7天访问记录
    public function actionWeek()
    {
        $appkey = Yii::$app->request->post('appkey');
        //当前时间
        $startTime = date('Y-m-d', time()-86400*6);
        $endTime = date('Y-m-d H:i:s', time());
        Yii::error($startTime);
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
        $sum[] = $sumPv;
        $sum[] = $sumIp;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $name;
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

    //最近30天访问记录
    public function actionMonth()
    {
        $appkey = Yii::$app->request->post('appkey');
        //当前时间
        $startTime = date('Y-m-d', time()-86400*29);
        $endTime = date('Y-m-d H:i:s', time());
        Yii::error($startTime);
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
        $sum[] = $sumPv;
        $sum[] = $sumIp;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $name;
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

//    某时间区间内的访问记录
    public function actionRange()
    {
        $appkey = Yii::$app->request->post('appkey');
        //当前时间
        $startTime = Yii::$app->request->post('startTime');
        $endTime = Yii::$app->request->post('endTime');
        $pages = Scount::find()->where(['type'=>1,'appkey'=>$appkey])->andWhere(['>=','time',$startTime])->andWhere(['<','time',$endTime])->groupBy('page')->all();
//        var_dump($pages);die;
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
        $sum[] = $sumPv;
        $sum[] = $sumIp;
        $result['code'] = 200;
        $result['data']['sum'][] = $sum;
        $result['data']['item'][] = $name;
        $result['data']['item'][] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }



}