<?php
/**
 * Created by PhpStorm.
 * User: flydany
 * Date: 2018/2/3
 * Time: 12:58
 */
namespace common\server\recharge;

use common\api\BaseApi;
use Yii;
use common\api\alipay\Alipay;

/**
 * 支付宝充值服务
 */
class AlipayServer extends BaseServer implements RechargeInterface {
    
    public $amountKey = 'total_amount';
    public $accountKey = 'buyer_id';
    
    /**
     * 初始系统配置参数
     * @return mixed|void
     */
    public function init()
    {
        $this->baseApi = new Alipay(Yii::$app->params['alipay']);
        $this->payment = static::PaymentForm;
    }

    /**
     * 组织充值接口请求参数
     * @param $params array 充值参数
     * @return array
     */
    public function rechargeParams($params)
    {
        $params['amount'] = bcdiv($params['amount'], 100, 2);
        return $params;
    }

    /**
     * 组织充值查询接口请求参数
     * @param $params array 查询参数
     * @return array
     */
    public function rechargeQueryParams($params)
    {
        return $params;
    }
}