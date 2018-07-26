<?php

namespace admin\controllers;

use common\helpers\Render;
use common\models\Recharge;
use common\models\RechargeLog;
use common\models\AdminResource;

class RechargeController extends Controller {

    /**
     *  showing recharge order list
     * @return string
     */
    public function actionList()
    {
        if($this->request->isGet) {
            return $this->render('list');
        }
        $params = $this->request->post();
        foreach(['start', 'end'] as $key) {
            if(isset($params[$key])) {
                $params[$key] = strtotime($params[$key]);
            }
        }
        $params['deleted_at'] = 0;
        $conditions = [
            'recharge_id', 'project_merchant_id', 'order_number', 'source_order_number', 'bank_id',
            ['created_at', '>=', 'start'], ['created_at', '<=', 'end'], 'status', 'deleted_at',
        ];
        $query = Recharge::filters($conditions, $params)->filterResource(AdminResource::TypeProject);
        $pagination = Render::pagination((clone $query)->count());
        $data['infos'] = $query->with('project')->with('projectMerchant')->orderBy('id desc')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data['page'] = Render::pager($pagination);
        return $this->json($data);
    }

    /**
     * show recharge detail
     * @param integer $id  - recharge id by get request
     * @return string
     */
    public function actionDetail()
    {
        if( ! $recharge = Recharge::finder($this->request->get('id'))) {
            return $this->error('invalid recharge', 'recharge/list');
        }
        if(empty($recharge->hasPermission)) {
            return $this->error('permission forbidden', 'recharge/list');
        }
        return $this->render('detail', ['recharge' => $recharge]);
    }
    /**
     * recharge detail show / update
     * @param integer $id  - recharge id by get request
     * @return mixed
     */
    public function actionUpdate()
    {
        /* @var $recharge Recharge */
        // id 为必填项，判断管理员存在状态
        // 未得到，渲染错误页面
        if( ! $recharge = Recharge::finder($this->request->get('id'))) {
            return $this->error('invalid recharge', 'recharge/list');
        }
        if($recharge && empty($recharge->hasPermission)) {
            return $this->error('permission forbidden', 'recharge/list');
        }
        $params = $this->request->post();
        $params['success_at'] = empty($params['success_at']) ? 0 : strtotime($params['success_at']);
        if ( ! $recharge->loadAttributes($params)->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($recharge->errors(), 'recharge/detail?id='.$recharge->id);
        }
        if ($recharge->save()) {
            // 保存成功
            return $this->success('recharge ('.$recharge->order_number.') update successful', [
                ['title' => 'go to recharge list page', 'url' => 'recharge/list'],
                ['title' => 'edit recharge again', 'url' => 'recharge/detail?id='.$recharge->id],
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('recharge ('.$recharge->order_number.') update failed, please try again.', 'recharge/detail?id='.$recharge->id);
    }
    /**
     * delete recharge
     * @param integer $id  - recharge id by post request
     * @return mixed
     */
    public function actionDelete()
    {
        return $this->json('system.error', 'it is not allowed to delete.');

        if( ! ($ids = $this->request->post('id'))) {
            return $this->json('invalid.param', 'you must choice at least one recharge.');
        }
        if(Recharge::trashAll(['id' => $ids])) {
            return $this->json(SuccessCode, 'recharge delete successful.');
        }
        return $this->json('system.error', 'recharge delete failed.');
    }

    /**
     *  show recharge log information
     * @param integer $id  - recharge id by get request
     * @return mixed
     */
    public function actionLogs()
    {
        if( ! $recharge = Recharge::finder($this->request->get('id'))) {
            return $this->json('Invalid.Recharge', 'invalid recharge');
        }
        if(empty($recharge->hasPermission)) {
            return $this->json('Permission.Forbidden', 'permission forbidden');
        }
        $query = RechargeLog::find()->where(['recharge_id' => $recharge->id]);
        $pagination =Render::pagination((clone $query)->count());
        $data['infos'] = $query->with('operator')->orderBy('id desc')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data['page'] = Render::pager($pagination);
        return $this->json($data);
    }

    /**
     *  开始给用户充值
     * @param integer $id  - recharge id by get request
     * @return mixed
     */
    public function actionSuccess()
    {
        if( ! $recharge = Recharge::finder($this->request->get('id'))) {
            return $this->error('invalid recharge order', 'recharge/list');
        }
        if($recharge->success($this->request->post('remark'))) {
            return $this->success('recharge order: '.$recharge->id.' operate success', [
                ['title' => 'go to recharge list page', 'url' => 'recharge/list'],
                ['title' => 'edit recharge again', 'url' => 'recharge/detail?id='.$recharge->id],
            ]);
        }
        return $this->error('recharge order: '.$recharge->id.' update `success` failed', 'recharge/detail?id='.$recharge->id);
    }

    /**
     *  拒绝充值申请
     * @param integer $id  - recharge id by get request
     * @return mixed
     */
    public function actionRefuse()
    {
        if( ! $recharge = Recharge::finder($this->request->get('id'))) {
            return $this->error('invalid recharge order', 'recharge/list');
        }
        if($recharge->refuse($this->request->post('remark'))) {
            return $this->success('recharge order: '.$recharge->id.' refused success', [
                ['title' => 'go to recharge list page', 'url' => 'recharge/list'],
                ['title' => 'edit recharge again', 'url' => 'recharge/detail?id='.$recharge->id],
            ]);
        }
        return $this->error('recharge order: '.$recharge->id.' update `failed` failed', 'recharge/detail?id='.$recharge->id);
    }
}
