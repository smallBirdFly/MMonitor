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

    // 首页
    public function actionIndex()
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        if(empty($remoteIp))
        {
            $remoteIp = Yii::$app->request->getUserIP();
        }
        $con = array();
        $con['ip'] = $remoteIp;
        $con['time'] = time();
        $con['url'] = Yii::$app->request->getPathInfo();
        //将数据格式化
        $result = json_encode($con);
        //将数据存入redis
        $redis = Yii::$app->redis;
        $redis->lpush('count_msg',"$result");
    }

//    验证ip
    public function actionCheck()
    {
        $redis = new Redis();
        $redis->connect('127.0.0.1',6379);
        $remoteIp = Yii::$app->request->headers->get('X-Real-IP');
        if(empty($remoteIp))
        {
            $remoteIp = Yii::$app->request->getUserIP();
        }
        $redis->lPush($remoteIp,$remoteIp);
        $redis->expire($remoteIp,60);

        //同一个ip地址一分钟之内访问超过60次会被当作脚本，不予接受该数据
        if($redis->lLen($remoteIp) < 60)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

//    把redis数据存入数据库
    public function actionSave()
    {
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $arr = $redis->lRange('count_msg',-99,-1);
        foreach($arr as $k=>$v)
        {
            $attr = json_decode($v);
            $attr->time = time();
            $rows[] = $attr;
        }
        Yii::$app->db->createCommand()->batchInsert(Scount::tableName(),['spm_code','tag','type','content','created_at'],$rows)->execute();
        $redis->lTrim('count_msg',0,-100);
    }

//    统计当前数据的结果，并将数据导出成sql语句
    public function actionExport()
    {
//        获得所有的spm_code
        $arr = Webs::find()->all();
        foreach($arr as $v)
        {
            $e_count = Scount::find()->where(['spm_code' => $v->spm_code,'tag'=>$v->tag,'type'=> 0])->count();
            $v_count = Scount::find()->where(['spm_code' => $v->spm_code,'tag'=>$v->tag,'type'=> 1])->count();
//                echo ' smp_code:'.$i->spm_code.' tag:'.$i->tag.' num:'.$count."<br>";
            $num = new Daycount();
            $num->spm_code = $v->spm_code;
            $num->tag = $v->tag;
            $num->e_num = $e_count;
            $num->v_num = $v_count;
            $num->save();
        }
        $content = Scount::find()->all();
        $string = null;
        foreach($content as $v)
        {
            $string .= '(\''.$v->spm_code.'\',';
            $string .= '\''.$v->tag.'\',';
            $string .= '\''.$v->type.'\',';
            $string .= '\''.$v->content.'\',';
            $string .= '\''.$v->created_at.'\',';
        }
        $tname = 'scount'.time();
        $ctable = 'CREATE TABLE `'.$tname.'` (
                `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `spm_code` varchar(25) NOT NULL,
                  `tag` tinyint(4) NOT NULL COMMENT \'页面上的模块\',
                  `type` tinyint(4) NOT NULL COMMENT \'返回的类型，0表示错误，1表示访问\',
                  `content` smallint(6) NOT NULL COMMENT \'返回的内容，访问则是次数，错误则是错误信息\',
                  `created_at` int(11) NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;INSERT INTO `'.$tname.'` ( `spm_code`, `tag`, `type`, `content`, `created_at`) VALUES';
        $sql =  $ctable.trim($string,',');
        $sqlfile = fopen('sql/'.$tname.'.sql', "w") or die("Unable to open file!");
        fwrite($sqlfile,$sql);
        fclose($sqlfile);
        Scount::deleteAll();
    }

    public function actionTest()
    {
        ignore_user_abort();//关闭浏览器仍然执行
        set_time_limit(0);//让程序一直执行下去
        $interval=86400;//每隔一定时间运行
        do{
            $redis = Yii::$app->redis;
            $redis->lpush('count_msg',time());
            sleep($interval);//等待时间，进行下一次操作。
        }while(true);
    }

    //统一错误页面
    public function actionError()
    {
        return $this->render('error');
    }
}
