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
use common\models\Recharge;

/**
 * @name 充值服务
 */
class RechargeServer implements RechargeInterface {
    
    // 第三方
    public $platform;
    
    // 充值服务提供商
    public $baseServer;

    public function __construct($platform)
    {
        $this->platform = $platform;
        
        $this->init();
    }

    /**
     * 申请充值
     * @param $params array 充值参数
     * @return array
     */
    public function recharge($params)
    {
        $response = $this->baseServer->recharge($params);
        $response['payment'] = $this->baseServer->payment;
        return $response;
    }

    /**
     * 充值订单状态查询
     * @param $params array 查询参数
     * @return array
     */
    public function rechargeQuery($params)
    {
        $response = $this->baseServer->rechargeQuery($params);
        return $response;
    }
    
    /**
     * 是否成功
     * @return boolean
     */
    public function success()
    {
        return $this->baseServer->success();
    }
    
    /**
     * 是否失败
     * @return boolean
     */
    public function failed()
    {
        return $this->baseServer->failed();
    }
    
    /**
     * 是否处理中
     * @return boolean
     */
    public function dealing()
    {
        return $this->baseServer->dealing();
    }
    
    /**
     * 处理订单回调
     * @return boolean
     */
    public function notify($params)
    {
        return $this->baseServer->notify($params);
    }
    
    /**
     * 初始化服务相关配置
     * @return Object
     * @throws \Exception
     */
    public function init()
    {
        // 初始化服务提供商
        switch($this->platform) {
            case Recharge::PlatformAlipay: {
                $this->baseServer = new AlipayServer();
            } break;
            case Recharge::PlatformWeChat: {
                $this->baseServer = new WechatServer();
            } break;
            default: {
                throw new \Exception('unknow platform');
            }
        }
        return $this->baseServer;
    }
}