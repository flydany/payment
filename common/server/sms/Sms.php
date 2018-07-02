<?php
/**
 * Created by PhpStorm.
 * User: flydany
 * Date: 2018/2/3
 * Time: 12:58
 */
namespace common\server\sms;

use Yii;
use common\api\sms\JiGuang;

/**
 * @name 短信发送服务
 */
class Sms {
    
    // 服务提供方API
    public $baseApi;
    
    // 返回数据
    public $response;
    
    
    public function __construct()
    {
        $this->baseApi = new JiGuang(Yii::$app->params['jiguang']);
    }
    
    /**
     * 发送短信
     * @param $mobile string 手机号
     * @param $template string 短信内容
     * @param $params array 模板参数
     * @return array
     */
    public function send($mobile, $template, $params = [])
    {
        return $this->baseApi->send($mobile, $template, $params);
    }
    
    /**
     * 判断是否发送成功
     * @return boolean
     */
    public function success()
    {
        // 判断请求状态是否成功
        if(in_array($this->baseApi->code, $this->baseApi->requestSuccessCodes)) {
            // 判断发送状态是否成功
            if(in_array($this->baseApi->status, $this->baseApi->statusSuccessCodes)) {
                return true;
            }
        }
        return false;
    }
}