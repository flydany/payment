<?php

namespace admin\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use common\helpers\Checker;
use common\helpers\Pager;
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
     * @name this action showing admin list
     * @param reqjuest type request->isAjax?
     * @return html|json
     */
    public function actionList()
    {
        if( ! $this->request->isAjax) {
            return $this->render('list', [
                'roles' => ArrayHelper::map(AdminRole::find()->select('id, title')->asArray()->all(), 'id', 'title'),
            ]);
        }
        $params = $this->request->post();
        $params['deleted_at'] = '0';
        $query = Admin::filterConditions(Admin::initCondition(['username', 'role_id', 'mobile', 'deleted_at'], $params));
        $data['page'] = Pager::page(['page_count' => 20, 'total_count' => $query->count()]);
        $data['infos'] = $query->orderBy('id desc')->offset(Pager::offset())->limit(Pager::limit())->asArray()->all();
        return $this->json($data);
    }
    
    /**
     * @name show admin detail
     * @param id int  - admin id by get request
     * @return string
     */
    public function actionAdminDetail()
    {
        $admin = null;
        $adminId = $this->request->get('id');
        if($adminId && ( ! $admin = Admin::finder($adminId))) {
            return $this->error('无效的管理员（Invalid Admin）', 'admin/admin-list');
        }
        return $this->render('admin-detail', [
            'roles' => ArrayHelper::map(AdminRole::find()->select('id, title')->asArray()->all(), 'id', 'title'),
            'data' => $admin,
        ]);
    }
    /**
     * insert admin
     */
    public function actionAdminInsert()
    {
        $admin = new Admin();
        $admin->setPostRequest();
        if ( ! $admin->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($admin->errors(), 'admin/admin-detail');
        }
        if ($admin->save()) {
            // 保存成功
            return $this->success('管理员（'.$admin->username.'）添加成功（Insert Success）', [
                ['title' => '前往管理员列表页', 'url' => 'admin/admin-list'],
                ['title' => '继续修改管理员', 'url' => 'admin/admin-detail?id='.$admin->id]
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('管理员（'.$admin->username.'）添加失败，请重试（System Fail）', 'admin/admin-detail');
    }
    /**
     * admin detail show / update
     * use get(id) to find admin
     */
    public function actionAdminUpdate()
    {
        /* @var $admin Admin */
        // id 为必填项，判断管理员存在状态
        // 未得到，渲染错误页面
        if( ! $admin = Admin::finder($this->request->get('id'))) {
            return $this->error('无效的管理员（Invalid Admin）', 'admin/admin-list');
        }
        $admin->setPostRequest();
        if ( ! $admin->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($admin->errors(), 'admin/admin-detail?id='.$admin->id);
        }
        if ($admin->save()) {
            // 保存成功
            return $this->success('管理员（'.$admin->username.'）更新成功（Update Success）', [
                ['title' => '前往管理员列表页', 'url' => 'admin/admin-list'],
                ['title' => '继续修改管理员', 'url' => 'admin/admin-detail?id='.$admin->id]
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('管理员（'.$admin->username.'）更新失败，请重试（System Fail）', 'admin/admin-detail?id='.$admin->id);
    }
    /**
     * delete admin
     */
    public function actionAdminDelete()
    {
        if( ! (($ids = $this->request->post('id')) || ($ids = $this->request->post('ids')))) {
            return $this->json('Invalid.Param', '请选择至少一个管理员（Exists Admin）');
        }
        else if(false && ('1' == $ids || (is_array($ids) && in_array('1', $ids)))) {
            return $this->json('Invalid.Param', '系统预留管理员，不允许删除（Permission Denied）');
        }
        else if(Admin::trashAll(['id' => $ids])) {
            return $this->json(SuccessCode, '管理员删除成功（Delete Success）');
        }
        return $this->json('System.Error', '管理员删除失败（Delete Fail）');
    }
    /**
     * set admin's permission
     */
    public function actionAdminPermissionEdit($adminId = '')
    {
        $admin = Admin::finder($adminId ? $adminId : $this->request->get('id'));
        if( ! $admin) {
            // 参数异常，渲染错误页面
            if($this->request->IsPost) {
                return $this->json('Invalid.Param', '无效的管理员（Invalid Admin）');
            }
            return $this->error('无效的管理员（Invalid Admin）', 'admin/admin-list');
        }
//        if( ! $this->request->IsPost) {
        if( ! $this->request->isAjax) {
            // render page use simple layout
            $this->layout = 'simple.php';
            return $this->render(
                'admin-permission-edit', [
                    'admin' => $admin,
                    'permissions' => ArrayHelper::map(Navigator::find()->select('id, title, parent_id')->orderBy('sort asc')->asArray()->all(), 'id', 'title', 'parent_id'),
                    'roles' => AdminRole::find()->select('id, title, identity')->all()
                ]
            );
        }
        // update admin's permission
        $rule = [
            'param' => [
                'role' => ['所属组', ['int', 'required']],
                'permission_detail' => ['权限', ['int']],
            ],
        ];
        $param = $this->request->getParams($rule['param'], 'post');
        $checker = Checker::authentication($rule, $param);
        if($checker['code'] != Checker::SuccessCode) {
            return $this->json('Invalid.Param', $checker['message'].'（Invalid Param）');
        }
        return $this->json($admin->setPermissions($param['role'], $param['permission_detail']));
    }
    
    /**
     * for navigator's CURD
     * this action showing navigator
     * return render / json
     */
    public function actionAdminRoleList()
    {
        if( ! $this->request->isAjax) {
            // 渲染页面
            return $this->render('admin-role-list');
        }
        $params = $this->request->post();
        $params['deleted_at'] = '0';
        $query = AdminRole::filterConditions(AdminRole::initCondition(['identity', ['title', 'like'], 'deleted_at'], $params));
        $data['page'] = Pager::page(['page_count' => 20, 'total_count' => $query->count()]);
        $data['infos'] = $query->orderBy('id desc')->offset(Pager::offset())->limit(Pager::limit())->asArray()->all();
        return $this->json($data);
    }
    // @name 组合前台使用数据
    public function adminRoleViewData($data = [], $handle = '')
    {
        $handle && $data['relate'] = json_encode(AdminRole::checker($handle));
        return $data;
    }
    /**
     * @name show admin role detail
     * @param id int by get request
     * @return string
     */
    public function actionAdminRoleDetail()
    {
        $adminRole = null;
        $roleId = $this->request->get('id');
        if($roleId && ( ! $adminRole = AdminRole::finder($roleId))) {
            return $this->error('无效的权组（Invalid Admin Role）', 'admin/admin-role-list');
        }
        return $this->render('admin-role-detail', $this->adminRoleViewData(
            ['data' => $adminRole],
            $roleId ? 'update' : 'insert'
        ));
    }
    /**
     * @name insert admin role
     * @request post method
     * @return string
     */
    public function actionAdminRoleInsert()
    {
        $adminRole = new AdminRole();
        $adminRole->setPostRequest();
        if ( ! $adminRole->validate()) {
            // 参数异常，渲染错误页面
            return $this->error(implode('。', ArrayHelper::getColumn($adminRole->getErrors(), '0')), 'admin/admin-role-list');
        }
        if ($adminRole->save()) {
            return $this->success('管理员权组（'.$adminRole->title.'）添加成功（Insert Success）', [
                ['title' => '前往管理员权组列表页', 'url' => 'admin/admin-role-list'],
                ['title' => '继续修改管理员权组', 'url' => 'admin/admin-role-detail?id='.$adminRole->id]
            ]);
        }
        // 保存失败，渲染错误页面
        return $this->error('管理员权组（'.$adminRole->title.'）添加失败（Insert Failed）', 'admin/admin-role-list');
    }
    /**
     * @name update admin role
     * @request post method
     * @return string
     */
    public function actionAdminRoleUpdate()
    {
        if( ! $adminRole = AdminRole::finder($this->request->get('id'))) {
            return $this->error('无效的权组（Invalid Admin Role）', 'admin/admin-role-list');
        }
        $oldIdentify = $adminRole->getOldAttribute('identity');
        $adminRole->setPostRequest();
        if ( ! $adminRole->validate()) {
            // 参数异常，渲染错误页面
            return $this->error(implode('。', ArrayHelper::getColumn($adminRole->getErrors(), '0')).'（Invalid Param）', 'admin/admin-role-detail?id='.$adminRole->id);
        }
        if ($adminRole->save()) {
            // 绑定role change
            $adminRole->onChange($oldIdentify);
            return $this->success('管理员权组（'.$adminRole->title.'）更新成功（Update Success）', [
                ['title' => '前往管理员权组列表页', 'url' => 'admin/admin-role-list'],
                ['title' => '继续修改管理员权组', 'url' => 'admin/admin-role-detail?id='.$adminRole->id]
            ]);
        }
        // 保存失败，渲染错误页面
        return $this->error('管理员权组（'.$adminRole->title.'）更新失败（Update Fail）', 'admin/admin-role-detail?id='.$adminRole->id);
    }
    /**
     * @name delete admin role
     * @param ids array/int  - role id by post ajax
     * @return string json
     */
    public function actionAdminRoleDelete()
    {
        if( ! (($ids = $this->request->post('id')) || ($ids = $this->request->post('ids')))) {
            return $this->json('Invalid.Param', '请选择至少一个管理员权组（Invalid Param）');
        }
        else if('1' == $ids || (is_array($ids) && in_array('1', $ids))) {
            return $this->json('Permission.Denied', '系统预留权组，不允许删除（Permission Denied）');
        }
        else if( ! AdminRole::trashAll(['id' => $ids])) {
            return $this->json('System.Error', '删除失败（System Error）');
        }
        return $this->json(SuccessCode, '删除成功（Delete Success）');
    }
    /**
     * @name set admin role's permission
     * @param id int  - role id by get request
     * @return string
     */
    public function actionAdminRolePermissionEdit()
    {
        $role = AdminRole::finder($this->request->get('id'));
        if( ! $role) {
            // 参数异常，渲染错误页面
            if($this->request->isPost) {
                return $this->json('Invalid.Param', '无效的权组（Invalid Param）');
            }
            return $this->error('无效的权组（Invalid Param）', 'admin/admin-role-list');
        }
        if( ! $this->request->isAjax) {
            // render page use simple layout
            $this->layout = 'simple.php';
            // $navigators = Navigator::find()->select('id, title, parent_id')->where(['flag' => 1])->orderBy('sort desc')->asArray()->all();
            return $this->render('admin-role-permission-edit', [
                'role' => $role,
                'permissions' => ArrayHelper::map(Navigator::find()->select('id, title, parent_id')->orderBy('sort asc')->asArray()->all(), 'id', 'title', 'parent_id')
            ]);
        }
        // update admin role's permission
        $rule = [
            'param' => [
                'permission_detail' => ['权限', 'int'],
            ],
        ];
        $param = $this->request->getParams($rule['param'], 'post');
        $checker = Checker::authentication($rule, $param);
        if($checker['code'] != Checker::SuccessCode) {
            return $this->json('Invalid.Param', $checker['message'].'（Invalid Param）');
        }
        if($role->setPermissions($param['permission_detail'])) {
            return $this->json(SuccessCode, '权限修改成功');
        }
        return $this->json('Update.Error', '权限修改失败');
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
            return $this->json('Invalid.Param', '数据异常：请您先选择一个权限组、用户（Invalid Param）');
        }
        $admin = false;
        if(is_numeric($id)) {
            $admin = Admin::find()->select('id, role_id')->where(['id' => $id])->asArray()->one();
        }
        return $this->json(['infos' => AdminPermission::getPermissionAsControllerId($id), 'admin' => $admin]);
    }
}
