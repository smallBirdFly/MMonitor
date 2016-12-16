<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/10 0010
 * Time: 17:30
 */
namespace statistics\controllers;

use common\utils\HttpResponseUtil;
use statistics\models\AccessLog;
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
    public function actionRange()
    {
        $request = Yii::$app->request;
        $spmcode = $request->post('spmcode');
        if(empty($request->post('startDay')) || empty($request->post('endDay')))
        {
            $result['code'] = 201;
            HttpResponseUtil::setJsonResponse($result);
            Yii::error('输入信息不完全');
            return;
        }
        else
        {
//            如果开始时间大于结束时间
//            转换时间
            if(strtotime($request->post('startDay')) && strtotime($request->post('endDay')))
            {

                $startTime = date('Y-m-d H:i:s',strtotime($request->post('startDay')));
                $endTime = date('Y-m-d H:i:s',strtotime($request->post('endDay')));
            }
            else
            {
                $result['code'] = 202;
                HttpResponseUtil::setJsonResponse($result);
                Yii::error('日期格式不正确');
                return;
            }
        }
        $res = Daycount::find()->where(['>=','time',$startTime])->andWhere(['<','time',$endTime])->andWhere(['spmcode'=>$spmcode,'type'=>1])->all();
        $result['code'] = 200;
        $result['total'] = count($res);
        $result['data']['content'] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

//统计某一天访问量
    public function actionDay()
    {
        $request = Yii::$app->request;
        if(empty($request->post('day')) || empty($request->post('spmcode')))
        {
            $result['code'] = 201;
            HttpResponseUtil::setJsonResponse($result);
            Yii::error('输入信息不完全');
            return;
        }
        if(strtotime($request->post('day')))
        {
            $startTime = date('Y-m-d',strtotime($request->post('day')));
            $endTime = date('Y-m-d',strtotime($request->post('day')+1));
        }
        else
        {
            $result['code'] = 202;
            HttpResponseUtil::setJsonResponse($result);
            Yii::error('日期格式不正确');
            return;
        }
        $res = Daycount::find()->where(['>=','time',$startTime])->andWhere(['<','time',$endTime])->andWhere(['spmcode'=>$request->post('spmcode'),'type'=>1])->all();
        $result['code'] = 200;
        $result['total'] = count($res);
        $result['data']['content'] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

    //最近24小时的统计访问
    public function actionHours()
    {
        $request = Yii::$app->request;
        if(empty($request->post('spmcode')))
        {
            $result['code'] = 201;
            HttpResponseUtil::setJsonResponse($result);
            Yii::error('输入信息不完全');
            return;
        }
        $startTime = date('Y-m-d H:i:s',time()-86400);
        $endTime = date('Y-m-d H:i:s',time());
        $res = Daycount::find()->where(['>=','time',$startTime])->andWhere(['<','time',$endTime])->andWhere(['spmcode'=>$request->post('spmcode'),'type'=>1])->all();
        $result['code'] = 200;
        $result['total'] = count($res);
        $result['data']['content'] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

//    最近一周的统计访问
    public function actionWeek()
    {
        $request = Yii::$app->request;
        if(empty($request->post('spmcode')))
        {
            $result['code'] = 201;
            HttpResponseUtil::setJsonResponse($result);
            Yii::error('输入信息不完全');
            return;
        }
        $startTime = date('Y-m-d H:i:s',time()-86400*7);
        $endTime = date('Y-m-d H:i:s',time());
        $res = Daycount::find()->where(['>=','time',$startTime])->andWhere(['<','time',$endTime])->andWhere(['spmcode'=>$request->post('spmcode'),'type'=>1])->all();
        $result['code'] = 200;
        $result['total'] = count($res);
        $result['data']['content'] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

//    最近一个月的统计访问
    public function actionMonth()
    {
        $request = Yii::$app->request;
        if(empty($request->post('spmcode')))
        {
            $result['code'] = 201;
            HttpResponseUtil::setJsonResponse($result);
            Yii::error('输入信息不完全');
            return;
        }
        $startTime = date('Y-m-d H:i:s',time()-86400*30);
        $endTime = date('Y-m-d H:i:s',time());
        $res = Daycount::find()->where(['>=','time',$startTime])->andWhere(['<','time',$endTime])->andWhere(['spmcode'=>$request->post('spmcode'),'type'=>1])->all();
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

    //新访客，一天之内第一次访问该网站的访客
    public function actionNewv()
    {
        $res = Ipaddress::find()->where(['>=','created_at',date('Y-m-d H:i:s',time()-86400)])->all();
        $result['total'] = count($res);
        $result['data']['content'] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

    //访问深度，针对某个网站级，用户平均对该网站浏览的网页数量
    public function actionDepthv()
    {
        $request = Yii::$app->request;
        if(empty($request->post('spmcode')))
        {
            $s['code'] = 201;
            HttpResponseUtil::setJsonResponse($s);
            Yii::error('信息不完全');
            return;
        }
        $res = Daycount::find()->where(['like','spmcode',$request->post('spmcode')])->andWhere(['type'=>1]);
        $count = $res->count();
//        访问过程的次数
        $num = $res->count();
        $s['code'] = 200;
        $s['data']['count'] = $count/$num;
        Yii::error($count);
        Yii::error($num);
        HttpResponseUtil::setJsonResponse($s);
    }

    //访问时间，页面停留的平均时间
    public function actionPtime()
    {
        $request = Yii::$app->request;
        $webs = Daycount::find()->where(['spmcode'=>$request->post('spmcode'),'type'=>1])->all();
        $ptime = 0;
        foreach($webs as $web)
        {
            $startTime = strtotime($web->time);
            $end = Daycount::find()->where(['ip'=>$web->ip,'type'=>2])->andWhere(['>','time',$web->time])->orderBy('time')->one();
            if(empty($end))
            {
                return;
            }
            $endTime = strtotime($end->time);
//            Yii::error($web->time);
//            Yii::error($end->time);
//            Yii::error($endTime-$startTime);
            $ptime += $endTime-$startTime;
        }
        $res['spmcode'] = $web->spmcode;
        $res['time'] = $ptime/count($webs).'s';
        $result['code'] = 200;
        $result['data']['content'] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

    //访问最多页面
    public function actionMaxv()
    {
        $spmcode = Daycount::find()->groupBy('spmcode')->all();
        $max = 0;
        $spm = null;
        foreach ($spmcode as $item)
        {
            $count = Daycount::find()->where(['spmcode'=>$item->spmcode,'type'=>1])->count();
            if($count > $max)
            {
                $max  = $count;
                $spm = Daycount::find()->where(['spmcode'=>$item->spmcode,'type'=>1])->one();
            }
        }
        $result['code'] = 200;
        $result['data']['spmcode'] = $spm->spmcode;
        HttpResponseUtil::setJsonResponse($result);
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

//    出现警告最多的页面
    public function actionMaxwarn()
    {
        $warns = Daycount::find()->where(['type'=>0])->groupBy('spmcode')->all();
        $max = 0;
        foreach ($warns as $warn)
        {
            $count = Daycount::find()->where(['spmcode'=>$warn->spmcode,'type'=>0])->count();
            if($count > $max)
            {
                $max  = $count;
                $spm = Daycount::find()->where(['spmcode'=>$warn->spmcode,'type'=>0])->one();
            }
        }
        $result['code'] = 200;
        $result['data']['spmcode'] = $spm->spmcode;
        HttpResponseUtil::setJsonResponse($result);
    }

//出现错误最多的页面
    public function actionMaxerror()
    {
        $errors = Daycount::find()->where(['type'=>-1])->groupBy('spmcode')->all();
        $max = 0;
        foreach ($errors as $error)
        {
            $count = Daycount::find()->where(['spmcode'=>$error->spmcode,'type'=>-1])->count();
            if($count > $max)
            {
                $max  = $count;
                $spm = Daycount::find()->where(['spmcode'=>$error->spmcode,'type'=>-1])->one();
            }
        }
        $result['code'] = 200;
        $result['data']['spmcode'] = $spm->spmcode;
        HttpResponseUtil::setJsonResponse($result);
    }

    //查询某个页面所有的警告信息
    public function actionWarns()
    {
        $request = Yii::$app->request;
        $errors = Daycount::find()->where(['type'=>0,'spmcode'=>$request->post('spmcode')])->all();
        foreach($errors as $error)
        {
            $attr['spmcode'] = $error->spmcode;
            $attr['error'] = json_decode($error->message);
            $res[] = $attr;
        }
        $result['code'] = 200;
        $result['total'] = count($res);
        $result['data']['errors'] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

    //查询某个网页的所有错误信息
    public function actionErrors()
    {
        $request = Yii::$app->request;
        $errors = Daycount::find()->where(['type'=>-1,'spmcode'=>$request->post('spmcode')])->all();
        foreach($errors as $error)
        {
            $attr['spmcode'] = $error->spmcode;
            $attr['error'] = json_decode($error->message);
            $res[] = $attr;
        }
        $result['code'] = 200;
        $result['total'] = count($res);
        $result['data']['errors'] = $res;
        HttpResponseUtil::setJsonResponse($result);
    }

    //网站的访问过程
    private function webVisit()
    {
        $request = Yii::$app->request;
        $webs = Daycount::find()->where(['type'=>1])->andWhere(['like','spmcode',$request->post('spmcode')])->all();
        $web = Daycount::find()->where(['like','spmcode',$request->post('spmcode')])->orderBy('ip')->all();
        $ptime = 0;
        foreach($webs as $web)
        {
            $startTime = strtotime($web->time);
            $end = Daycount::find()->where(['ip'=>$web->ip,'type'=>2])->andWhere(['>','time',$web->time])->orderBy('time')->one();
            if(empty($end))
            {
                return;
            }
            $endTime = strtotime($end->time);
            $ptime += $endTime-$startTime;
        }
        return $ptime;


    }
//    网页的访问过程
    private function pageVisit()
    {
        $request = Yii::$app->request;
        $webs = Daycount::find()->where(['spmcode'=>$request->post('spmcode'),'type'=>1])->all();
        $ptime = 0;
        foreach($webs as $web)
        {
            $startTime = strtotime($web->time);
            $end = Daycount::find()->where(['ip'=>$web->ip,'type'=>2])->andWhere(['>','time',$web->time])->orderBy('time')->one();
            if(empty($end))
            {
                return;
            }
            $endTime = strtotime($end->time);
            $ptime += $endTime-$startTime;
        }
        return $ptime;
    }

    public function actionTest()
    {
    }
}