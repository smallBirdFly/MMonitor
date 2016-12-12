<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/10 0010
 * Time: 17:30
 */
namespace statistics\controllers;

use common\utils\HttpResponseUtil;
use statistics\models\Daycount;
use statistics\models\Ipaddress;

use Yii;
use yii\console\Controller;

class AnalysisController extends Controller
{

    public function actionAnaly()
    {
        $request = Yii::$app->request;

        $query = Daycount::find();
        //根据spmcode查找
        if ($request->post('spmcode')) {
            $query = $query->andWhere(['spmcode' => $request->post('spmcode')]);
            Yii::error($request->post('spmcode'));
        }
        //根据类型查找
        if($request->post('type'))
        {
            $query = $query->andWhere(['type' => $request->post('type')]);
        }
        //根据IP地址查找
        if ($request->post('ip')) {
            $query = $query->andWhere(['ip' => $request->post('ip')]);
        }
        //根据来源查找
        if ($request->post('referrer')) {
            $query = $query->andWhere(['referrer' => $request->post('referrer')]);
        }
        //根据错误消息查找信息
        if($request->post('message'))
        {
            $query = $query->andWhere(['like','message',$request->post('message')]);
        }
        //查找最近一天的信息
        if ($request->post('time') == 'day') {
            $starttime = time() - 60 * 60 * 24;
            $query->andWhere(['>=', 'time', $starttime]);
        }
        //查找最近一周的信息
        if ($request->post('time') == 'week') {
            $starttime = time() - 7 * 60 * 60 * 24;
            $query->andWhere(['>=', 'time', $starttime]);
        }
        //查找最近一个月的信息
        if ($request->post('time') == 'month') {
            $starttime = time() - 7 * 30 * 60 * 60 * 24;
            $query->andWhere(['>=', 'time', $starttime]);
        }
        //根据时间区间查找信息
        if ($request->post('starttime') && $request->post('endtime'))
        {
            $starttime = $request->post('starttime');
            $endtime = $request->post('endtime');
            $query->andWhere(['>=', 'time', $starttime]);
            $query->andWhere(['<=', 'time', $endtime]);
        }

        $result = $query->all();
        if(empty($result))
        {
            $res['code'] = 500;
            HttpResponseUtil::setJsonResponse($res);
        }
        $num = 0;
        foreach($result as $count)
        {
            $num += $count->num;
        }
        $s['code'] = 200;
        $s['data']['count'] = $num;
        $s['data']['total'] = Daycount::find()->sum('num');
        $s['data']['percent'] = round(100*$num/$s['data']['total'],2).'%';
        HttpResponseUtil::setJsonResponse($s);
    }

//    统计访问量
    public function actionDaypv()
    {
        $request = Yii::$app->request;
        $spmcode = $request->post('spmcode');
        if(empty($request->post('endDay')))
        {
//       查看最近一天、一周、一个月的访问量
            if($request->post('startDay') == 1 || $request->post('startDay') == 7 || $request->post('startDay') == 30)
            {
                $startTime = date('Y-m-d H:i:s',time()-24*60*60*$request->post('startDay'));
                $endTime = date('Y-m-d H:i:s',time());
            }
            //统计某一天的访问量
            else if(strtotime($request->post('startDay')))
            {
                $startTime = date('Y-m-d H:i:s',strtotime($request->post('startDay')));
                $endTime = date('Y-m-d H:i:s',strtotime($request->post('startDay'))+86400);

            }
            else
            {
                $result['code'] = 201;
                HttpResponseUtil::setJsonResponse($result);
                return;
            }
        }
        else
        {
//        统计某个时间内的访问量
            if(strtotime($request->post('endDay')))
            {
                $startTime = date('Y-m-d H:i:s',strtotime($request->post('startDay')));
                $endTime = date('Y-m-d H:i:s',strtotime($request->post('endDay')));
            }
            else
            {
                $result['code'] = 202;
                HttpResponseUtil::setJsonResponse($result);
                return;
            }
        }
//
//        Yii::error($endTime);
        $res = Daycount::find()->where(['>=','time',$startTime])->andWhere(['<','time',$endTime])->andWhere(['spmcode'=>$spmcode,'type'=>1])->all();
        $result['code'] = 200;
        $result['total'] = count($res);
        $result['data']['content'] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

    //独立访问量
    public function actionUniquev()
    {
        $request = Yii::$app->request;
        if($request->post('date'))
        {
            $request = Yii::$app->request;
            if(!strtotime($request->post('date')))
            {
                $result['code'] = 201;
                HttpResponseUtil::setJsonResponse($result);
                return;
            }
            $day = date('Y-m-d H:i:s',strtotime($request->post('day'))+86400);
            $res = Daycount::find()->where(['<','time',$day])->andWhere(['>=','time',$request->post('day')])->groupBy('ip')->all();
            $result['code'] = 200;
            $result['total'] = count($res);
            $result['data']['content'] = $res;
            HttpResponseUtil::setJsonResponse($result);
        }
        else
        {
            //查询所有的ip
            $total = Ipaddress::find()->count();
            $result['code'] = 200;
            $result['total'] = $total;
            $result['data']['content'] = Ipaddress::find()->all();
            HttpResponseUtil::setJsonResponse($result);
        }
    }

    //新访客
    public function actionNewv()
    {
        $request = Yii::$app->request;
        if(!strtotime($request->post('day')))
        {
            $result['code'] = 201;
            HttpResponseUtil::setJsonResponse($result);
            return;
        }
        $day = date('Y-m-d H:i:s',strtotime($request->post('day'))+86400);
        $res = Ipaddress::find()->where(['<','created_at',$day])->andWhere(['>=','created_at',$request->post('day')])->all();
        $result['total'] = count($res);
        $result['data']['content'] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

    //访问深度，针对某个网站级，用户平均对该网站浏览的网页数量
    public function actionPdepth()
    {
        $request = Yii::$app->request;
        $res = Daycount::find()->where(['spmcode','like',$request->post('spmcode')])->groupBy('ip')->all();
        $num = 0;
        foreach($res as $item)
        {
            $num += $item->num;
        }
        $s['code'] = 200;
        $s['data']['count'] = $num;
        $s['data']['total'] = Daycount::find()->where(['spmcode','like',$request->post('spm')])->count();
        $s['data']['percent'] = round(100*$num/$s['data']['total'],2).'%';
        HttpResponseUtil::setJsonResponse($s);
    }

    //访问时间，1、对网页某段时间内停留的总时间；2、用户页面停留的平均时间
    public function actionPtime()
    {
        $request = Yii::$app->request;
        //页面总停留时间
        $webs = Daycount::find()->where(['spmcode'=>$request->post('spmcode')])->groupBy('ip')->all();
        Daycount::find()->where(['type=1'])->groupBy(['spmcode']);
    }

    //访问最多页面
    public function actionMaxv()
    {
//        $spmcode = Daycount::find()->where(['spmcode'=>Daycount::find()->groupBy('spmcode')->having('spmcode')->count()]);
        $spmcode = Daycount::find()->groupBy('spmcode')->all();
        foreach ($spmcode as $item)
        {
            $data['count'] = Daycount::find()->where(['spmcode' => $item->spmcode])->count();
            $data['spmcdoe'] = $item->spmcode;
        }
        $spmcode = Daycount::find()->distinct()->count('spmcode');
        var_dump($spmcode);
    }

    //    来源分析
    public function actionReferrer()
    {
        $request = Yii::$app->request;
        //输入某个网站
        $referrer = Daycount::find()->where(['spmcode'=>$request->post('spmcode'),'type'=>1]);
        $enter = $referrer->andWhere(['referrer'=>1])->count();
        $link = $referrer->andWhere(['referrer'=>2])->count();
        $seo = $referrer->andWhere(['referrer'=>3])->count();
        $enter = $referrer->andWhere(['referrer'=>4])->count();

    }
//    出现警告
    public function actionAppearwarn()
    {
        $request = Yii::$app->request;
        $warms = Daycount::find()->where(['type'=>0,$request->post('spmcode')])->all();
        if(empty($warms))
        {
            //无警告现象出现
        }
        else
        {
            foreach($warms as $warm)
            {
                $result['data']['content'][] = json_decode($warm->message);
            }
            $result['num'] = count($warms);
            HttpResponseUtil::setJsonResponse($result);
        }
    }

//出现错误
    public function actionAppearError()
    {
        $request = Yii::$app->request;
        $errors = Daycount::find()->where(['type'=>-1,$request->post('spmcode')])->all();
        if(empty($errors))
        {
            //无警告现象出现
        }
        else
        {
            foreach($errors as $error)
            {
                $result['data']['content'][] = json_decode($error->message);
            }
            $result['num'] = count($errors);
            HttpResponseUtil::setJsonResponse($result);
        }
    }

    public function actionTest()
    {
    }
}