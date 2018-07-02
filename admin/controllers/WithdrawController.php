<?php

namespace admin\controllers;

use common\helpers\Checker;
use common\helpers\Pager;
use common\models\Withdraw;
use common\models\WithdrawLog;

class WithdrawController extends Controller {

    public $parent = 'finance';
    
    /**
     * @name showing Withdraw order list
     * @return string
     */
    public function actionWithdrawList()
    {
        if( ! $this->request->isAjax) {
            return $this->render('withdraw-list');
        }
        $params = $this->request->post();
        $rules = [
            'param' => [
                'user_id' => ['用户编号', ['int']],
                'type' => ['充值方式', ['inkey' => Withdraw::$platformSelector]],
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
        $query = Withdraw::filterConditions(Withdraw::initCondition(['user_id', 'type', ['created_at', '>=', 'start'], ['created_at', '<=', 'end'], 'status', 'deleted_at'], $params));
        $data['page'] = Pager::page(['page_count' => 20, 'total_count' => $query->count()]);
        $data['infos'] = $query->orderBy('id desc')->with('user')->offset(Pager::offset())->limit(Pager::limit())->asArray()->all();
        return $this->json($data);
    }

    /**
     * @name show withdraw order information
     * @param id int by get request
     * @return string
     */
    public function actionWithdrawDetail()
    {
        if( ! $withdraw = Withdraw::finder($this->request->get('id'))) {
            return $this->error('无效的充值订单（Invalid Order）', 'withdraw/withdraw-list');
        }
        return $this->render('withdraw-detail', ['data' => $withdraw]);
    }

    /**
     * @name show withdraw log information
     * @param id int by get request
     * @return string
     */
    public function actionWithdrawLog()
    {
        $id = $this->request->get('id');
        if(empty($id)) {
            return $this->json('Invalid.Param', '无效的请求（Invalid Param）');
        }
        $query = WithdrawLog::find()->where(['withdraw_id' => $id]);
        $data['page'] = Pager::page(['page_count' => 50, 'total_count' => $query->count()]);
        $data['infos'] = $query->orderBy('id desc')->offset(Pager::offset())->limit(Pager::limit())->asArray()->all();
        return $this->json($data);
    }

    /**
     * @name 开始给用户打款
     * @param $id int 提现编号
     * @return mixed
     */
    public function actionWithdrawing()
    {
        if( ! $withdraw = Withdraw::finder($this->request->get('id'))) {
            return $this->error('无效的提现申请记录（Invalid Withdraw）', 'withdraw/withdraw-list');
        }
        if($withdraw->withdrawing($this->request->post('remark'))) {
            return $this->success('提现申请记录（'.$withdraw->id.'）设置打款中成功（Withdrawing Success）', [
                ['title' => '前往提现申请列表页', 'url' => 'withdraw/withdraw-list'],
                ['title' => '继续更新提现申请记录', 'url' => 'withdraw/withdraw-detail?id='.$withdraw->id]
            ]);
        }
        return $this->error('提现申请记录（'.$withdraw->id.'）设置打款中失败（Withdrawing Failed）', 'withdraw/withdraw-detail?id='.$withdraw->id);
    }

    /**
     * @name 给用户打款成功
     * @param $id int 提现编号
     * @return mixed
     */
    public function actionSuccess()
    {
        if( ! $withdraw = Withdraw::finder($this->request->get('id'))) {
            return $this->error('无效的提现申请记录（Invalid Withdraw）', 'withdraw/withdraw-list');
        }
        if($withdraw->success($this->request->post('remark'))) {
            return $this->success('提现申请记录（'.$withdraw->id.'）打款成功（Withdraw Success）', [
                ['title' => '前往提现申请列表页', 'url' => 'withdraw/withdraw-list'],
                ['title' => '继续更新提现申请记录', 'url' => 'withdraw/withdraw-detail?id='.$withdraw->id]
            ]);
        }
        return $this->error('提现申请记录（'.$withdraw->id.'）设置`打款成功`失败（Withdraw Failed）', 'withdraw/withdraw-detail?id='.$withdraw->id);
    }

    /**
     * @name 拒绝提现申请
     * @param $id int 提现编号
     * @return mixed
     */
    public function actionRefuse()
    {
        if( ! $withdraw = Withdraw::finder($this->request->get('id'))) {
            return $this->error('无效的提现申请记录（Invalid Withdraw）', 'withdraw/withdraw-list');
        }
        if($withdraw->refuse($this->request->post('remark'))) {
            return $this->success('提现申请记录（'.$withdraw->id.'）已拒绝（Refuse Success）', [
                ['title' => '前往提现申请列表页', 'url' => 'withdraw/withdraw-list'],
                ['title' => '继续更新提现申请记录', 'url' => 'withdraw/withdraw-detail?id='.$withdraw->id]
            ]);
        }
        return $this->error('提现申请记录（'.$withdraw->id.'）拒绝失败（Refuse Failed）', 'withdraw/withdraw-detail?id='.$withdraw->id);
    }
}
