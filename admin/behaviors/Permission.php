<?php

/**
 * this hook class was created for check admin's login status
 * and check admin's permission before action called
 * 
 */
 
namespace admin\behaviors;

use Yii;
use common\models\Admin;

/**
 * admin permission check
 * 1、login status
 * 2、action permission
 *
 * remark: whiteList => 登陆白名单
 *         permissionWhiteList => 权限白名单
 */
class Permission extends \yii\base\ActionFilter {

    // 在action之前运行，可用来过滤输入、过滤权限等
    // 如果返回值为false,则action不会运行
    public function beforeAction($action)
    {
        /** @var $controller \admin\controllers\Controller */
        // echo '<pre>'; print_r($action); die;
        $controller = & $action->controller;
        // 初始化权限验证 导航
        if($controller->parent && $controller->id != $controller->parent) {
            $controller->setNavigator($controller->parent);
        }
        else {
            $controller->navigator = ['controller' => $controller->id, 'action' => $controller->action->id];
        }
        // Yii::$app->admin = Yii::$app->session->get('admin');
        // 判断当前 controller 是否需要登录
        // echo $controller->action->id;
        // echo '<pre>'; print_r($action->controller->action); echo '</pre>'; die;
        if(isset($controller->whiteList) && in_array($controller->action->id, $controller->whiteList)) {
            return true;
        }
        // 判断登录态
        if( ! Yii::$app->isLogin()) {
            if(Yii::$app->request->isAjax) {
                // json format数据需要返回数据到 response.data中
                Yii::$app->getResponse()->data = $controller->json('Permission.Denied', '请先登录（Permission Denied）');
            }
            else {
                // html format数据直接返回数据到response.content中
                $controller->layout = 'simple.php';
                Yii::$app->getResponse()->content = $controller->error('请先登录（Permission Denied）', 'welcome/login');
            }
            // 发送response数据到页面
            Yii::$app->getResponse()->send();
            return false;
        }
        // 判断当前 controller 是否需要权限
        // echo $controller->action->id;
        // echo '<pre>'; print_r($controller->permissionWhiteList); echo '</pre>'; die;
        if(isset($controller->permissionWhiteList) && in_array($controller->action->id, $controller->permissionWhiteList)) {
            return true;
        }
        // 验证权限
        if( ! Admin::checkPermission($controller->navigator['controller'], $controller->navigator['action'])) {
            if(Yii::$app->request->isAjax) {
                // json format数据需要返回数据到 response.data中
                Yii::$app->getResponse()->data = $controller->json('Permission.Denied', '无页面访问权限（Permission Denied）');
            }
            else {
                // html format数据直接返回数据到response.content中
                $controller->layout = 'simple.php';
                Yii::$app->getResponse()->content = $controller->error('无效的访问权限（Permission Denied）');
            }
            // 发送response数据到页面
            Yii::$app->getResponse()->send();
            return false;
        }
        return true;
    }
}
