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
        if( ! $project = Project::finder($this->request->get('id'))) {
            return $this->error('invalid project', $target);
        }
        if(empty($project->hasPermission)) {
            return $this->error('permission forbidden', $target);
        }
        if($this->request->isGet) {
            return $this->render('resources', ['resource' => $project, 'target' => $target]);
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
        if( ! $merchant = Merchant::finder($this->request->get('id'))) {
            return $this->error('invalid project', $target);
        }
        if(empty($merchant->hasPermission)) {
            return $this->error('permission forbidden', $target);
        }
        if($this->request->isGet) {
            return $this->render('resources', ['resource' => $merchant, 'target' => $target]);
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
        if(AdminResource::setResources($params['power'], $params['identity'], $params['type'])) {
            return $this->success('administrator resource: '.AdminResource::$typeSelector[$params['type']].' '.$params['power'].'\'s permission update successful.', $backUrl);
        }
        return $this->error('administrator resource: '.AdminResource::$typeSelector[$params['type']].' '.$params['power'].'\'s permission update failed.', $backUrl);
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
        if(AdminResource::setPermissions($params['powers'], $params['identity'], $params['type'])) {
            return $this->success('administrator resource: '.AdminResource::$typeSelector[$params['type']].' '.$params['identity'].'\'s permission update successful.', $backUrl);
        }
        return $this->error('administrator resource: '.AdminResource::$typeSelector[$params['type']].' '.$params['identity'].'\'s permission update failed.', $backUrl);
    }
}
