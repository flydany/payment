<?php

namespace admin\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use common\helpers\Render;
use common\helpers\Checker;
use common\models\Project;

class ProjectController extends Controller {
    
    /**
     * this action showing project list
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
        $query = Project::filterConditions(Project::initCondition(['project_id', ['title', 'like'], 'status', 'deleted_at'], $params));
        $pagination = Render::pagination((clone $query)->count());
        $data['infos'] = $query->orderBy('id desc')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data['page'] = Render::pager($pagination);
        return $this->json($data);
    }
    
    /**
     * show project detail
     * @param id int  - project id by get request
     * @return string
     */
    public function actionDetail()
    {
        $project = null;
        $projectId = $this->request->get('id');
        if($projectId && ( ! $project = Project::finder($projectId))) {
            return $this->error('invalid project', 'project/list');
        }
        return $this->render('detail', ['data' => $project]);
    }
    /**
     * insert project
     */
    public function actionInsert()
    {
        $project = new Project();
        if ( ! $project->loadAttributes($this->request->post())->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($project->errors(), 'project/detail');
        }
        if ($project->save()) {
            // 保存成功
            return $this->success('project ('.$project->title.') insert successful', [
                ['title' => 'go to project list page', 'url' => 'project/list'],
                ['title' => 'edit project again', 'url' => 'project/detail?id='.$project->id]
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('project ('.$project->title.') insert failed, please try again', 'project/detail');
    }
    /**
     * project detail show / update
     * use get(id) to find project
     */
    public function actionUpdate()
    {
        /* @var $project project */
        // id 为必填项，判断管理员存在状态
        // 未得到，渲染错误页面
        if( ! $project = Project::finder($this->request->get('id'))) {
            return $this->error('invalid project', 'project/list');
        }
        if ( ! $project->loadAttributes($this->request->post())->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($project->errors(), 'project/detail?id='.$project->id);
        }
        if ($project->save()) {
            // 保存成功
            return $this->success('project ('.$project->title.') update successful', [
                ['title' => 'go to project list page', 'url' => 'project/list'],
                ['title' => 'edit project again', 'url' => 'project/detail?id='.$project->id],
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('project ('.$project->title.') update failed, please try again.', 'project/detail?id='.$project->id);
    }
    /**
     * delete project
     */
    public function actionDelete()
    {
        if( ! ($ids = $this->request->post('id'))) {
            return $this->json('invalid.param', 'you must choice at least one project.');
        }
        if('1' == $ids || (is_array($ids) && in_array('1', $ids))) {
            return $this->json('invalid.param', 'system project can\'t be modify.');
        }
        if(Project::trashAll(['id' => $ids])) {
            return $this->json(SuccessCode, 'project delete successful.');
        }
        return $this->json('system.error', 'project delete failed.');
    }

    /**
     * set project's permission
     * @param integer $projectId project Number
     * @return json
     */
    public function actionPermissions($projectId = '')
    {
        $project = Project::finder($projectId ? $projectId : $this->request->get('id'));
        if(empty($project)) {
            return $this->error('invalid permission group', 'project/group-list');
        }
        if($this->request->isGet) {
            return $this->render('permissions', ['project' => $project]);
        }
        // update project's permission
        $rule = [
            'param' => [
                'identities' => ['permission groups', ['status', 'required']],
                'permissions' => ['permissions', ['controller']],
            ],
        ];
        $param = $this->request->getparams($rule['param'], 'post');
        $checker = Checker::authentication($rule, $param);
        if($checker['code'] != Checker::SuccessCode) {
            return $this->error($checker['message'], 'project/group-permissions?id='.$project->id);
        }
        if($project->setPermissions($param['identities'], $param['permissions'])) {
            return $this->success('project ('.$project->title.') permission update successful.', [
                ['title' => 'go to project list page', 'url' => 'project/group-list'],
                ['title' => 'edit project page', 'url' => 'project/detail?id='.$project->id],
                ['title' => 'edit project permissions again', 'url' => 'project/permissions?id='.$project->id],
            ]);
        }
        return $this->error('project ('.$project->title.') permission update failed.', 'project/permissions?id='.$project->id);
    }
    /**
     * set project role's permission
     * @param id int  - role id by get request
     * @return string
     */
    public function actionGroupPermissions()
    {
        $projectRole = projectRole::finder($this->request->get('id'));
        // 参数异常，渲染错误页面
        if(empty($projectRole)) {
            return $this->error('invalid permission group', 'project/group-list');
        }
        if($this->request->isGet) {
            return $this->render('group-permissions', ['role' => $projectRole]);
        }
        // update project role's permission
        $rule = [
            'param' => [
                'permissions' => ['permissions', ['controller']],
            ],
        ];
        $param = $this->request->post();
        $checker = Checker::authentication($rule, $param);
        if($checker['code'] != Checker::SuccessCode) {
            return $this->error($checker['message'], 'project/group-permissions?id='.$projectRole->id);
        }
        if($projectRole->setPermissions($param['permissions'])) {
            return $this->success('permission group ('.$projectRole->title.') permission update successful.', [
                ['title' => 'go to permission group list page', 'url' => 'project/group-list'],
                ['title' => 'edit permission group page', 'url' => 'project/group-detail?id='.$projectRole->id],
                ['title' => 'edit permission group permissions again', 'url' => 'project/group-permissions?id='.$projectRole->id],
            ]);
        }
        return $this->error('permission group ('.$projectRole->title.') permission update failed.', 'project/group-permissions?id='.$projectRole->id);
    }
}
