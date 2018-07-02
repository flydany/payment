<?php
/**
 * Created by PhpStorm.
 * User: flydany
 * Date: 2018/2/3
 * Time: 12:58
 */
namespace common\server\recharge;

use Yii;
use common\api\BaseApi;
use common\helpers\Render;
use common\models\UserBond;
use common\models\EmploymentRecharge;

/**
 * 充值接口
 */
abstract class BaseServer {
    
    // 充值方式常量
    const PaymentQrcode = 'qrcode';
    const PaymentForm = 'form';
    
    // 充值方式
    public $payment;
    
    // 服务提供方API
    public $baseApi;
    
    // 返回数据
    public $code;
    public $message;
    public $response;
    public $amountKey;
    public $accountKey;
    
    public function __construct()
    {
        $this->init();
    }
    abstract function init();
    
    // 设置处理状态
    public function setStatus($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
        return $this;
    }
    public function getMessage()
    {
        return $this->message;
    }
    // 设置返回数据
    public function setResponse($response)
    {
        $this->setStatus($response['code'], $response['message']);
        $this->response = $response['response'];
        return $this;
    }
    
    /**
     * 申请充值
     * @param $params array 支付参数
     * @return array
     */
    public function recharge($params)
    {
        $this->setResponse($this->baseApi->recharge($this->rechargeParams($params)));
        $this->response['payment'] = $this->payment;
        return $this->response;
    }
    public function rechargeParams($params)
    {
        return $params;
    }
    
    /**
     * 订单回调
     * @param $params array 回调参数
     * @return boolean
     */
    public function notify($params)
    {
        $this->baseApi->responseBody = $params;
        $this->baseApi->setStatus('200', 'Success');
        $this->baseApi->parseResponse();
        if(empty($this->baseApi->orderNumber)) {
            return true;
        }
        $this->setResponse($this->baseApi->response);
        // 获取充值订单
        if( ! ($recharge = $this->rechargeOrder($this->baseApi->orderNumber))) {
            Yii::info('未知的订单类型');
            throw new \Exception('unknow recharge order type');
        }
        if($this->success()) {
            if($recharge->amount == Render::value($this->response, $this->amountKey)) {
                if($recharge->success(Render::value($this->response, $this->accountKey), Render::value($this->response, $this->amountKey))) {
                    return true;
                }
                return false;
            }
            Yii::info('订单金额不匹配');
            throw new \Exception('amount don\'t match');
        }
        else if($this->failed()) {
            if($recharge->failed(Render::value($this->response, $this->accountKey))) {
                return true;
            }
            return false;
        }
    }
    
    /**
     * 根据订单号获取充值订单
     * @param $orderNumber string 订单号
     * @return object
     * @throws \Exception
     */
    public function rechargeOrder($orderNumber)
    {
        $recharge = null;
        // 设计师支付保证金订单
        if(substr($orderNumber, 0, 2) == UserBond::SnPrefix) {
            /** @var UserBond $recharge */
            $recharge = UserBond::find()->where(['order_number' => $orderNumber])->one();
            if(empty($recharge)) {
                Yii::info('订单不存在');
                throw new \Exception('order not exists');
            }
            if( ! in_array($recharge->status, [UserBond::StatusInit])) {
                Yii::info('状态不能被修改');
                throw new \Exception('status can\'t be rechange');
            }
        }
        // 雇主支付雇佣费用订单
        else if(substr($orderNumber, 0, 2) == EmploymentRecharge::SnPrefix) {
            /** @var EmploymentRecharge $recharge */
            $recharge = EmploymentRecharge::find()->where(['order_number' => $orderNumber])->one();
            if(empty($recharge)) {
                Yii::info('订单不存在');
                throw new \Exception('order not exists');
            }
            if( ! in_array($recharge->status, [EmploymentRecharge::StatusInit, EmploymentRecharge::StatusPaying])) {
                Yii::info('状态不能被修改');
                throw new \Exception('status can\'t be rechange');
            }
        }
        return $recharge;
    }
    
    /**
     * 充值订单状态查询
     * @param $params array 查询参数
     * @return mixed
     */
    public function rechargeQuery($params)
    {
        $this->setResponse($this->baseApi->recharge($this->rechargeQueryParams($params)));
        return $this->response;
    }
    public function rechargeQueryParams($params)
    {
        return $params;
    }
    
    /**
     * 是否成功
     * @return boolean
     */
    public function success()
    {
        // 请求异常
        if(in_array($this->code, [BaseApi::RequestIgnore])) {
            return false;
        }
        // 判断请求状态是否成功
        if(in_array($this->code, $this->baseApi->requestSuccessCodes)) {
            // 判断发送状态是否成功
            if(in_array($this->baseApi->status, $this->baseApi->statusSuccessCodes)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * 是否失败
     * @return boolean
     */
    public function failed()
    {
        // 请求异常
        if(in_array($this->code, [BaseApi::RequestIgnore])) {
            return false;
        }
        // 订单不存在校验
        if(in_array($this->code, $this->baseApi->requestExistCodes)) {
            return true;
        }
        // 判断请求状态是否成功
        if(in_array($this->code, $this->baseApi->requestSuccessCodes)) {
            // 判断发送状态是否成功
            if(in_array($this->baseApi->status, $this->baseApi->statusFailedCodes)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * 是否处理中
     * @return boolean
     */
    public function dealing()
    {
        return true;
    }
}