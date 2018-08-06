<?php

namespace admin\controllers;

use common\models\AdminResource;
use Yii;
use yii\helpers\ArrayHelper;
use common\helpers\Render;
use common\helpers\Checker;
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
        $query = Admin::filters(['username', 'role_id', 'mobile', 'deleted_at'], $params)->andWhere(['!=', 'id', '0']);
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
        if ( ! $admin->loadAttributes($this->request->post())->validate()) {
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
        if ( ! $admin->loadAttributes($this->request->post())->validate()) {
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
        if( ! ($ids = $this->request->post('id'))) {
            return $this->json('invalid.param', 'you must choice at least one administrator.');
        }
        if('1' == $ids || (is_array($ids) && in_array('1', $ids))) {
            return $this->json('invalid.param', 'system administrator can\'t be modify.');
        }
        if(Admin::trashAll(['id' => $ids])) {
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
        if(empty($admin)) {
            return $this->error('invalid permission group', 'admin/group-list');
        }
        if($this->request->isGet) {
            return $this->render('permissions', ['admin' => $admin]);
        }
        // update admin's permission
        $rule = [
            'param' => [
                'identities' => ['permission groups', ['status', 'required']],
                'permissions' => ['permissions', ['controller']],
            ],
        ];
        $param = $this->request->getparams($rule['param'], 'post');
        $checker = Checker::authentication($rule, $param);
        if($checker['code'] != Checker::SuccessCode) {
            return $this->error($checker['message'], 'admin/group-permissions?id='.$admin->id);
        }
        if($admin->setPermissions($param['identities'], $param['permissions'])) {
            return $this->success('administrator ('.$admin->username.') permission update successful.', [
                ['title' => 'go to administrator list page', 'url' => 'admin/group-list'],
                ['title' => 'edit administrator page', 'url' => 'admin/detail?id='.$admin->id],
                ['title' => 'edit administrator permissions again', 'url' => 'admin/permissions?id='.$admin->id],
            ]);
        }
        return $this->error('administrator ('.$admin->username.') permission update failed.', 'admin/permissions?id='.$admin->id);
    }
    
    /**
     * for navigator's CURD
     * this action showing navigator
     * return render / json
     */
    public function actionGroupList()
    {
        if( ! $this->request->isAjax) {
            // 渲染页面
            return $this->render('group-list');
        }
        $params = $this->request->post();
        $params['deleted_at'] = '0';
        $query = AdminRole::filters(['identity', ['title', 'like'], 'deleted_at'], $params)->andWhere(['!=', 'id', '1']);
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
    public function actionGroupDetail()
    {
        $adminRole = null;
        $roleId = $this->request->get('id');
        if($roleId && ( ! $adminRole = AdminRole::finder($roleId))) {
            return $this->error('invalid administrator role', 'admin/group-list');
        }
        return $this->render('group-detail', ['data' => $adminRole]);
    }
    /**
     * insert admin role
     * @request post method
     * @return string
     */
    public function actionGroupInsert()
    {
        $adminRole = new AdminRole();
        if ( ! $adminRole->loadAttributes($this->request->post())->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($adminRole->errors(), 'admin/group-list');
        }
        if ($adminRole->save()) {
            return $this->success('permission group ('.$adminRole->title.') insert success', [
                ['title' => 'go to permission group list page', 'url' => 'admin/group-list'],
                ['title' => 'edit permission group again', 'url' => 'admin/group-detail?id='.$adminRole->id],
            ]);
        }
        // 保存失败，渲染错误页面
        return $this->error('permission group ('.$adminRole->title.') insert failed', 'admin/group-list');
    }
    /**
     * update admin role
     * @request post method
     * @return string
     */
    public function actionGroupUpdate()
    {
        if( ! $adminRole = AdminRole::finder($this->request->get('id'))) {
            return $this->error('invalid permission group', 'admin/group-list');
        }
        $oldIdentify = $adminRole->getOldAttribute('identity');
        if ( ! $adminRole->loadAttributes($this->request->post())->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($adminRole->errors(), 'admin/group-detail?id='.$adminRole->id);
        }
        if ($adminRole->save()) {
            // 绑定role change
            $adminRole->onChange($oldIdentify);
            return $this->success('permission group ('.$adminRole->title.') update successful', [
                ['title' => 'go to permission group list page', 'url' => 'admin/group-list'],
                ['title' => 'edit permission group again', 'url' => 'admin/group-detail?id='.$adminRole->id],
            ]);
        }
        // 保存失败，渲染错误页面
        return $this->error('permission group ('.$adminRole->title.') update failed', 'admin/group-detail?id='.$adminRole->id);
    }
    /**
     * delete admin role
     * @param ids array/int  - role id by post ajax
     * @return string json
     */
    public function actionGroupDelete()
    {
        if( ! ($ids = $this->request->post('id'))) {
            return $this->json('invalid.param', 'you must choice at least one permission group.');
        }
        if('1' == $ids || (is_array($ids) && in_array('1', $ids))) {
            return $this->json('invalid.param', 'system permission group can\'t be modify.');
        }
        if(AdminRole::trashAll(['id' => $ids])) {
            return $this->json(SuccessCode, 'permission group delete successful.');
        }
        return $this->json('system.error', 'permission group delete failed.');
    }
    /**
     * set admin role's permission
     * @param id int  - role id by get request
     * @return string
     */
    public function actionGroupPermissions()
    {
        $adminRole = AdminRole::finder($this->request->get('id'));
        // 参数异常，渲染错误页面
        if(empty($adminRole)) {
            return $this->error('invalid permission group', 'admin/group-list');
        }
        if($this->request->isGet) {
            return $this->render('group-permissions', ['role' => $adminRole]);
        }
        // update admin role's permission
        $rule = [
            'param' => [
                'permissions' => ['permissions', ['controller']],
            ],
        ];
        $param = $this->request->post();
        $checker = Checker::authentication($rule, $param);
        if($checker['code'] != Checker::SuccessCode) {
            return $this->error($checker['message'], 'admin/group-permissions?id='.$adminRole->id);
        }
        if($adminRole->setPermissions($param['permissions'])) {
            return $this->success('permission group ('.$adminRole->title.') permission update successful.', [
                ['title' => 'go to permission group list page', 'url' => 'admin/group-list'],
                ['title' => 'edit permission group page', 'url' => 'admin/group-detail?id='.$adminRole->id],
                ['title' => 'edit permission group permissions again', 'url' => 'admin/group-permissions?id='.$adminRole->id],
            ]);
        }
        return $this->error('permission group ('.$adminRole->title.') permission update failed.', 'admin/group-permissions?id='.$adminRole->id);
    }
}
