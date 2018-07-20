<?php

/**
 * this base this was create for common this property
 * & return json data
 */
 
namespace admin\controllers;

use Yii;
use yii\base\Model;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

class Controller extends \yii\web\Controller {

    public $layout = 'main.php';
    // page status
    public $isAlert = false;
    public $status = ['code' => SuccessCode, 'message' => ''];
    // page controller~action for navigator 
    // permission check
    // parent for wether this controller is 'Class A' navigator
    public $parent = '';
    public $navigator = [];
    // 登录白名单
    public $whiteList = [];
    // public $layout = 'simple.php';
    
    /**
     * @inheritdoc
     * all actions will be check login status except 'login' & whiteList
     * check login hook
     */
    public function behaviors()
    {
        return [
             // @name 后台管理员权限校验hook
             // @return Yii::$app->admin = login admin, you can use as $this->admin/$this->getAdmin()
             'access' => [
                'class' => \admin\behaviors\Permission::className(),
            ],
        ];
    }

    // exit with json data
    // 支持方式
    // code.string message.string data.array
    // data.array (success)
    // data.array['code', 'message', 'data'] (error)
    public function json($code = SuccessCode, $message = '', $data = [])
    {
        // @rule 设置页面处理返回数据格式
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
        // code、message 为字符串时
        else {
            $this->setStatus($code, $message);
        }
        // @rule 如果 $data 存在，push code, message 进 $data 数组
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
                $web['url'] = strpos($web['url'], 'javascript') == 0 ? $skip : Url::to('@web/'.$web['url']);
                if( ! isset($web['title'])) {
                    $web['title'] = 'jump?';
                }
            }
        }
        else {
            $skip = $skip ? (strpos($skip, 'javascript') == 0 ? $skip : Url::to('@web/'.$skip)) : false;
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
                $web['url'] = strpos($web['url'], 'javascript') == 0 ? $skip : Url::to('@web/'.$web['url']);
                if( ! isset($web['title'])) {
                    $web['title'] = 'jump?';
                }
            }
        }
        else {
            $skip = $skip ? (strpos($skip, 'javascript') == 0 ? $skip : Url::to('@web/'.$skip)) : false;
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
                    $web['title'] = 'jump?';
                }
            }
        }
        else {
            $skip = $skip ? Url::to('@web/'.$skip) : false;
        }
        return $this->redirect([Url::to('@web/welcome/skip'), 'message' => $message, 'skip' => $skip]);
    }
    
    // @name 设置当前选中的一级、二级菜单
    // @describe 可用于验证当前方法的权限
    // @param $this->navigator array 当前导航系统组合
    // @return object $this
    public function setNavigator($controller)
    {
        $this->navigator['controller'] = $controller;
        $this->navigator['action'] = $this->id.'/'.$this->action->id;
        return $this;
    }

    // @name set json status
    // @param $code string/array 状态码
    // @param $message string 状态描述
    // @return object $this
    public function setStatus($code, $message = '')
    {
        // @rule 如果code是数组格式，解析为code, message
        if(is_array($code)) {
            $message = $code['message'];
            $code = $code['code'];
        }
        // @rule 设置状态码、状态描述
        $this->status = ['code' => $code, 'message' => $message];
        return $this;
    }
    
    // set json status
    // @param $code string/array 状态码
    // @param $message string 状态描述
    // @return object $this
    public function setAlert($code, $message = '')
    {
        if(is_array($code)) {
            $message = $code['message'];
            $code = $code['code'];
        }
        // @rule 设置是否弹窗 -> true
        $this->isAlert = true;
        // @rule 设置弹窗状态描述
        $this->setStatus($code, $message);
        return $this;
    }
    
    // @name 权限验证
    // @param $controller string 控制器
    // @param $action string 方法
    // @param $admin_id 管理员编号
    // @return boolean
    public function checkPermission($controller = null, $action = null, $admin_id = null)
    {
        // @rule 为空设置当前控制器
        if($controller === null) {
            $controller = $this->id;
        }
        // @rule 为空设置当前方法
        if($action === null) {
            $action = $this->action->id;
        }
        // 校验权限是否存在
        if(\common\models\Admin::checkPermission($controller, $action, $admin_id)) {
            return true;
        }
        return false;
    }

    // @name get response
    // @return object yii response
    public function getResponse()
    {
        return Yii::$app->getResponse();
    }

    // @name get request
    // @return object yii request
    public function getRequest()
    {
        return Yii::$app->getRequest();
    }

    // @name get config params
    // @return object yii params
    public function getParams()
    {
        return Yii::$app->params;
    }

    // @name get session
    // @return object yii session
    public function getSession()
    {
        return Yii::$app->getSession();
    }
    
    // @name get application
    // @return object yii app
    public function getApp()
    {
        return Yii::$app;
    }

    // @name get admin
    // @return object login admin
    public function getAdmin()
    {
        return Yii::$app->getAdmin();
    }

    // @name is admin logined
    // @return boolean
    public function isLogin()
    {
        return Yii::$app->isLogin();
    }

    // @name 打印数组
    public function v($data)
    {
        echo '<pre>'; print_r($data); echo '</pre>';
    }
}
