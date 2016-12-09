<?php

namespace statistics\controllers;

use common\components\HttpUtils;
use common\components\Constant;

use common\components\MMLogger;
use common\utils\HttpResponseUtil;
use statistics\component\DaemonCommand;
use Redis;
use statistics\models\Daycount;
use statistics\models\Scount;
use statistics\models\Webs;
use Yii;
use yii\base\ErrorException;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\helpers\Url;
use yii\redis\Connection;
use yii\web\NotFoundHttpException;

class SiteController extends Controller
{
    public $layout = 'blank';

    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

//    验证ip
    private function check()
    {
        $redis = Yii::$app->redis;
        $remoteIp = Yii::$app->request->headers->get('X-Real-IP');
        if(empty($remoteIp))
        {
            $remoteIp = Yii::$app->request->getUserIP();
        }
        $redis->lpush($remoteIp,$remoteIp);
        $redis->expire($remoteIp,60);

        //同一个ip地址一分钟之内访问超过60次会被当作脚本，不予接受该数据
        if($redis->llen($remoteIp) < 60)
        {
            return true;
        }
        else
        {
//            将该数据丢弃
            return false;
        }
    }

    public function actionIndex()
    {
    }

    //存到redis缓存中
    public function actionSaveredis()
    {
        $request = Yii::$app->request;
        Yii::error($request->get());
//        var_dump($request->get());
//        die;
        $logger = MMLogger::getLogger(__FUNCTION__);
        $remoteIp = $request->headers->get('X-Real-IP');
        if(empty($remoteIp))
        {
            $remoteIp = $request->getUserIP();
        }
        Yii::error($remoteIp);
        $message['ip'] = $remoteIp;
        $message['time'] = time();
        $message['url'] = $request->getReferrer();
        Yii::error($message['url']);

        //是否为脚本
        if(!$this->check())
        {
            $logger->info('IP:'.$message['ip'].'    time:'.$message['time'].'   url:'.$message['url'].'        message:'.'访问过于频繁');
            $result['code'] = 400;
            HttpResponseUtil::setJsonResponse($result);
            return;
        }
        //如果传过来为空
        if(empty($request->get('content')))
        {
            $logger->info('IP:'.$message['ip'].'    time:'.$message['time'].'   url:'.$message['url'].'        message:'.'content没有接受到数据');
            $result['code'] = 401;
            HttpResponseUtil::setJsonResponse($result);
            return;
        }

        try
        {
            $contents = json_decode($request->get('content'))->content;
        }
        catch(ErrorException $e)
        {
            $logger->info('IP:'.$message['ip'].'    time:'.$message['time'].'   url:'.$message['url'].'        message:'.'content内容不符合要求');
            $result['code'] = 401;
            HttpResponseUtil::setJsonResponse($result);
            return;
        }

        //循环内容
        foreach($contents as $content)
        {
            if(empty($remoteIp))
            {
                $remoteIp = $request->getUserIP();
            }
            $message['ip'] = $remoteIp;
            $message['time'] = time();
            $message['url'] = $request->getReferrer();
            //没有获取到spmcode的数据无效，丢弃
            if(empty($content->spm))
            {
                $logger->info('IP:'.$message['ip'].'    time:'.$message['time'].'   url:'.$message['url'].'        message:'.'没有找到spmcode');
                $result['code'] = 403;
                HttpResponseUtil::setJsonResponse($result);
                continue;
            }

            $con['spmcode'] = $content->spm;
            Yii::error($content->spm);
//            somcode的最后一位为type值
            $spmcode = explode('.',$content->spm);
            $type = $spmcode[count($spmcode)-1];

            //判断type的值,如果是正常的情况
            if($type == 1 || $type == 2)
            {
                //需要的数据
            }
            else if($type == 0)
            {
                $messages = $content->con;
                foreach($messages as $mess)
                {
                    //获取消息
                    $message['message'][] = $mess;
                }
            }
            else if($type == -1)
            {
                $messages = $content->con;
                foreach($messages as $mess)
                {
                    //获取消息
                    $message['message'][] = $mess;
                }
            }
            else
            {
                $logger->info('IP:'.$message['ip'].'    time:'.$message['time'].'   url:'.$message['url'].'        message:'.'不正确的type值');
                $result['code'] = 405;
                HttpResponseUtil::setJsonResponse($result);
                continue;
            }

            $con['content'] = $message;
            //将数据格式化
            $result = json_encode($con);
            //将数据存入redis
            $redis = Yii::$app->redis;
            $redis->lpush('msg',$result);
            $result1['code'] = 200;
            HttpResponseUtil::setJsonResponse($result1);
        }
    }

//    把redis数据存入数据库
    public function actionSavedisk()
    {
        $logger = MMLogger::getLogger(__FUNCTION__);
        //每次存入数据库的数据的条数
        $num = Yii::$app->params['num'];
        $redis = Yii::$app->redis;
        //获取当前redis中的数据条数
        Yii::error('调用的savedb接口');
        $len  = $redis->llen("msg");
        if($len == 0)
        {
            $logger->warn('time:'.date('Y-m-d H:i:s').'当前redis未存在数据');
            $result1['code'] = 201;
            HttpResponseUtil::setJsonResponse($result1);
            return;
        }

        //计算需要循环的次数
        $count = ceil($len/$num);
        for($i = 0; $i < $count; $i++)
        {
            $arr = $redis->lrange('msg',-$num,-1);
            $len  = $redis->llen("msg");
            foreach($arr as $v)
            {
                $attr = json_decode($v);
                $res['spmcode'] = $attr->spmcode;
                $res['ip'] = $attr->content->ip;
                $res['time'] = $attr->content->time;
                $res['url'] = $attr->content->url;
                if(!empty($attr->content->message))
                {
                    $res['message'] = json_encode($attr->content->message);
                }
                else
                {
                    $res['message'] = '';
                }
                $res['created_at'] = date('Y-m-d H:m:s');
                $rows[] = $res;
            }
            $db = Yii::$app->db->createCommand()->batchInsert(Scount::tableName(),['spmcode','ip','time','url','message','created_at'],$rows)->execute();
            if(!$db)
            {
               /* $result1['code'] = 402;
                $result1['data']['content'] = "数据插入数据库失败";
                HttpResponseUtil::setJsonResponse($result1);*/
                $logger->info('time:'.date('Y-m-d H:i:s').'数据插入数据库失败');
                $result1['code'] = 402;
                HttpResponseUtil::setJsonResponse($result1);
                return;
            }
            //将存储了的数据清空，否则下次循环会重复添加
            $rows = array();
            //如果当前长度大于要出栈的数量
            if($len > $num)
            {
                $redis->ltrim('msg',0,$len-$num-1);
//                Yii::error('出栈1');
            }
            else
            {
                $redis->del('msg');
            }
        }
        $result1['code'] = 200;
        HttpResponseUtil::setJsonResponse($result1);
    }

//  统计当前数据的结果，并将数据导出成sql语句
    public function actionExport()
    {
        $logger = MMLogger::getLogger(__FUNCTION__);
//        对当天数据进行统计
        $categorys = Scount::find()->groupBy(['spmcode','ip','url','time'])->all();
        if(empty($categorys))
        {
            $result['code'] = 201;
            $logger->warn('time:'.date('Y-m-d H:i:s').'当前数据表中未存在数据');
            HttpResponseUtil::setJsonResponse($result);
            return;
        }
        foreach($categorys as $category)
        {
            $num = Scount::find()->where(['spmcode'=> $category->spmcode,'ip'=>$category->ip,'url'=>$category->url,'time'=>$category->time])->count();
            $count = new Daycount();
            $count->spmcode = $category->spmcode;
            $count->ip = $category->ip;
            $count->url = $category->url;
            $count->time = $category->time;
            $count->num = $num;
            $count->save();
        }
        $content = Scount::find()->all();
        $string = null;
        foreach($content as $v)
        {
            $string .= '(\''.$v->spmcode.'\',';
            $string .= '\''.$v->ip.'\',';
            $string .= '\''.$v->time.'\',';
            $string .= '\''.$v->url.'\',';
            $string .= '\''.$v->message.'\',';
            $string .= '\''.$v->created_at.'\'),';
        }
        $tname = 'scount'.time();
        $ctable = 'CREATE TABLE `'.$tname.'` (
                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `spmcode` varchar(40) NOT NULL,
                  `ip` char(15) NOT NULL COMMENT \'返回的内容\',
                  `time` char(20) NOT NULL,
                  `url` varchar(100) NOT NULL,
                  `message` varchar(100) NOT NULL,
                  `created_at` char(20) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;INSERT INTO `'.$tname.'` ( `spmcode`, `ip`,`time`,`url`,  `message`,`created_at`) VALUES';
        $sql =  $ctable.trim($string,',');
        $sqlfile = fopen(Yii::$app->basePath."/web/sql/".$tname.'.sql', "w") or die("Unable to open file!");
        fwrite($sqlfile,$sql);
        fclose($sqlfile);
        Scount::deleteAll();
        $result['code'] = 200;
        HttpResponseUtil::setJsonResponse($result);
    }

