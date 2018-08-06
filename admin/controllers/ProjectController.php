<?php

namespace admin\controllers;

use Yii;
use common\helpers\Render;
use common\models\Project;
use common\models\ProjectApi;
use common\models\ProjectContacts;
use common\models\ProjectMerchant;
use common\models\AdminResource;

class ProjectController extends Controller {

    /*********************************************************************************/
    /************** project  *********************************************************/
    /*********************************************************************************/
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
        if(empty($project->hasPermission)) {
            return $this->error('permission forbidden', 'project/list');
        }
        if ( ! $project->loadAttributes($this->request->post)->validate()) {
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

    /*********************************************************************************/
    /************** project api  ****************************************************/
    /*********************************************************************************/
    /**
     * this action showing project api list
     * @param request type request->isAjax?
     * @return html|json
     */
    public function actionApiList()
    {
        if( ! $this->request->isAjax) {
            return $this->render('api-list');
        }
        $params = $this->request->post();
        $query = ProjectApi::filters([['title', 'like'], 'project_id', 'api'], $params)->filterResource(AdminResource::TypeProject);
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
    public function actionApiDetail()
    {
        $api = null;
        $apiId = $this->request->get('id');
        if($apiId && ( ! $api = ProjectApi::finder($apiId))) {
            return $this->error('invalid project api', 'project/api-list');
        }
        if($api && empty($api->project->hasPermission)) {
            return $this->error('permission forbidden', 'project/api-list');
        }
        return $this->render('api-detail', ['data' => $api]);
    }
    /**
     * insert project
     */
    public function actionApiInsert()
    {
        $api = new ProjectApi();
        $params = $this->request->post();
        $times = [];
        foreach($params['start'] as $k => $start) {
            if(empty($start) || empty($params['end'][$k])) {
                $params['times'] = '';
                continue;
            }
            $times[] = ['start' => $start, 'end' => $params['end'][$k]];
        }
        $params['times'] = json_encode($times);
        $parameters = [];
        foreach($params['parameter_name'] as $k => $name) {
            if(empty($name)) {
                continue;
            }
            $parameters[$name] = $params['parameter_value'][$k];
        }
        $params['parameters'] = json_encode($parameters);
        if ( ! $api->loadAttributes($params)->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($api->errors(), 'project/api-detail');
        }
        if(empty($api->project->hasPermission)) {
            return $this->error('permission denied of project: '.$api->project->title, 'project/api-list');
        }
        if ($api->save()) {
            // 保存成功
            return $this->success('project api ('.$api->title.') insert successful', [
                ['title' => 'go to project api list page', 'url' => 'project/api-list'],
                ['title' => 'edit project api again', 'url' => 'project/api-detail?id='.$api->id]
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('project api ('.$api->title.') insert failed, please try again', 'project/api-detail');
    }
    /**
     * project detail show / update
     * use get(id) to find project
     */
    public function actionApiUpdate()
    {
        /* @var $api ProjectApi */
        // id 为必填项，判断管理员存在状态
        // 未得到，渲染错误页面
        if( ! $api = ProjectApi::finder($this->request->get('id'))) {
            return $this->error('invalid project api', 'project/api-list');
        }
        if(empty($api->project->hasPermission)) {
            return $this->error('permission denied of project: '.$api->project->title, 'project/api-list');
        }
        $params = $this->request->post();
        $times = [];
        foreach($params['start'] as $k => $start) {
            if(empty($start) || empty($params['end'][$k])) {
                $params['times'] = '';
                continue;
            }
            $times[] = ['start' => $start, 'end' => $params['end'][$k]];
        }
        $params['times'] = json_encode($times);
        $parameters = [];
        foreach($params['parameter_name'] as $k => $name) {
            if(empty($name)) {
                continue;
            }
            $parameters[$name] = $params['parameter_value'][$k];
        }
        $params['parameters'] = json_encode($parameters);
        if ( ! $api->loadAttributes($params)->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($api->errors(), 'project/api-detail?id='.$api->id);
        }
        if(empty($api->project->hasPermission)) {
            return $this->error('permission denied of project: '.$api->project->title, 'project/api-list');
        }
        if ($api->save()) {
            // 保存成功
            return $this->success('project ('.$api->title.') update successful', [
                ['title' => 'go to project api list page', 'url' => 'project/api-list'],
                ['title' => 'edit project api again', 'url' => 'project/api-detail?id='.$api->id],
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('project api ('.$api->title.') update failed, please try again.', 'project/api-detail?id='.$api->id);
    }
    /**
     * delete project
     */
    public function actionApiDelete()
    {
        if( ! ($ids = $this->request->post('id'))) {
            return $this->json('invalid.param', 'you must choice at least one project api.');
        }
        if(ProjectApi::trashAll(['id' => $ids])) {
            return $this->json(SuccessCode, 'project api delete successful.');
        }
        return $this->json('system.error', 'project api delete failed.');
    }

    /*********************************************************************************/
    /************** project contacts  **********************************************/
    /*********************************************************************************/
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

    /**
     * this action showing project merchant list
     * @param request type request->isAjax?
     * @return html|json
     */
    public function actionMerchantList()
    {
        if( ! $this->request->isAjax) {
            return $this->render('merchant-list');
        }
        $params = $this->request->post();
        $query = ProjectMerchant::filters(['id', ['title', 'like'], 'project_id', 'platform_id', 'paytype', 'status'], $params)->filterResource(AdminResource::TypeProject);
        $pagination = Render::pagination((clone $query)->count());
        $data['infos'] = $query->with('project')->with('merchant')->orderBy('id desc')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data['page'] = Render::pager($pagination);
        return $this->json($data);
    }

    /**
     * show project detail
     * @param id int  - project id by get request
     * @return string
     */
    public function actionMerchantDetail()
    {
        $merchant = null;
        $merchantId = $this->request->get('id');
        if($merchantId && ( ! $merchant = ProjectMerchant::finder($merchantId))) {
            return $this->error('invalid project merchant', 'project/merchant-list');
        }
        if($merchant && empty($merchant->hasPermission)) {
            return $this->error('permission forbidden', 'project/merchant-list');
        }
        return $this->render('merchant-detail', ['data' => $merchant]);
    }
    /**
     * insert project
     */
    public function actionMerchantInsert()
    {
        $merchant = new ProjectMerchant();
        if ( ! $merchant->loadAttributes($this->request->post())->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($merchant->errors(), 'project/merchant-detail');
        }
        if(empty($merchant->project)) {
            return $this->error('unknown project', 'project/merchant-list');
        }
        if(empty($merchant->merchant)) {
            // 参数异常，渲染错误页面
            return $this->error('unknow merchant configurate', 'project/merchant-detail');
        }
        if(empty($merchant->hasPermission)) {
            return $this->error('permission forbidden', 'project/merchant-list');
        }
        if ($merchant->save()) {
            // 保存成功
            return $this->success('project merchant ('.$merchant->title.') insert successful', [
                ['title' => 'go to project merchant list page', 'url' => 'project/merchant-list'],
                ['title' => 'edit project merchant again', 'url' => 'project/merchant-detail?id='.$merchant->id]
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('project merchant ('.$merchant->title.') insert failed, please try again', 'project/merchant-detail');
    }
    /**
     * project detail show / update
     * use get(id) to find project merchant
     */
    public function actionMerchantUpdate()
    {
        /* @var $merchant ProjectMerchant */
        // id 为必填项，判断项目商户号存在状态
        // 未得到，渲染错误页面
        if( ! $merchant = ProjectMerchant::finder($this->request->get('id'))) {
            return $this->error('invalid project merchant', 'project/merchant-list');
        }
        if ( ! $merchant->loadAttributes($this->request->post())->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($merchant->errors(), 'project/merchant-detail?id='.$merchant->id);
        }
        if(empty($merchant->merchant)) {
            // 参数异常，渲染错误页面
            return $this->error('unknow merchant configurate', 'project/merchant-detail');
        }
        if(empty($merchant->project)) {
            return $this->error('unknown project', 'project/merchant-list');
        }
        if(empty($merchant->hasPermission)) {
            return $this->error('permission forbidden', 'project/merchant-list');
        }
        if ($merchant->save()) {
            // 保存成功
            return $this->success('project ('.$merchant->title.') update successful', [
                ['title' => 'go to project merchant list page', 'url' => 'project/merchant-list'],
                ['title' => 'edit project merchant again', 'url' => 'project/merchant-detail?id='.$merchant->id],
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('project merchant ('.$merchant->title.') update failed, please try again.', 'project/merchant-detail?id='.$merchant->id);
    }
    /**
     * delete project merchant
     */
    public function actionMerchantDelete()
    {
        if( ! ($ids = $this->request->post('id'))) {
            return $this->json('invalid.param', 'you must choice at least one project merchant.');
        }
        if(ProjectMerchant::trashAll(['id' => $ids])) {
            return $this->json(SuccessCode, 'project merchant delete successful.');
        }
        return $this->json('system.error', 'project merchant delete failed.');
    }
}
