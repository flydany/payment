<?php
/**
 * Created by PhpStorm.
 * User: flydany
 * Date: 2018/2/3
 * Time: 13:04
 */
namespace common\api\jiguang;

use Yii;
use JiGuang\JSMS;
use common\api\BaseApi;
use common\server\sms\SmsCaptcha;

/**
 * @name 极光短信提供商API
 */
class JiGuang extends BaseApi {
    
    // 短信验证码发送模板
    const TemplateRegister = 1;
    const TemplateChangeMobile = 3;
    const TemplateChangePassword = 4;
    const TemplateInformation = 5;
    
    // 状态规范
    const RequestSuccess = '200';
    const RequestFailed = '500';
    public $requestSuccessCodes = [self::RequestSuccess];
    public $statusSuccessCodes = [self::RequestSuccess];
    
    // 请求参数配置
    public $requestCodeKey = 'status';
    public $requestMessageKey = '';
    public $statusCodeKey = 'status';
    public $statusErrorCodeKey = 'status';
    public $statusErrorMessageKey = '';
    
    // 短信模板
    public static $templateSelector = [
        SmsCaptcha::TemplateRegister => self::TemplateRegister,
        SmsCaptcha::TemplateChangeMobile => self::TemplateChangeMobile,
        SmsCaptcha::TemplateChangePassword => self::TemplateChangePassword,
        SmsCaptcha::TemplateInformation => self::TemplateInformation,
    ];
    
    // 短信提供商API
    public $smsApi;
    
    // 加密key
    public $key;
    
    public function __construct($config)
    {
        $this->setPartnerId($config['partnerId'])->setKey($config['key']);
        
        // 初始化短信发送接口
        $this->smsApi = new JSMS($this->partnerId, $this->key);
    }
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }
    
    /**
     * 发送短信
     * @param $mobile string 接收手机号
     * @param $template integer 模板编号
     * @param $params array 发送参数
     * @return array
     */
    public function send($mobile, $template, $params)
    {
        $response = $this->smsApi->sendMessage($mobile, $this->template($template), $params);
        Yii::info('发送短信['.$mobile.']:'.json_encode($response));
        if($response['http_code'] == static::RequestSuccess) {
            $this->setStatus(static::RequestSuccess, 'Success');
            $this->status = static::RequestSuccess;
        }
        else {
            $this->setStatus(static::RequestFailed, 'Send Failed');
            $this->status = static::RequestFailed;
        }
        $this->response = ['code' => $this->code, 'message' => $this->message, 'response' => ['status' => $this->status]];
        return $this->response;
    }
    
    /**
     * 发送短信
     * @param $mobile string 接收手机号
     * @param $template integer 模板编号
     * @param $params array 发送参数
     * @return array
     */
    public function sendCode($mobile, $template, $params)
    {
        $response = $this->smsApi->sendCode($mobile, $this->template($template), $params);
        Yii::info('发送短信['.$mobile.']:'.json_encode($response));
        if($response['http_code'] == static::RequestSuccess) {
            $this->setStatus(static::RequestSuccess, 'Success');
            $this->status = static::RequestSuccess;
        }
        else {
            $this->setStatus(static::RequestFailed, 'Send Failed');
            $this->status = static::RequestFailed;
        }
        $this->response = ['code' => $this->code, 'message' => $this->message, 'response' => ['status' => $this->status]];
        return $this->response;
    }
    
    /**
     * 组织短验提示内容
     * @param $type string 提示类型
     * @return mixed
     */
    public function template($type)
    {
        $template = static::$templateSelector[$type];
        // $message = str_replace(':captcha', $this->captcha, $template);
        return $template;
    }
}