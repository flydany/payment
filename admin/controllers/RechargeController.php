<?php

namespace admin\controllers;

use common\helpers\Checker;
use common\helpers\Render;
use common\models\Platform;
use common\models\Recharge;
use common\models\RechargeLog;

class RechargeController extends Controller {

    /**
     * @name showing recharge order list
     * @return string
     */
    public function actionList()
    {
        $params = $this->request->post();
        if($this->request->isGet) {
            return $this->render('list', ['params' => $params]);
        }
        foreach(['start', 'end'] as $key) {
            if(isset($params[$key])) {
                $params[$key] = strtotime($params[$key]);
            }
        }
        $params['deleted_at'] = 0;
        $conditions = [
            'project_id', 'project_merchant_id', 'order_number', 'source_order_number', 'bank_id',
            ['created_at', '>=', 'start'], ['created_at', '<=', 'end'], 'status', 'deleted_at',
        ];
        $query = Recharge::filters($conditions, $params);
        $pagination = Render::pagination((clone $query)->count());
        $data['infos'] = $query->orderBy('id desc')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data['page'] = Render::pager($pagination);
        return $this->json($data);
    }

    /**
     * @name show recharge order information
     * @param id int by get request
     * @return string
     */
    public function actionDetail()
    {
        if( ! $recharge = Recharge::finder($this->request->get('id'))) {
            return $this->error('无效的充值订单（Invalid Order）', 'recharge/recharge-list');
        }
        return $this->render('recharge-detail', ['data' => $recharge]);
    }
    
    /**
     * @name show recharge log information
     * @param id int by get request
     * @return string
     */
    public function actionRechargeLog()
    {
        $id = $this->request->get('id');
        if(empty($id)) {
            return $this->json('Invalid.Param', '无效的请求（Invalid Param）');
        }
        $query = RechargeLog::find()->where(['recharge_id' => $id]);
        $data['page'] = Pager::page(['page_count' => 20, 'total_count' => $query->count()]);
        $data['infos'] = $query->orderBy('id desc')->offset(Pager::offset())->limit(Pager::limit())->asArray()->all();
        return $this->json($data);
    }
    
    /**
     * @name 开始给用户充值
     * @param $id int 充值编号
     * @return mixed
     */
    public function actionSuccess()
    {
        if( ! $recharge = Recharge::finder($this->request->get('id'))) {
            return $this->error('无效的充值申请记录（Invalid Recharge）', 'recharge/recharge-list');
        }
        if($recharge->success($this->request->post('remark'))) {
            return $this->success('充值申请记录（'.$recharge->id.'）充值成功（Recharge Success）', [
                ['title' => '前往充值申请列表页', 'url' => 'recharge/recharge-list'],
                ['title' => '继续更新充值申请记录', 'url' => 'recharge/recharge-detail?id='.$recharge->id]
            ]);
        }
        return $this->error('充值申请记录（'.$recharge->id.'）设置`充值成功`失败（Recharge Failed）', 'recharge/recharge-detail?id='.$recharge->id);
    }
    
    /**
     * @name 拒绝充值申请
     * @param $id int 充值编号
     * @return mixed
     */
    public function actionRefuse()
    {
        if( ! $recharge = Recharge::finder($this->request->get('id'))) {
            return $this->error('无效的充值申请记录（Invalid Recharge）', 'recharge/recharge-list');
        }
        if($recharge->refuse($this->request->post('remark'))) {
            return $this->success('充值申请记录（'.$recharge->id.'）已拒绝（Refuse Success）', [
                ['title' => '前往充值申请列表页', 'url' => 'recharge/recharge-list'],
                ['title' => '继续更新充值申请记录', 'url' => 'recharge/recharge-detail?id='.$recharge->id]
            ]);
        }
        return $this->error('充值申请记录（'.$recharge->id.'）拒绝失败（Refuse Failed）', 'recharge/recharge-detail?id='.$recharge->id);
    }
}
