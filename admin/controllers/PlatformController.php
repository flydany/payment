<?php

namespace admin\controllers;

use Yii;
use yii\web\UploadedFile;
use common\helpers\Render;
use common\models\Platform;
use common\models\Merchant;
use common\models\MerchantBank;
use common\models\MerchantBankMaintain;
use common\models\AdminResource;

class PlatformController extends Controller {

    // 访问白名单
    public $whiteList = ['file-encode'];

    /*********************************************************************************/
    /************** platform  *******************************************************/
    /*********************************************************************************/
    /**
     * this action showing platform list
     * @param request type request->isAjax?
     * @return html|json
     */
    public function actionList()
    {
        $powers = array_unique(
            AdminResource::find()->select('power')
                ->where(['identity' => Yii::$app->admin->identity, 'type' => AdminResource::TypePlatform, 'power' => array_keys(Platform::$platformSelector)])
                ->column()
        );
        return $this->render('list', ['powers' => $powers]);
    }

    /*********************************************************************************/
    /************** merchant  *******************************************************/
    /*********************************************************************************/
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
        $query = Merchant::filters(['id', ['title', 'like'], 'platform_id', 'merchant_number', 'paytype', 'status', 'deleted_at'], $params)->filterResource(AdminResource::TypePlatform);
        // return $this->v($query->createCommand()->getRawSql());
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
            return $this->error('invalid merchant', 'platform/merchant-list');
        }
        if($merchant && empty($merchant->hasPermission)) {
            return $this->error('permission forbidden', 'platform/merchant-list');
        }
        return $this->render('merchant-detail', ['data' => $merchant]);
    }
    /**
     * insert merchant
     */
    public function actionMerchantInsert()
    {
        $merchant = new Merchant();
        $params = $this->request->post();
        $parameters = [];
        foreach($params['parameter_name'] as $k => $name) {
            if(empty($name)) {
                continue;
            }
            $parameters[$name] = $params['parameter_value'][$k];
        }
        $params['parameters'] = json_encode($parameters);
        if ( ! $merchant->loadAttributes($params)->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($merchant->errors(), 'platform/merchant-detail');
        }
        if ($merchant->save()) {
            // 保存成功
            return $this->success('merchant ('.$merchant->title.') insert successful', [
                ['title' => 'go to merchant list page', 'url' => 'platform/merchant-list'],
                ['title' => 'edit merchant again', 'url' => 'platform/merchant-detail?id='.$merchant->id]
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('merchant ('.$merchant->title.') insert failed, please try again', 'platform/merchant-detail');
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
            return $this->error('invalid merchant', 'platform/merchant-list');
        }
        if(empty($merchant->hasPermission)) {
            return $this->error('permission denied for platform: '.Platform::$platformSelector[$merchant->platform_id].', merchant_number: '.$merchant->merchant_number, 'platform/merchant-list');
        }
        $params = $this->request->post();
        $parameters = [];
        foreach($params['parameter_name'] as $k => $name) {
            if(empty($name)) {
                continue;
            }
            $parameters[$name] = $params['parameter_value'][$k];
        }
        $params['parameters'] = json_encode($parameters);
        if ( ! $merchant->loadAttributes($params)->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($merchant->errors(), 'platform/merchant-detail?id='.$merchant->id);
        }
        if(empty($merchant->hasPermission)) {
            return $this->error('permission denied for platform: '.Platform::$platformSelector[$merchant->platform_id].', merchant_number: '.$merchant->merchant_number, 'platform/merchant-list');
        }
        if ($merchant->save()) {
            // 保存成功
            return $this->success('merchant ('.$merchant->title.') update successful', [
                ['title' => 'go to merchant list page', 'url' => 'platform/merchant-list'],
                ['title' => 'edit merchant again', 'url' => 'platform/merchant-detail?id='.$merchant->id],
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('merchant ('.$merchant->title.') update failed, please try again.', 'platform/merchant-detail?id='.$merchant->id);
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
    /***
     * 公私钥读取
     * @param $upload file
     * @return base64_encode string
     */
    function actionFileEncoder()
    {
        $file = UploadedFile::getInstanceByName('upload');
        if(empty($file)) {
            return $this->json('File.Exists', 'file objects do not exist!');
        }
        $reader = base64_encode(file_get_contents($file->tempName));
        $exts = explode('.', $file->name);
        return $this->json(['reader' => $reader, 'ext' => end($exts)]);
    }

    /*********************************************************************************/
    /************** merchant banks *************************************************/
    /*********************************************************************************/
    /**
     * this action showing merchant bank list
     * @param request type request->isAjax?
     * @return html|json
     */
    public function actionBankList()
    {
        if( ! $this->request->isAjax) {
            return $this->render('bank-list');
        }
        $params = $this->request->post();
        $params['deleted_at'] = '0';
        $query = MerchantBank::filters(['id', 'platform_id', 'merchant_number', 'paytype', 'status', 'deleted_at'], $params)->filterResource(AdminResource::TypePlatform);
        $pagination = Render::pagination((clone $query)->count());
        $data['infos'] = $query->orderBy(['id' => 'desc'])->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data['page'] = Render::pager($pagination);
        return $this->json($data);
    }

    /**
     * show merchant bank detail
     * @param id int  - merchant bank id by get request
     * @return string
     */
    public function actionBankDetail()
    {
        $bank = null;
        $bankId = $this->request->get('id');
        if($bankId && ( ! $bank = MerchantBank::finder($bankId))) {
            return $this->error('invalid merchant bank', 'platform/bank-list');
        }
        if($bank && empty($bank->hasPermission)) {
            return $this->error('permission forbidden', 'platform/bank-list');
        }
        return $this->render('bank-detail', ['data' => $bank]);
    }
    /**
     * insert merchant bank
     */
    public function actionBankInsert()
    {
        $bank = new MerchantBank();
        $params = $this->request->post();
        foreach(['single_amount', 'day_amount', 'month_amount'] as $key) {
            $params[$key] = bcmul($params[$key], 100, 0);
        }
        foreach(['weekday', 'weekend', 'holiday'] as $key) {
            $times = [];
            foreach($params[$key.'_start'] as $k => $start) {
                if(empty($start) || empty($params[$key.'_end'][$k])) {
                    $params[$key.'_times'] = '';
                    continue;
                }
                $times[] = ['start' => $start, 'end' => $params[$key.'_end'][$k]];
            }
            $params[$key.'_times'] = json_encode($times);
        }
        if ( ! $bank->loadAttributes($params)->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($bank->errors(), 'platform/bank-detail');
        }
        if(empty($bank->hasPermission)) {
            return $this->error('permission forbidden', 'platform/bank-list');
        }
        if ($bank->save()) {
            // 保存成功
            return $this->success('bank ('.Platform::$bankSelector[$bank->bank_id].') insert successful', [
                ['title' => 'go to merchant bank list page', 'url' => 'platform/bank-list'],
                ['title' => 'edit merchant bank again', 'url' => 'platform/bank-detail?id='.$bank->id]
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('bank ('.Platform::$bankSelector[$bank->bank_id].') insert failed, please try again', 'platform/bank-detail');
    }
    /**
     * merchant bank detail show / update
     * use get(id) to find merchant bank
     */
    public function actionBankUpdate()
    {
        /* @var $bank Bank */
        // id 为必填项，判断管理员存在状态
        // 未得到，渲染错误页面
        if( ! $bank = MerchantBank::finder($this->request->get('id'))) {
            return $this->error('invalid merchant bank', 'platform/bank-list');
        }
        if($bank && empty($bank->hasPermission)) {
            return $this->error('permission forbidden', 'platform/bank-list');
        }
        $params = $this->request->post();
        foreach(['single_amount', 'day_amount', 'month_amount'] as $key) {
            $params[$key] = bcmul($params[$key], 100, 0);
        }
        foreach(['weekday', 'weekend', 'holiday'] as $key) {
            $times = [];
            foreach($params[$key.'_start'] as $k => $start) {
                if(empty($start) || empty($params[$key.'_end'][$k])) {
                    $params[$key.'_times'] = '';
                    continue;
                }
                $times[] = ['start' => $start, 'end' => $params[$key.'_end'][$k]];
            }
            $params[$key.'_times'] = json_encode($times);
        }
        if ( ! $bank->loadAttributes($params)->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($bank->errors(), 'platform/bank-detail?id='.$bank->id);
        }
        if(empty($bank->hasPermission)) {
            return $this->error('permission forbidden', 'platform/bank-list');
        }
        if ($bank->save()) {
            // 保存成功
            return $this->success('bank ('.Platform::$bankSelector[$bank->bank_id].') update successful', [
                ['title' => 'go to merchant bank list page', 'url' => 'platform/bank-list'],
                ['title' => 'edit merchant bank again', 'url' => 'platform/bank-detail?id='.$bank->id],
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('bank ('.Platform::$bankSelector[$bank->bank_id].') update failed, please try again.', 'platform/bank-detail?id='.$bank->id);
    }
    /**
     * delete merchant bank
     */
    public function actionBankDelete()
    {
        return $this->json('system.error', 'it is not allowed to delete.');

        if( ! ($ids = $this->request->post('id'))) {
            return $this->json('invalid.param', 'you must choice at least one merchant bank.');
        }
        if(MerchantBank::trashAll(['id' => $ids])) {
            return $this->json(SuccessCode, 'bank delete successful.');
        }
        return $this->json('system.error', 'bank delete failed.');
    }

    /*********************************************************************************/
    /************** merchant maintain  **********************************************/
    /*********************************************************************************/
    /**
     * this action showing merchant maintain list
     * @param request type request->isAjax?
     * @return html|json
     */
    public function actionMaintainList()
    {
        if( ! $this->request->isAjax) {
            return $this->render('maintain-list');
        }
        $params = $this->request->post();
        $params['deleted_at'] = '0';
        $query = MerchantBankMaintain::filters(['id', 'platform_id', 'merchant_number', 'paytype', 'status', 'deleted_at'], $params)->filterResource(AdminResource::TypePlatform);
        $pagination = Render::pagination((clone $query)->count());
        $data['infos'] = $query->orderBy(['id' => 'desc'])->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        $data['page'] = Render::pager($pagination);
        return $this->json($data);
    }

    /**
     * show merchant maintain detail
     * @param id int  - merchant maintain id by get request
     * @return string
     */
    public function actionMaintainDetail()
    {
        $maintain = null;
        $maintainId = $this->request->get('id');
        if($maintainId && ( ! $maintain = MerchantBankMaintain::finder($maintainId))) {
            return $this->error('invalid merchant maintain', 'platform/maintain-list');
        }
        if($maintain && empty($maintain->hasPermission)) {
            return $this->error('permission forbidden', 'platform/maintain-list');
        }
        return $this->render('maintain-detail', ['data' => $maintain]);
    }
    /**
     * insert merchant maintain
     */
    public function actionMaintainInsert()
    {
        $maintain = new MerchantBankMaintain();
        $params = $this->request->post();
        foreach(['single_amount', 'day_amount', 'month_amount'] as $key) {
            $params[$key] = bcmul($params[$key], 100, 0);
        }
        foreach(['begin_at', 'finish_at'] as $key) {
            $params[$key] = strtotime($params[$key]);
        }
        $times = [];
        foreach($params['start'] as $k => $start) {
            if(empty($start) || empty($params['end'][$k])) {
                $params['times'] = '';
                continue;
            }
            $times[] = ['start' => $start, 'end' => $params['end'][$k]];
        }
        $params['times'] = json_encode($times);
        if ( ! $maintain->loadAttributes($params)->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($maintain->errors(), 'platform/maintain-detail');
        }
        if(empty($maintain->hasPermission)) {
            return $this->error('permission forbidden', 'platform/maintain-list');
        }
        if ($maintain->save()) {
            // 保存成功
            return $this->success('maintain ('.Platform::$platformSelector[$maintain->platform_id].') insert successful', [
                ['title' => 'go to merchant maintain list page', 'url' => 'platform/maintain-list'],
                ['title' => 'edit merchant maintain again', 'url' => 'platform/maintain-detail?id='.$maintain->id]
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('maintain ('.Platform::$platformSelector[$maintain->platform_id].') insert failed, please try again', 'platform/maintain-detail');
    }
    /**
     * merchant maintain detail show / update
     * use get(id) to find merchant maintain
     */
    public function actionMaintainUpdate()
    {
        /* @var $maintain Maintain */
        // id 为必填项，判断管理员存在状态
        // 未得到，渲染错误页面
        if( ! $maintain = MerchantBankMaintain::finder($this->request->get('id'))) {
            return $this->error('invalid merchant maintain', 'platform/maintain-list');
        }
        if($maintain && empty($maintain->hasPermission)) {
            return $this->error('permission forbidden', 'platform/maintain-list');
        }
        $params = $this->request->post();
        foreach(['single_amount', 'day_amount', 'month_amount'] as $key) {
            $params[$key] = bcmul($params[$key], 100, 0);
        }
        foreach(['begin_at', 'finish_at'] as $key) {
            $params[$key] = strtotime($params[$key]);
        }
        $times = [];
        foreach($params['start'] as $k => $start) {
            if(empty($start) || empty($params['end'][$k])) {
                $params['times'] = '';
                continue;
            }
            $times[] = ['start' => $start, 'end' => $params['end'][$k]];
        }
        $params['times'] = json_encode($times);
        if ( ! $maintain->loadAttributes($params)->validate()) {
            // 参数异常，渲染错误页面
            return $this->error($maintain->errors(), 'platform/maintain-detail?id='.$maintain->id);
        }
        if(empty($maintain->hasPermission)) {
            return $this->error('permission forbidden', 'platform/maintain-list');
        }
        if ($maintain->save()) {
            // 保存成功
            return $this->success('maintain ('.Platform::$platformSelector[$maintain->platform_id].') update successful', [
                ['title' => 'go to merchant maintain list page', 'url' => 'platform/maintain-list'],
                ['title' => 'edit merchant maintain again', 'url' => 'platform/maintain-detail?id='.$maintain->id],
            ]);
        }
        // 参数异常，渲染错误页面
        return $this->error('maintain ('.Platform::$platformSelector[$maintain->platform_id].') update failed, please try again.', 'platform/maintain-detail?id='.$maintain->id);
    }
    /**
     * delete merchant maintain
     */
    public function actionMaintainDelete()
    {
        return $this->json('system.error', 'it is not allowed to delete.');

        if( ! ($ids = $this->request->post('id'))) {
            return $this->json('invalid.param', 'you must choice at least one merchant maintain.');
        }
        if(MerchantBankMaintain::trashAll(['id' => $ids])) {
            return $this->json(SuccessCode, 'maintain delete successful.');
        }
        return $this->json('system.error', 'maintain delete failed.');
    }
}
