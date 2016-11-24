<?php

namespace statistics\controllers;

use common\components\HttpUtils;
use common\components\Constant;

use common\utils\HttpResponseUtil;
use statistics\component\DaemonCommand;
use Redis;
use statistics\models\Daycount;
use statistics\models\Scount;
use statistics\models\Webs;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\helpers\Url;
use yii\redis\Connection;

class SiteController extends Controller
{
    public $layout = 'blank';
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
            return false;
        }
    }

    public function actionIndex()
    {
        ignore_user_abort();//关闭浏览器仍然执行
        set_time_limit(0);//让程序一直执行下去
        $interval=5*60;//每隔一定时间运行
        do{
            sleep($interval);
        }while(true);
    }

    //存到redis缓存中
    public function actionSaveredis()
    {
        //是否为脚本
        if(!$this->check())
        {
            return false;
        }
        $contents = json_decode(Yii::$app->request->get('content'))->content;
        foreach($contents as $content)
        {
            //没有获取到spmcode的数据无效，丢弃
            if(empty($content->spm))
            {
                break;
            }

            $spmcode = explode('.',$content->spm);
            $type = $spmcode[count($spmcode)-1];
            $con['spmcode'] = $spmcode;
            //判断type的值,如果是正常的情况
            if($type == 1 || $type == 2)
            {
                //        获取ip地址
                if(empty($remoteIp))
                {
                    $remoteIp = Yii::$app->request->getUserIP();
                }
                //传递的数据
                $message['ip'] = $remoteIp;
                $message['time'] = time();
                $message['url'] = Url::to(['/']).Yii::$app->request->getPathInfo();
            }
            else if($type = 0)
            {
                if(empty($remoteIp))
                {
                    $remoteIp = Yii::$app->request->getUserIP();
                }
                $message['ip'] = $remoteIp;
                $message['time'] = time();
                $message['url'] = Url::to(['/']).Yii::$app->request->getPathInfo();
                //出现错误警告的情况
                $messages = $content->con;
                foreach($messages as $message)
                {
                    //获取消息
                    $message['message'][] = $message->err;
                }
            }
            else if($type == -1)
            {
                if(empty($remoteIp))
                {
                    $remoteIp = Yii::$app->request->getUserIP();
                }
                $message['ip'] = $remoteIp;
                $message['time'] = time();
                $message['url'] = Url::to(['/']).Yii::$app->request->getPathInfo();
                $messages = $content->con;
                foreach($messages as $message)
                {
                    //获取消息
                    $message['message'][] = $message->warm;
                }
            }
            $con['content'] = $message;
            //将数据格式化
            $result = json_encode($con);
            //将数据存入redis
            $redis = Yii::$app->redis;
            $redis->lpush('msg',$result);
        }
    }

//    把redis数据存入数据库
    public function actionSavedb()
    {
        $num = 100;
        $redis = Yii::$app->redis;
        $arr = $redis->lrange('msg',-$num+1,-1);
        foreach($arr as $k=>$v)
        {
            $attr = json_decode($v);
            $attr->time = time();
            $rows[] = $attr;
        }
        Yii::$app->db->createCommand()->batchInsert(Scount::tableName(),['spmcode','content','created_at'],$rows)->execute();
        $redis->ltrim('msg',0,-$num);
    }

//    统计当前数据的结果，并将数据导出成sql语句
    public function actionExport()
    {
//        对当天数据进行统计
        $categorys = Scount::find()->groupBy('spmcode')->all();
        foreach($categorys as $category)
        {
            $num = Scount::find()->where(['spmcode'=> $category->spmcode])->count();
            $count = new Daycount();
            $count->spmcode = $category->spmcode;
            $count->num = $num;
            $count->save();
        }

        $content = Scount::find()->all();
        $string = null;
        foreach($content as $v)
        {
            $string .= '(\''.$v->spmcode.'\',';
            $string .= '\''.$v->content.'\',';
            $string .= '\''.$v->created_at.'\'),';
        }
        $tname = 'scount'.time();
        $ctable = 'CREATE TABLE `'.$tname.'` (
                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `spmcode` varchar(40) NOT NULL,
                  `content` varchar(100) NOT NULL COMMENT \'返回的内容\',
                  `created_at` int(11) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;INSERT INTO `'.$tname.'` ( `spmcode`, `content`, `created_at`) VALUES';
        $sql =  $ctable.trim($string,',');
        $sqlfile = fopen('sql/'.$tname.'.sql', "w") or die("Unable to open file!");
        fwrite($sqlfile,$sql);
        fclose($sqlfile);
        Scount::deleteAll();
    }

    public function actionTest()
    {
        /*ignore_user_abort();//关闭浏览器仍然执行
        set_time_limit(0);//让程序一直执行下去
        $interval=86400;//每隔一定时间运行
        do{
            $redis = Yii::$app->redis;
            $redis->lpush('count_msg',time());
            sleep($interval);//等待时间，进行下一次操作。
        }while(true);*/
        $arr['content'][]  = array(
            'spm' => 123,
            'con' => array(
                'err' => 'err1',
                'err1' => 'err2',
            )

        );
        $arr['content'][]  = array(
            'spm' => 123,
            'con' => array(
                'err' => 'err1',
                'err1' => 'err2',
            )

        );
        echo json_encode($arr);
    }

    //统一错误页面
    public function actionError()
    {
        return $this->render('error');
    }
}
