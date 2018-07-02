<?php

namespace admin\controllers;

use common\helpers\Checker;
use common\helpers\Pager;
use common\models\Recharge;
use common\models\RechargeLog;

class RechargeController extends Controller {

    public $parent = 'finance';

    /**
     * @name showing recharge order list
     * @return string
     */
    public function actionRechargeList()
    {
        if( ! $this->request->isAjax) {
            return $this->render('recharge-list');
        }
        $params = $this->request->post();
        $rules = [
            'param' => [
                'user_id' => ['用户编号', ['int']],
                'type' => ['充值方式', ['inkey' => Recharge::$platformSelector]],
                'start' => ['开始时间', ['date' => 'Y-m-d']],
                'end' => ['结束时间', ['date' => 'Y-m-d']],
                'status' => ['状态', ['int']],
            ],
        ];
        $checker = Checker::authentication($rules, $params);
        if($checker['code'] != SuccessCode) {
            return $this->json('Param.Error', $checker['message']);
        }
        foreach(['start', 'end'] as $key) {
            if($params[$key]) {
                $params[$key] = strtotime($params[$key]);
            }
        }
        $params['deleted_at'] = 0;
        $query = Recharge::filterConditions(Recharge::initCondition(['user_id', 'type', ['created_at', '>=', 'start'], ['created_at', '<=', 'end'], 'status', 'deleted_at'], $params));
        $data['page'] = Pager::page(['page_count' => 20, 'total_count' => $query->count()]);
        $data['infos'] = $query->orderBy('id desc')->with('user')->offset(Pager::offset())->limit(Pager::limit())->asArray()->all();
        return $this->json($data);
    }

    /**
     * @name show recharge order information
     * @param id int by get request
     * @return string
     */
    public function actionRechargeDetail()
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
