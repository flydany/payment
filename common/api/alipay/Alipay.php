<?php
/**
 * Created by PhpStorm.
 * User: flydany
 * Date: 2018/2/3
 * Time: 13:04
 */
namespace common\api\alipay;

use Yii;
use common\api\BaseApi;

require_once 'pagepay/service/AlipayTradeService.php';
require_once 'pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';

/**
 * 支付宝API
 * Alipay Recharge API
 */
class Alipay extends BaseApi {
    
    // 请求状态码
    const RequestSuccess = 'TRADE_SUCCESS';
    public $requestSuccessCodes = [self::RequestSuccess];
    
    // 订单状态码
    const StatusSuccess = 'TRADE_SUCCESS';
    public $statusSuccessCodes = [self::StatusSuccess, 'TRADE_FINISHED'];
    public $statusFailedCodes = ['TRADE_CLOSED'];
    public $requestExistCodes = [];
    
    // 请求参数配置
    public $signKey = 'sign';
    public $requestCodeKey = 'trade_status';
    public $requestMessageKey = '';
    public $statusCodeKey = 'trade_status';
    public $statusErrorCodeKey = 'trade_status';
    public $statusErrorMessageKey = '';
    public $orderNumberKey = 'out_trade_no';
    // 不参与签名的字段
    public $unSignKeys = ['sign'];
    
    public $alipayApi;
    
    public function __construct($config)
    {
        $this->setRequestUri($config['requestUri']);
        $this->setPartnerId($config['partnerId']);
        // 设置商家公钥
        $this->setPublicKey($config['publicKey'], 'alipay');
        // 设置商户私钥
        $this->setPrivateKey($config['privateKey'], 'alipay');
        
        // 设置Alipay初始化Configuration
        $this->alipayApi = new \AlipayTradeService([
            'app_id' => $this->partnerId,
            'merchant_private_key' => $this->privateKey,
            'notify_url' => Yii::$app->request->getHostInfo().'/notify/alipay',
            'charset' => $this->charset,
            'sign_type' => 'RSA2',
            'gatewayUrl' => $this->requestUri,
            'alipay_public_key' => $this->publicKey,
        ]);
    }
    
    /**
     * 充值申请
     * @param $params array 充值参数
     * @return array
     */
    public function recharge($params)
    {
        // 构造参数
        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody('');
        $payRequestBuilder->setSubject('订单支付');
        $payRequestBuilder->setTotalAmount($params['amount']);
        $payRequestBuilder->setOutTradeNo($params['order_number']);
        
        /**
         * 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
         */
        $response = $this->alipayApi->pagePay($payRequestBuilder, isset($params['return_url']) ? $params['return_url'] : '', \Yii::$app->request->getHostInfo().'/notify/alipay');
        
        $this->status = self::StatusSuccess;
        $this->response = ['code' => self::StatusSuccess, 'message' => 'Success', 'response' => ['javascript' => $response]];
        return $this->response;
    }
    
    /**
     * 充值查询
     * @param $params array 查询参数
     * @return array
     */
    public function rechargeQuery($params)
    {
        $this->status = '0';
        $this->response = ['code' => SuccessCode, 'message' => 'Success'];
        return $this->response;
    }
    
    /**
     * 验证签名
     * @param $params array 待验签参数
     * @return boolean
     */
    public function verifySign($params)
    {
        return true;
    }
}