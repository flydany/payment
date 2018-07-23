<?php

namespace admin\controllers;

use Yii;
use common\helpers\Render;
use common\helpers\Checker;
use common\models\Platform;
use common\models\Merchant;

class PlatformController extends Controller {
    
    /**
     * this action showing merchant list
     * @param request type request->isAjax?
     * @return html|json
     */
    public function actionMerchantList()
    {
        if( ! $this->request->isAjax) {
            return $this->render('merchant-list');
        }
        $params = $this->request->post();
        $params['deleted_at'] = '0';
        $query = Merchant::filters(['id', ['title', 'like'], 'paytype', 'status', 'deleted_at'], $params);
        $pagination = Render::pagination((clone $query)->count());
        $data['infos'] = $query->orderBy('id desc')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data['page'] = Render::pager($pagination);
        return $this->json($data);
    }
    
    /**
     * show merchant detail
     * @param id int  - merchant id by get request
     * @return string
     */
    public function actionMerchantDetail()
    {
        $merchant = null;
        $merchantId = $this->request->get('id');
        if($merchantId && ( ! $merchant = Merchant::finder($merchantId))) {
            return $this->error('invalid merchant', 'merchant/merchant-list');
        }
        if($merchant && empty($merchant->hasPermission)) {
            return $this->error('permission forbidden', 'merchant/merchant-list');
        }
        return $this->render('merchant-detail', ['data' => $merchant]);
    }
    /**
     * insert merchant
     */
    public function actionMerchantInsert()
    {
        $merchant = new Merchant();
        if ( ! $merchant->loadAttributes($this->request->post())->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($merchant->errors(), 'merchant/merchant-detail');
        }
        if ($merchant->save()) {
            // 保存成功
            return $this->success('merchant ('.$merchant->title.') insert successful', [
                ['title' => 'go to merchant list page', 'url' => 'merchant/merchant-list'],
                ['title' => 'edit merchant again', 'url' => 'merchant/merchant-detail?id='.$merchant->id]
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('merchant ('.$merchant->title.') insert failed, please try again', 'merchant/merchant-detail');
    }
    /**
     * merchant detail show / update
     * use get(id) to find merchant
     */
    public function actionMerchantUpdate()
    {
        /* @var $merchant Merchant */
        // id 为必填项，判断管理员存在状态
        // 未得到，渲染错误页面
        if( ! $merchant = Merchant::finder($this->request->get('id'))) {
            return $this->error('invalid merchant', 'merchant/merchant-list');
        }
        if($merchant && empty($merchant->hasPermission)) {
            return $this->error('permission forbidden', 'merchant/merchant-list');
        }
        if ( ! $merchant->loadAttributes($this->request->post())->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($merchant->errors(), 'merchant/merchant-detail?id='.$merchant->id);
        }
        if ($merchant->save()) {
            // 保存成功
            return $this->success('merchant ('.$merchant->title.') update successful', [
                ['title' => 'go to merchant list page', 'url' => 'merchant/merchant-list'],
                ['title' => 'edit merchant again', 'url' => 'merchant/merchant-detail?id='.$merchant->id],
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('merchant ('.$merchant->title.') update failed, please try again.', 'merchant/merchant-detail?id='.$merchant->id);
    }
    /**
     * delete merchant
     */
    public function actionMerchantDelete()
    {
        return $this->json('system.error', 'it is not allowed to delete.');

        if( ! ($ids = $this->request->post('id'))) {
            return $this->json('invalid.param', 'you must choice at least one merchant.');
        }
        if(Merchant::trashAll(['id' => $ids])) {
            return $this->json(SuccessCode, 'merchant delete successful.');
        }
        return $this->json('system.error', 'merchant delete failed.');
    }
}