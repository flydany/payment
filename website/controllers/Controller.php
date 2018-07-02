<?php

/**
 * this base this was create for common this property
 * & return json data
 */
 
namespace website\controllers;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

class Controller extends \yii\web\Controller {
    
    // page submit type / just for return page type render / json
    public $isAjax = false;
    // page status
    public $isAlert = false;
    public $status = ['code' => SuccessCode, 'message' => ''];
    // 登录白名单
    public $whiteList = [];

    // exit with json data
    // param 支持参数类型 ↓
    // code.string message.string data.array
    // data.array (success)
    // data.array['code', 'message', 'data'] (error)
    public function json($code = SuccessCode, $message = '', $data = [])
    {
        $this->response->format = \yii\web\Response::FORMAT_JSON;

        // code 默认 成功 / 200
        $code || $code = SuccessCode;
        // code 为数组时， 包含 code, message
        if(is_array($code)) {
            if(isset($code['code'])) {
                $this->setStatus($code['code'], isset($code['message']) ? $code['message'] : '');
                unset($code['code']);
                unset($code['message']);
                if( ! empty($code)) {
                    $data = array_merge($data, $code);
                }
            }
            // 不包含 code 且 data 不存在时，表示 data = code
            else if(empty($data)) {
                $data = $code;
            }
        }
        else {
            $this->setStatus($code, $message);
        }
        if($data) {
            ! isset($data['code']) && $data['code'] = $this->status['code'];
            ! isset($data['message']) && $data['message'] = $this->status['message'];
            return $data;
        }
        else {
            return $this->status;
        }
    }
    // @name 错误异常提示页面 / 跳转
    // @return string error html
    public function error($message, $skip = '')
    {
        // @rule 如果message是数组格式，解析为message, skip
        if(is_array($message)) {
            $skip = $message['skip'];
            $message = $message['message'];
        }
        if(is_array($skip)) {
            foreach($skip as $k => & $web) {
                if( ! isset($web['url'])) {
                    unset($skip[$k]);
                    continue;
                }
                $web['url'] = Url::to('@web/'.$web['url']);
                if( ! isset($web['title'])) {
                    $web['title'] = '点击跳转';
                }
            }
        }
        else {
            $skip = $skip ? Url::to('@web/'.$skip) : false;
        }
        return $this->render('/layouts/error', ['message' => $message, 'skip' => $skip]);
    }
    // @name 操作成功提示页面 / 跳转
    // @return string success html
    public function success($message, $skip = '')
    {
        // @rule 如果message是数组格式，解析为message, skip
        if(is_array($message)) {
            $skip = $message['skip'];
            $message = $message['message'];
        }
        if(is_array($skip)) {
            foreach($skip as $k => & $web) {
                if( ! isset($web['url'])) {
                    unset($skip[$k]);
                    continue;
                }
                $web['url'] = Url::to('@web/'.$web['url']);
                if( ! isset($web['title'])) {
                    $web['title'] = '点击跳转';
                }
            }
        }
        else {
            $skip = $skip ? Url::to('@web/'.$skip) : false;
        }
        return $this->render('/layouts/success', ['message' => $message, 'skip' => $skip]);
    }
    // @name 错误异常跳出 后台界面
    // @return string skip html
    public function skip($message, $skip = '')
    {
        // @rule 如果message是数组格式，解析为message, skip
        if(is_array($message)) {
            $skip = $message['skip'];
            $message = $message['message'];
        }
        if(is_array($skip)) {
            foreach($skip as $k => & $web) {
                if( ! isset($web['url'])) {
                    unset($skip[$k]);
                    continue;
                }
                $web['url'] = Url::to('@web/'.$web['url']);
                if( ! isset($web['title'])) {
                    $web['title'] = '点击跳转';
                }
            }
        }
        else {
            $skip = $skip ? Url::to('@web/'.$skip) : false;
        }
        return $this->render('/layouts/skip', ['message' => $message, 'skip' => $skip]);
    }

    // set json status
    public function setStatus($code, $message = '')
    {
        if(is_array($code)) {
            $message = $code['message'];
            $code = $code['code'];
        }
        $this->status = ['code' => $code, 'message' => $message];
    }
    
    // set json status
    public function setAlert($code, $message = '')
    {
        if(is_array($code)) {
            $message = $code['message'];
            $code = $code['code'];
        }
        $this->isAlert = true;
        $this->setStatus($code, $message);
    }
    // @name 获取model错误描述
    // @return string
    public function buildModelError($model)
    {
        return implode('。', ArrayHelper::getColumn($model->getErrors(), '0')).'（Param Error）';
    }

    // get response
    public function getResponse()
    {
        return Yii::$app->getResponse();
    }

    // get request
    public function getRequest()
    {
        return Yii::$app->getRequest();
    }

    // get admin
    public function getParams()
    {
        return Yii::$app->params;
    }

    // get session
    public function getSession()
    {
        return Yii::$app->getSession();
    }
    
    // get redis
    public function getRedis()
    {
        return Yii::$app->redis;
    }
    
    // get application
    public function getApp()
    {
        return Yii::$app;
    }

    // get user
    public function getUser()
    {
        return Yii::$app->getUser();
    }

    // @name is admin logined
    // @return boolean
    public function isLogin()
    {
        return Yii::$app->isLogin();
    }

    // 打印数组
    public function v($data)
    {
        echo '<pre>'; print_r($data); echo '</pre>';
    }
}
