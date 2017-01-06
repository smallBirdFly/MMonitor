<?php

namespace mmbackend\controllers\vpass;

use backend\models\Loginuser;
use mmbackend\components\VpassUrl;
use mmbackend\components\VPassUtil;
use common\utils\UrlUtil;
use Yii;
use yii\web\Controller;

class VController extends Controller
{
    public $layout = 'blank';

    public function actionSlogin()
    {
        Yii::error('222');
        $session = Yii::$app->session;
        $session->open();
        $request = Yii::$app->request;
        $mmcode = $request->get('mmcode');
        if(isset($mmcode))
        {
            $vclient = VPassUtil::getVPassClient();
            $resp = $vclient->getToken($mmcode);
            Yii::error('resp:');
            Yii::error($resp);
            if(isset($resp))
            {
                $resp = json_decode($resp, true);
                if($resp['code'] == 200)
                {
                    $data = $resp['data'];
                    $session['app'] = "mm_survey";
                    $session['x-access-token'] = $data['x-access-token'];

                    $userInfo = $data['userInfo'];
                    $userName = '';
                    if(!empty($userInfo['userName']))
                    {
                        $userName = $userInfo['userName'];
                    }
                    else if(!empty($userInfo['mobile']))
                    {
                        $userName = $userInfo['mobile'];
                    }
                    Yii::error('session_login');
                    Yii::error($resp);
                    Yii::error($userName);
                    Yii::error('222');
                    $session['u_name'] = $userName;
                    $session['u_id'] = $userInfo['id'];
                    $session['loginType'] = '1';
                    $session['expires_in'] = $userInfo['expireTime'];
                    $user = Loginuser::findOne(['u_id' => $session['u_id']]);
                    if(is_null($user))
                    {
                        $login = new Loginuser();
                        $login->u_id = $session['u_id'];
                        $login->u_name = $session['u_name'];
                        $login->loginType = $session['loginType'];
                        $login->expires_in = $session['expires_in'];
                        $login->save();
                    }
                    echo "<script>window.parent.postMessage(" . Yii::$app->params['vpass']['appkey']." , '*');</script>";
                }
                else
                {
                    Yii::warning($resp['errno'].",",__FUNCTION__);
                }
            }
            else
            {
                Yii::warning("vpass get-token failed.",__FUNCTION__);
            }
        }
        else
        {
            Yii::warning("no mmcode request",__FUNCTION__);
        }
    }

    public function actionSlogout()
    {
        Yii::$app->session->open();
//        unset(Yii::$app->session['u_id']);
//        unset(Yii::$app->session['u_name']);
//        unset(Yii::$app->session['loginType']);
//        unset(Yii::$app->session['expires_in']);
//        unset(Yii::$app->session['x-access-token']);
        Yii::$app->session->destroy();
        Yii::$app->session->close();
        echo "<script>window.parent.postMessage(" . Yii::$app->params['vpass']['appkey']. " , '*');</script>";
    }


    public function actionSloginHttp()
    {
        $session = Yii::$app->session;
        $session->open();
        $request = Yii::$app->request;
        $mmcode = $request->get('mmcode');

        if(isset($mmcode))
        {
            $getTokenUrl = VpassUrl::getTokenCreateUrl().'?mmcode='.$mmcode;
            $urlModel = new UrlUtil();
            $resp = $urlModel->get_contents($getTokenUrl);
//            $vclient = VPassUtil::getVPassClient();
//            $resp = $vclient->getToken($mmcode);

            if(isset($resp))
            {
                if($resp['errno'] == 0)
                {
                    $msg = $resp['msg'];
                    if($msg['code'] == 200)
                    {
                        $data = $resp['data'];
                        $session['app'] = "mm_survey";
                        $session['x-access-token'] = $data['x-access-token'];

                        $userInfo = $data['userInfo'];
                        $userName = '';
                        if(!empty($userInfo['userName']))
                        {
                            $userName = $userInfo['userName'];
                        }
                        else if(!empty($userInfo['mobile']))
                        {
                            $userName = $userInfo['mobile'];
                        }
                        $session['u_name'] = $userName;
                        $session['u_id'] = $userInfo['id'];
                        $session['loginType'] = '1';
                        $session['expires_in'] = $userInfo['expireTime'];
                        echo "<script>window.parent.postMessage(" . Yii::$app->params['vpass']['appkey']. " , '*');</script>";
                    }
                    else
                    {
                        Yii::warning($resp,__FUNCTION__);
                    }
                }
                else
                {
                    Yii::warning($resp['errno'].":",__FUNCTION__);
                }
            }
            else
            {
                Yii::warning("vpass get-token failed.",__FUNCTION__);
            }
        }
        else
        {
            Yii::warning("no mmcode request",__FUNCTION__);
        }
    }
}
