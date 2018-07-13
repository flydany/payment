<?php

namespace admin\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use common\helpers\Checker;
use common\helpers\Captcha;
use common\models\Admin;
use common\models\Navigator;

class SiteController extends Controller {
    
    // 登录白名单
    public $whiteList = [
       'login', 'captcha', 'logout', 'error', 'success', 'skip', 'sub-navigator', 'phone-captcha',
    ];
    // 权限验证白名单
    public $permissionWhiteList = [
        'index', 'personal-data', 'change-password', 'show-permission',
    ];

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // $this->layout = 'simple.php';

        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if ($this->isLogin()) {
            return $this->goHome();
        }
        $this->layout = 'simple.php';
        $rule = [
            'param' => [
                'username' => ['登录名', ['username', 'required']],
                'password' => ['密码', ['password', 'required']],
                'captcha' => ['验证码', ['length' => 4, 'required']],
            ]
        ];
        if( ! $this->request->IsPost) {
            return $this->render('login', ['relate' => json_encode($rule), 'param' => []]);
        }
        $param = $this->request->getParams($rule['param'], 'post');
        $checker = Checker::authentication($rule, $param);
        // 参数异常，渲染错误页面
        if($checker['code'] != Checker::SuccessCode) {
            return $this->skip($checker['message'], 'site/login');
        }
        if( ! Captcha::validate($param['captcha'], 'admin_login')) {
            return $this->skip('验证码错误（Invalid Captcha', 'site/login');
        }
        /** @var Admin $admin */
        $admin = Admin::find()->where(['username' => $param['username']])->with('adminRole')->one();
        if(empty($admin)) {
            return $this->skip('无效的管理员（Admin Exists）', 'site/login');
        }
        if( ! $admin->validatePassword($param['password'])) {
            return $this->skip('无效的管理员（Invalid Password）', 'site/login');
        }
        if( ! $admin->valid()) {
            return $this->skip('无效的管理员（Invalid Admin）', 'site/login');
        }
        $admin->login();
        return $this->goHome();
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        $this->session->remove('admin');

        return $this->skip('成功退出（Login Out）', 'site/login');
    }

    // 个人资料
    public function actionPersonalData()
    {
        $navigators = Navigator::find()->select('id, title, parent_id')->where(['flag' => 1])->orderBy('sort DESC')->asArray()->all();
        return $this->render('personal-data', ['permissions' => ArrayHelper::map($navigators, 'id', 'title', 'parent_id')]);
    }
    public function actionShowPermission()
    {
        return (new \admin\controllers\AdminController([], []))->actionAdminPermissionEdit();
    }

    // 修改密码
    public function actionChangePassword()
    {
        $rule = [
            'param' => [
                'old' => ['旧密码', ['password', 'required']],
                'new' => ['新密码', ['password', 'required']],
                'renew' => ['重复密码', ['password', 'eq' => ':new', 'required'], ['eq' => '两次输入密码不一致']],
            ]
        ];
        if( ! $this->request->IsPost) {
            return $this->render('change-password', ['relate' => json_encode($rule)]);
        }
        $param = $this->request->getParams($rule['param'], 'post');
        $checker = Checker::authentication($rule, $param);
        if($checker['code'] != Checker::SuccessCode) {
            // 参数异常，渲染错误页面
            return $this->error($checker['message'].'（Invalid Param）', 'site/change-password');
        }
        $admin = Admin::findOne($this->admin['id']);
        if( ! $admin->validatePassword($param['old'])) {
            // 参数异常，渲染错误页面
            return $this->error('旧密码错误（Invalid Old Password）', 'site/change-password');
        }
        $admin->password_digest = $param['new'];
        if( ! $admin->save()) {
            return $this->error('密码修改失败（System Error）', 'site/personal-data');
        }
        $admin->login();
        return $this->success('密码修改成功（Changed Password）', 'site/personal-data');
    }

    /**
     * captcha
     */
    public function actionCaptcha()
    {
        $rule = [
            'param' => [
                'name' => ['名称', ['username', 'required']],
            ]
        ];
        $params = $this->request->get();
        $checker = Checker::authentication($rule, $params);
        // 参数异常，渲染错误页面
        if($checker['code'] != Checker::SuccessCode) {
            return 'Error';
        }
        // 生成图形验证码
        Captcha::send($params['name']);
    }

    /**
     * @name Display sub navigator.
     *
     * @return string
     */
    public function actionSubNavigator()
    {
        /**
         * the parent navigator id
         * to find the id's childrens
         *
         * return the json data of navigator
         */
        $id = $this->request->post('id');
        if( ! $id) {
            return $this->json('Invalid.Param', '请刷新页面重试（Invalid Param）');
        }
        return $this->json(['navigators' => Navigator::getLefter($id)]);
    }
    // 操作成功 / 失败 提示页面
    public function actionSkip()
    {
        $this->layout = 'simple.php';
        return $this->render('../layouts/skip', ['message' => $this->request->get('message'), 'skip' => $this->request->get('skip')]);
    }
}
