<?php

/**
 * this hook class was create for check user's login status
 * & check user's permission before action called
 * 
 */
 
namespace website\behaviors;

use Yii;
use common\models\User;

/**
 * user permission check
 * 1、login status
 * 2、action permission
 */
class Permission extends \yii\base\ActionFilter {

    // 在action之前运行，可用来过滤输入、过滤权限等
    // 如果返回值为false, 则action不会运行
    public function beforeAction($action)
    {
        // echo '<pre>'; print_r($action); die;
        $controller = & $action->controller;
        // 判断当前 controller 是否需要登录
        // echo $controller->action->id;
        // echo '<pre>'; print_r($action->controller->action); echo '</pre>'; die;
        if(isset($controller->whiteList) && in_array($controller->action->id, $controller->whiteList)) {
            return true;
        }
        // 判断登录态
        if(Yii::$app->isLogin()) {
            return true;
        }
        if(Yii::$app->request->isAjax) {
            // json format数据需要返回数据到 response.data中
            Yii::$app->getResponse()->data = $controller->json('Permission.Denied', '请先登录（Permission Denied）');
        }
        else {
            // html format数据直接返回数据到response.content中
            // $controller->layout = 'simple.php';
            Yii::$app->getResponse()->content = $controller->error('请先登录（Permission Denied）', 'user/login');
        }
        // 发送response数据到页面
        Yii::$app->getResponse()->send();
        // 结束application
        Yii::$app->end();
        return false;
    }
}
