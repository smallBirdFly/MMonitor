<?php

namespace statistics\controllers;


use common\components\MMLogger;
use common\utils\HttpResponseUtil;
use statistics\component\AuthFilter;

use statistics\models\Count;
use statistics\models\Month;
use statistics\models\Page;
use statistics\models\Scount;

use statistics\models\Today;
use statistics\models\Type;
use statistics\models\Week;
use statistics\models\Yesterday;

use Yii;
use yii\db\ActiveRecord;
use yii\web\Controller;


class SiteController extends Controller
{
    public $layout = 'blank';

    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'auth' => [
                'class' => AuthFilter::className(),
                'except' => ['login'],
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
    private function check($remoteIp)
    {
        $redis = Yii::$app->redis;
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

    //验证是否今天第一次访问
    private function visit($remoteIp)
    {
        $redis = Yii::$app->redis;
        var_dump($redis->llen($remoteIp.'visit'));
        if($redis->llen($remoteIp.'visit') > 0)
        {
            return 0;
        }
        else
        {
            $redis->lpush($remoteIp.'visit',$remoteIp);
            $redis->expireat($remoteIp.'visit',strtotime(date("Y-m-d"))+86400);
//        $redis->expireat($remoteIp.'visit',time()+10);
            return 1;
        }
    }

    public function actionIndex()
    {
       return $this->render('index');
    }

    //存到redis缓存中
    public function actionSaveredis()
    {
        $request = Yii::$app->request;
        $logger = MMLogger::getLogger(__FUNCTION__);

        $remoteIp = $request->headers->get('X-Real-IP');
        if(empty($remoteIp))
        {
            $remoteIp = $request->getUserIP();
        }
        $message['ip'] = $remoteIp;
        $message['time'] = date('Y-m-d H:i:s');
        $message['referrer'] = $request->getReferrer();
        //是否为脚本
        if(!$this->check($remoteIp))
        {
            $logger->info('IP:'.$message['ip'].'    time:'.$message['time'].'   url:'.$message['url'].'        message:'.'访问过于频繁');
            $result['code'] = 400;
            HttpResponseUtil::setJsonResponse($result);
            return;
        }

        //没有获取到code的数据无效，丢弃
        if(empty($request->get('code')))
        {
            $logger->info('IP:'.$message['ip'].'    time:'.$message['time'].'   url:'.$message['referrer'].'        message:'.'没有找到code');
            $result['code'] = 403;
            HttpResponseUtil::setJsonResponse($result);
            return;
        }
//      code的最后一位为type值
        $code = explode('.',$request->get('code'));
        $type = $code[count($code)-1];

        //判断type的值,如果是正常的情况
        if($type == 1 || $type == 2)
        {
            //需要的数据
        }
        else if($type == 0 || $type == -1)
        {
            $messages = $request->get('msg');
            if(!is_array($request->get('msg')))
            {
                $logger->info('IP:'.$message['ip'].'    time:'.$message['time'].'   url:'.$message['referrer'].'        message:'.'不正确的type值');
                $result['code'] = 405;
                HttpResponseUtil::setJsonResponse($result);
                return;
            }
            foreach($messages as $mess)
            {
                //获取消息
                $message['message'][] = $mess;
            }
        }
        else
        {
            $logger->info('IP:'.$message['ip'].'    time:'.$message['time'].'   url:'.$message['referrer'].'        message:'.'不正确的type值');
            $result['code'] = 405;
            HttpResponseUtil::setJsonResponse($result);
            return;
        }

        //是否是今天第一次访问
        $con['visit'] = $this->visit($remoteIp);
        $con['type'] = $type;
        $con['appkey'] = $code[0];
        $con['page'] = $code[1];
        $con['content'] = $message;
        //将数据格式化
        $result = json_encode($con);
        //将数据存入redis
        $redis = Yii::$app->redis;
        $redis->lpush('msg',$result);
        $result1['code'] = 200;
        HttpResponseUtil::setJsonResponse($result1);
    }


//    把redis数据存入数据库
    public function actionSavedisk()
    {
        $logger = MMLogger::getLogger(__FUNCTION__);
        //每次存入数据库的数据的条数
        $num = Yii::$app->params['num'];
        $redis = Yii::$app->redis;
        //获取当前redis中的数据条数
        $len  = $redis->llen("msg");
        if($len == 0)
        {
            $logger->warn('     time:'.date('Y-m-d H:i:s').'        当前redis未存在数据');
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
                $code = explode('.',$attr->code);
                $res['appkey'] = $code[0];
                $res['page'] = $code[1];
                $res['type'] = $code[count($code)-1];
                $res['ip'] = $attr->content->ip;
                $res['visit'] = $this->visit( $res['ip']);
                $res['time'] = $attr->content->time;
                $res['referrer'] = $attr->content->referrer;
                if(is_null($attr->content->referrer))
                {
                    $res['referrer'] = '';
                }
                if(!empty($attr->content->message))
                {
                    $res['message'] = json_encode($attr->content->message);
                }
                else
                {
                    $res['message'] = '';
                }
                $res['created_at'] = date('Y-m-d H:i:s',time());
                $rows[] = $res;
            }
            $db = Yii::$app->db->createCommand()->batchInsert(Today::tableName(),['appkey','page','type','visit','ip','time','referrer','message','created_at'],$rows)->execute();
            $db = Yii::$app->db->createCommand()->batchInsert(Week::tableName(),['appkey','page','type','visit','ip','time','referrer','message','created_at'],$rows)->execute();
            $db = Yii::$app->db->createCommand()->batchInsert(Month::tableName(),['appkey','page','type','visit','ip','time','referrer','message','created_at'],$rows)->execute();
            if(!$db)
            {
                $logger->info('     time:'.date('Y-m-d H:i:s').'     数据插入数据库失败，');
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
        //处理30天前的数据
        $date = date('Y-m-d',time()-86400*30);
        $string = null;
        $month_contents = Month::find()->where(['like','time',$date])->all();
        foreach($month_contents as $v)
        {
            $string .= '(\''.$v->appkey.'\',';
            $string .= '\''.$v->page.'\',';
            $string .= '\''.$v->type.'\',';
            $string .= '\''.$v->ip.'\',';
            $string .= '\''.$v->time.'\',';
            $string .= '\''.$v->referrer.'\',';
            $string .= '\''.$v->message.'\',';
            $string .= '\''.$v->visit.'\',';
            $string .= '\''.$v->created_at.'\'),';
        }
        $tname = $date.'_count_'.time();
        $ctable = 'CREATE TABLE `'.$tname.'` (
                  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                  `appkey` char(9) NOT NULL,
                  `page` int(11) NOT NULL,
                  `type` char(3) NOT NULL,
                  `ip` char(15) NOT NULL COMMENT \'返回的内容\',
                  `time` char(20) NOT NULL,
                  `referrer` varchar(100) NOT NULL,
                  `message` varchar(100) NOT NULL,
                  `visit` char(1) NOT NULL,
                  `created_at` datetime NOT NULL,
                  PRIMARY KEY (`id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;INSERT INTO `'.$tname.'` ( `appkey`,`page`,`type`, `ip`,`time`,`referrer`,`message`,`visit`,`created_at`) VALUES';
        $sql =  $ctable.trim($string,',');
        $sqlfile = fopen(Yii::$app->basePath."/web/sql/".$tname.'.sql', "w") or die("Unable to open file!");
        fwrite($sqlfile,$sql);
        fclose($sqlfile);

        $transaction = ActiveRecord::getDb()->beginTransaction();
        try
        {
            //前30天的数据处理
            $appkeys = Page::find()->all();
            $types = Type::find()->all();
            foreach($appkeys as $appkey)
            {
                foreach($types as $type)
                {
                    for($i = 0; $i < 24; $i++)
                    {
                        $sql =  Month::find()->where(['page' => $appkey['id'],'type'=> $type['type'],'HOUR(time)'=>$i])->andWhere(['like','time',$date]);
                        //每天各个小时的访问量
                        $pv =  $sql->count();
                        $arrPv[] = array(
                            'appkey' => $appkey['appkey'],
                            'page' => $appkey['id'],
                            'type' => $type['type'],
                            'date' => date('Y-m-d H:m:i',strtotime($date) + 3600 * $i),
                            'visit' => 0,
                            'num' => $pv
                        );
                        //每天各个小时的独立访问量
                        $ip =  $sql->andWhere(['visit'=>1])->count();
                        $arrIp[] = array(
                            'appkey' => $appkey['appkey'],
                            'page' => $appkey['id'],
                            'type' => $type['type'],
                            'date' => date('Y-m-d H:m:i',strtotime($date) + 3600 * $i),
                            'visit' => 1,
                            'num' => $ip
                        );
                    }
                }
            }
            Yii::$app->db->createCommand()->batchInsert(Count::tableName(),['appkey','page','type','time','visit','num'],$arrIp)->execute();
            Yii::$app->db->createCommand()->batchInsert(Count::tableName(),['appkey','page','type','time','visit','num'],$arrPv)->execute();
            Month::deleteAll(['like','time',$date]);

            //第7天的数据删除
            $week_date = date('Y-m-d',time()-86400*7);
           /* $week_contents = Week::find()->where(['like','time',$week_date])->select(['appkey','page','type','ip','time','referrer','message','visit','created_at'])->asArray()->all();
            $db = Yii::$app->db->createCommand()->batchInsert(Month::tableName(),['appkey','page','type','ip','time','referrer','message','visit','created_at'],$week_contents)->execute();*/
            Week::deleteAll(['like','time',$week_date]);

            //今天数据到昨天数据中
            $yesterday_date = date('Y-m-d',time()-86400);
            Yesterday::deleteAll();
            $yesterday_contents = Today::find()->where(['like','time',$yesterday_date])->select(['appkey','page','type','ip','time','referrer','message','visit','created_at'])->asArray()->all();
            $db = Yii::$app->db->createCommand()->batchInsert(Yesterday::tableName(),['appkey','page','type','ip','time','referrer','message','visit','created_at'],$yesterday_contents)->execute();
            Today::deleteAll(['like','time',$yesterday_date]);
            $transaction->commit();
        }
        catch(\yii\base\Exception $e)
        {
            $transaction->rollBack();
            $result['code'] = 201;
            $logger->warn('数据库事务处理操作失败');
            return;
        }

        $result['code'] = 200;
        HttpResponseUtil::setJsonResponse($result);
    }

    public function actionTest()
    {
        $s = Yii::$app->request->post('type');
        echo $s;
    }



    //统一错误页面
    public function actionError()
    {

    }
}