    public function actionTest()
    {
        $arr['content'][]  = array(
            'spm' => '123.12.0',
            'con' => array(
                'err' => 'err1',
                'err' => 'err2',
            )

        );
        $arr['content'][]  = array(
            'spm' => '123.12.-1',
            'con' => array(
                'warm' => 'warm1',
                'warm' => 'warm2',
            )

        );
        echo json_encode($arr);
    }

    public function actionAnalysis()
    {
        $request = Yii::$app->request;

        $query = Daycount::find();
        //获得4个条件
        if($request->post('spmcode'))
        {
            $query = $query->where(['spmcode'=>$request->post('spmcode')]);
        }

        if($request->post('ip'))
        {
//            $ip = "'ip'=>'".Yii::$app->request->post('ip')."'";
            $query = $query->where(['ip'=>$request->post('ip')]);
        }

        if($request->post('url'))
        {
            $query = $query->where(['url' => $request->post('url')]);
        }

        if($request->post('time') == 'day')
        {
            $starttime = time()-60*60*24;
            $query->where(['>=','time',$starttime]);
        }
        if($request->post('time') == 'week')
        {
            $starttime = time()-7*60*60*24;
            $query->where(['>=','time',$starttime]);
        }
        if($request->post('time') == 'month')
        {
            $starttime = time()-7*30*60*60*24;
            $query->where(['>=','time',$starttime]);
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

    }

    //统一错误页面
    public function actionError()
    {
    }
}
