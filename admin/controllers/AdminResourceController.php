<?php

namespace admin\controllers;

use common\helpers\Checker;
use common\models\Project;
use common\models\Merchant;
use common\models\AdminResource;

class AdminResourceController extends Controller {

    /**
     * 设置单个项目的授权对象
     * @return string
     */
    public function actionProject()
    {
        $target = 'project/list';
        if( ! $project = Project::finder($this->request->get('item_id') ?? $this->request->post('item_id'))) {
            return $this->error('invalid project', $target);
        }
        if(empty($project->hasPermission)) {
            return $this->error('permission forbidden', $target);
        }
        if($this->request->isGet) {
            return $this->render('resources', ['resource' => $project, 'commit' => 'project', 'type' => AdminResource::TypeProject, 'target' => $target]);
        }
        return $this->resources($target);
    }
    /**
     * 设置单个商户号的授权对象
     * @return string
     */
    public function actionMerchant()
    {
        $target = 'platform/merchant-list';
        if( ! $merchant = Merchant::finder($this->request->get('item_id') ?? $this->request->post('item_id'))) {
            return $this->error('invalid project', $target);
        }
        if(empty($merchant->hasPermission)) {
            return $this->error('permission forbidden', $target);
        }
        if($this->request->isGet) {
            return $this->render('resources', ['resource' => $merchant, 'commit' => 'merchant', 'type' => AdminResource::TypeMerchant, 'target' => $target]);
        }
        return $this->resources($target);
    }

    /**
     * 资源权限管理
     * @describe 设置单个资源的授权对象
     * @return string
     */
    public function resources($backUrl = '')
    {
        $params = $this->request->post();
        $checker = Checker::authentication(AdminResource::flyer(), $params);
        if($checker['code'] != Checker::SuccessCode) {
            return $this->error($checker['message'], $backUrl);
        }
        if(AdminResource::setResources($params['item_id'], $params['identity'], $params['type'])) {
            return $this->success('administrator resource: '.AdminResource::$typeSelector[$params['type']].' '.$params['item_id'].'\'s permission update successful.', $backUrl);
        }
        return $this->error('administrator resource: '.AdminResource::$typeSelector[$params['type']].' '.$params['item_id'].'\'s permission update failed.', $backUrl);
    }

    /**
     * 资源权限管理
     * @describe 设置单个对象的授权资源
     * @return string
     */
    public function permissions($backUrl = '')
    {
        $params = $this->request->post();
        $checker = Checker::authentication(AdminResource::flyer(), $params);
        if($checker['code'] != Checker::SuccessCode) {
            return $this->error($checker['message'], $backUrl);
        }
        if(AdminResource::setPermissions($params['item_id'], $params['identity'], $params['type'])) {
            return $this->success('administrator resource: '.AdminResource::$typeSelector[$params['type']].' '.$params['item_id'].'\'s permission update successful.', $backUrl);
        }
        return $this->error('administrator resource: '.AdminResource::$typeSelector[$params['type']].' '.$params['item_id'].'\'s permission update failed.', $backUrl);
    }
}
