<?php

namespace admin\controllers;

use common\models\AdminResource;
use Yii;
use common\helpers\Render;
use common\helpers\Checker;
use common\models\Project;
use common\models\ProjectContacts;

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
        $query = Project::filters(['id', ['title', 'like'], 'status', 'deleted_at'], $params)->filterResource(AdminResource::TypeProject);
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
        if($project && empty($project->hasPermission)) {
            return $this->error('permission forbidden', 'project/list');
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
        /* @var $project Project */
        // id 为必填项，判断管理员存在状态
        // 未得到，渲染错误页面
        if( ! $project = Project::finder($this->request->get('id'))) {
            return $this->error('invalid project', 'project/list');
        }
        if($project && empty($project->hasPermission)) {
            return $this->error('permission forbidden', 'project/list');
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
        return $this->json('system.error', 'it is not allowed to delete.');

        if( ! ($ids = $this->request->post('id'))) {
            return $this->json('invalid.param', 'you must choice at least one project.');
        }
        if(Project::trashAll(['id' => $ids])) {
            return $this->json(SuccessCode, 'project delete successful.');
        }
        return $this->json('system.error', 'project delete failed.');
    }

    /**
     * this action showing project contacts list
     * @param request type request->isAjax?
     * @return html|json
     */
    public function actionContactsList()
    {
        if( ! $this->request->isAjax) {
            return $this->render('contacts-list');
        }
        $params = $this->request->post();
        $query = ProjectContacts::filters(['project_id', 'name', 'mobile', 'identity', 'email'], $params)->filterResource(AdminResource::TypeProject);
        $pagination = Render::pagination((clone $query)->count());
        $data['infos'] = $query->with('project')->orderBy('id desc')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data['page'] = Render::pager($pagination);
        return $this->json($data);
    }

    /**
     * show project detail
     * @param id int  - project id by get request
     * @return string
     */
    public function actionContactsDetail()
    {
        $contacts = null;
        $contactsId = $this->request->get('id');
        if($contactsId && ( ! $contacts = ProjectContacts::finder($contactsId))) {
            return $this->error('invalid project contacts', 'project/contacts-list');
        }
        if($contacts && empty($contacts->project->hasPermission)) {
            return $this->error('permission forbidden', 'project/contacts-list');
        }
        return $this->render('contacts-detail', ['data' => $contacts]);
    }
    /**
     * insert project
     */
    public function actionContactsInsert()
    {
        $project = Project::finder($this->request->post('project_id'));
        if(empty($project)) {
            return $this->error('unknown project', 'project/contacts-list');
        }
        if(empty($project->hasPermission)) {
            return $this->error('permission forbidden', 'project/contacts-list');
        }
        $contacts = new ProjectContacts();
        if ( ! $contacts->loadAttributes($this->request->post())->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($contacts->errors(), 'project/contacts-detail');
        }
        if ($contacts->save()) {
            // 保存成功
            return $this->success('project contacts ('.$contacts->name.') insert successful', [
                ['title' => 'go to project contacts list page', 'url' => 'project/contacts-list'],
                ['title' => 'edit project contacts again', 'url' => 'project/contacts-detail?id='.$contacts->id]
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('project contacts ('.$contacts->name.') insert failed, please try again', 'project/contacts-detail');
    }
    /**
     * project detail show / update
     * use get(id) to find project
     */
    public function actionContactsUpdate()
    {
        /* @var $contacts ProjectContacts */
        // id 为必填项，判断管理员存在状态
        // 未得到，渲染错误页面
        if( ! $contacts = ProjectContacts::finder($this->request->get('id'))) {
            return $this->error('invalid project contacts', 'project/contacts-list');
        }
        if ( ! $contacts->loadAttributes($this->request->post())->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($contacts->errors(), 'project/contacts-detail?id='.$contacts->id);
        }
        if(empty($contacts->project)) {
            return $this->error('unknown project', 'project/contacts-list');
        }
        if(empty($contacts->project->hasPermission)) {
            return $this->error('permission forbidden', 'project/contacts-list');
        }
        if ($contacts->save()) {
            // 保存成功
            return $this->success('project ('.$contacts->name.') update successful', [
                ['title' => 'go to project contacts list page', 'url' => 'project/contacts-list'],
                ['title' => 'edit project contacts again', 'url' => 'project/contacts-detail?id='.$contacts->id],
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('project contacts ('.$contacts->name.') update failed, please try again.', 'project/contacts-detail?id='.$contacts->id);
    }
    /**
     * delete project
     */
    public function actionContactsDelete()
    {
        if( ! ($ids = $this->request->post('id'))) {
            return $this->json('invalid.param', 'you must choice at least one project contacts.');
        }
        if(ProjectContacts::trashAll(['id' => $ids])) {
            return $this->json(SuccessCode, 'project contacts delete successful.');
        }
        return $this->json('system.error', 'project contacts delete failed.');
    }
}
