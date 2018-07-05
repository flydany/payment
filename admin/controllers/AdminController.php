<?php

namespace admin\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use common\helpers\Render;
use common\helpers\Checker;
use common\models\Navigator;
use common\models\Admin;
use common\models\AdminRole;
use common\models\AdminPermission;

class AdminController extends Controller {
    
    // 不需要登录权限的action白名单
    public $whiteList = [
        'permission-detail',
    ];

    /**
     * this action showing administrator list
     * @param request type request->isAjax?
     * @return html|json
     */
    public function actionList()
    {
        if( ! $this->request->isAjax) {
            return $this->render('list');
        }
        $params = $this->request->post();
        $params['deleted_at'] = '0';
        $query = Admin::filterConditions(Admin::initCondition(['username', 'role_id', 'mobile', 'deleted_at'], $params));
        $pagination = Render::pagination((clone $query)->count());
        $data['infos'] = $query->orderBy('id desc')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data['page'] = Render::pager($pagination);
        return $this->json($data);
    }
    
    /**
     * show admin detail
     * @param id int  - admin id by get request
     * @return string
     */
    public function actionDetail()
    {
        $admin = null;
        $adminId = $this->request->get('id');
        if($adminId && ( ! $admin = Admin::finder($adminId))) {
            return $this->error('invalid administrator', 'admin/list');
        }
        return $this->render('detail', ['data' => $admin]);
    }
    /**
     * insert admin
     */
    public function actionInsert()
    {
        $admin = new Admin();
        $admin->setPostRequest();
        if ( ! $admin->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($admin->errors(), 'admin/detail');
        }
        if ($admin->save()) {
            // 保存成功
            return $this->success('administrator ('.$admin->username.') insert successful', [
                ['title' => 'go to manager list page', 'url' => 'admin/list'],
                ['title' => 'edit administrator again', 'url' => 'admin/detail?id='.$admin->id]
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('administrator ('.$admin->username.') insert failed, please try again', 'admin/detail');
    }
    /**
     * admin detail show / update
     * use get(id) to find admin
     */
    public function actionUpdate()
    {
        /* @var $admin Admin */
        // id 为必填项，判断管理员存在状态
        // 未得到，渲染错误页面
        if( ! $admin = Admin::finder($this->request->get('id'))) {
            return $this->error('invalid admin', 'admin/list');
        }
        $admin->setPostRequest();
        if ( ! $admin->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($admin->errors(), 'admin/detail?id='.$admin->id);
        }
        if ($admin->save()) {
            // 保存成功
            return $this->success('administrator ('.$admin->username.') update successful', [
                ['title' => 'go to manager list page', 'url' => 'admin/list'],
                ['title' => 'edit administrator again', 'url' => 'admin/detail?id='.$admin->id],
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('administrator ('.$admin->username.') update failed, please try again.', 'admin/detail?id='.$admin->id);
    }
    /**
     * delete admin
     */
    public function actionDelete()
    {
        if( ! (($ids = $this->request->post('id')) || ($ids = $this->request->post('ids')))) {
            return $this->json('invalid.param', 'you must choice at least one administrator.');
        }
        else if(false && ('1' == $ids || (is_array($ids) && in_array('1', $ids)))) {
            return $this->json('invalid.param', 'system administrator can\'t be modify.');
        }
        else if(Admin::trashAll(['id' => $ids])) {
            return $this->json(SuccessCode, 'administrator delete successful.');
        }
        return $this->json('system.error', 'administrator delete failed.');
    }
    /**
     * set admin's permission
     * @param integer $adminId administrator Number
     * @return json
     */
    public function actionPermissions($adminId = '')
    {
        $admin = Admin::finder($adminId ? $adminId : $this->request->get('id'));
        if( ! $this->request->isAjax) {
            if(empty($admin)) {
                return $this->error('invalid administrator', 'admin/list');
            }
            // render page use simple layout
            $this->layout = 'simple.php';
            return $this->render('permissions', ['admin' => $admin]);
        }
        if(empty($admin)) {
            return $this->json('invalid.administrator', 'invalid administrator.');
        }
        // update admin's permission
        $rule = [
            'param' => [
                'role' => ['administrator group', ['int', 'required']],
                'permission_detail' => ['permissions', ['int']],
            ],
        ];
        $param = $this->request->getparams($rule['param'], 'post');
        $checker = Checker::authentication($rule, $param);
        if($checker['code'] != Checker::SuccessCode) {
            return $this->json('invalid.param', $checker['message']);
        }
        return $this->json($admin->setPermissions($param['role'], $param['permission_detail']));
    }
    
    /**
     * for navigator's CURD
     * this action showing navigator
     * return render / json
     */
    public function actionRoleList()
    {
        if( ! $this->request->isAjax) {
            // 渲染页面
            return $this->render('role-list');
        }
        $params = $this->request->post();
        $params['deleted_at'] = '0';
        $query = AdminRole::filterConditions(AdminRole::initCondition(['identity', ['title', 'like'], 'deleted_at'], $params));
        $pagination = Render::pagination((clone $query)->count());
        $data['infos'] = $query->orderBy('id desc')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data['page'] = Render::pager($pagination);
        return $this->json($data);
    }
    /**
     * show admin role detail
     * @param id int by get request
     * @return string
     */
    public function actionRoleDetail()
    {
        $adminRole = null;
        $roleId = $this->request->get('id');
        if($roleId && ( ! $adminRole = AdminRole::finder($roleId))) {
            return $this->error('invalid administrator role', 'admin/role-list');
        }
        return $this->render('role-detail', ['data' => $adminRole]);
    }
    /**
     * insert admin role
     * @request post method
     * @return string
     */
    public function actionRoleInsert()
    {
        $adminRole = new AdminRole();
        $adminRole->setPostRequest();
        if ( ! $adminRole->validate()) {
            // 参数异常，渲染错误页面
            return $this->error(implode('. ', ArrayHelper::getColumn($adminRole->getErrors(), '0')), 'admin/role-list');
        }
        if ($adminRole->save()) {
            return $this->success('administrator group ('.$adminRole->title.') insert success', [
                ['title' => 'go to administrator group list page', 'url' => 'admin/role-list'],
                ['title' => 'edit administrator group again', 'url' => 'admin/role-detail?id='.$adminRole->id],
            ]);
        }
        // 保存失败，渲染错误页面
        return $this->error('administrator group ('.$adminRole->title.') insert failed', 'admin/role-list');
    }
    /**
     * update admin role
     * @request post method
     * @return string
     */
    public function actionRoleUpdate()
    {
        if( ! $adminRole = AdminRole::finder($this->request->get('id'))) {
            return $this->error('invalid administrator group', 'admin/role-list');
        }
        $oldIdentify = $adminRole->getOldAttribute('identity');
        $adminRole->setPostRequest();
        if ( ! $adminRole->validate()) {
            // 参数异常，渲染错误页面
            return $this->error(implode('. ', ArrayHelper::getColumn($adminRole->getErrors(), '0')), 'admin/role-detail?id='.$adminRole->id);
        }
        if ($adminRole->save()) {
            // 绑定role change
            $adminRole->onChange($oldIdentify);
            return $this->success('administrator group ('.$adminRole->title.') update successful', [
                ['title' => 'go to administrator group list page', 'url' => 'admin/role-list'],
                ['title' => 'edit administrator group again', 'url' => 'admin/role-detail?id='.$adminRole->id],
            ]);
        }
        // 保存失败，渲染错误页面
        return $this->error('administrator group ('.$adminRole->title.') update failed', 'admin/role-detail?id='.$adminRole->id);
    }
    /**
     * delete admin role
     * @param ids array/int  - role id by post ajax
     * @return string json
     */
    public function actionRoleDelete()
    {
        if( ! (($ids = $this->request->post('id')) || ($ids = $this->request->post('ids')))) {
            return $this->json('invalid.param', 'you must choice at least one administrator group.');
        }
        else if('1' == $ids || (is_array($ids) && in_array('1', $ids))) {
            return $this->json('invalid.param', 'system administrator group can\'t be modify.');
        }
        else if(AdminRole::trashAll(['id' => $ids])) {
            return $this->json(SuccessCode, 'administrator group delete successful.');
        }
        return $this->json('system.error', 'administrator group delete failed.');
    }
    /**
     * set admin role's permission
     * @param id int  - role id by get request
     * @return string
     */
    public function actionRolePermission()
    {
        $role = AdminRole::finder($this->request->get('id'));
        if( ! $this->request->isAjax) {
            if(empty($role)) {
                return $this->error('invalid administrator group', 'admin/role-list');
            }
            // render page use simple layout
            $this->layout = 'simple.php';
            return $this->render('role-permissions', ['role' => $role]);
        }
        // 参数异常，渲染错误页面
        if(empty($role)) {
            return $this->json('invalid.param', 'invalid administrator group');
        }
        // update admin role's permission
        $rule = [
            'param' => [
                'permission_detail' => ['permissions', 'int'],
            ],
        ];
        $param = $this->request->getparams($rule['param'], 'post');
        $checker = Checker::authentication($rule, $param);
        if($checker['code'] != Checker::SuccessCode) {
            return $this->json('invalid.param', $checker['message'].' (invalid param) ');
        }
        if($role->setPermissions($param['permission_detail'])) {
            return $this->json(SuccessCode, 'administrator group ('.$role->title.') permission update successful.');
        }
        return $this->json('system.error', 'administrator group ('.$role->title.') permission update failed.');
    }

    /**
     * role permission detail
     * 顶级栏目：子栏目：true 仅有子栏目权限
     * 顶级栏目：super 有当前子栏目下的所有权限
     * super 有当前系统的所有权限
     * return {"infos":{"1":{"11":true},"4":{"super":true}},"code":200,"msg":""}
     */
    public function actionPermissionDetail()
    {
        if( ! $id = $this->request->post('id')) {
            return $this->json('invalid.param', 'you must choice at least one administrator or administrator group.');
        }
        $admin = false;
        if(is_numeric($id)) {
            $admin = Admin::find()->select('id, role_id')->where(['id' => $id])->asArray()->one();
        }
        return $this->json(['infos' => AdminPermission::permissionSelector($id), 'admin' => $admin]);
    }
}
