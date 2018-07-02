<?php

namespace admin\controllers;

use common\helpers\Pager;
use common\helpers\Checker;
use common\models\UserAccountChange;

class FinanceController extends Controller {

    public $parent = 'finance';

    /**W
     * @name showing user account change list
     * @return string
     */
    public function actionAccountChangeList()
    {
        if( ! $this->request->isAjax) {
            return $this->render('account-change-list');
        }
        $params = $this->request->post();
        $rules = [
            'param' => [
                'user_id' => ['用户编号', ['int', 'required']],
                'type' => ['类型', ['inkey' => UserAccountChange::$changeSelector]],
                'start' => ['开始时间', ['date' => 'Y-m-d']],
                'end' => ['结束时间', ['date' => 'Y-m-d']],
            ],
        ];
        $checker = Checker::authentication($rules, $params);
        if($checker['code'] != SuccessCode) {
            return $this->json('Param.Error', $checker['message']);
        }
        foreach(['start', 'end'] as $key) {
            $params[$key] = strtotime($params[$key]);
        }
        $params['deleted_at'] = 0;
        $query = UserAccountChange::filterConditions(UserAccountChange::initCondition(['user_id', 'type', ['created_at', '>=', 'start'], ['created_at', '<=', 'end'], 'status', 'deleted_at'], $params));
        $data['page'] = Pager::page(['page_count' => 20, 'total_count' => $query->count()]);
        $data['infos'] = $query->orderBy('id desc')->with('user')->offset(Pager::offset())->limit(Pager::limit())->asArray()->all();
        return $this->json($data);
    }
}
