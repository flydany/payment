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
 * 短信发送服务
 */
class SmsCaptcha extends Sms {
    
    // 缓存前缀
    const SessionPrefix = 'captcha_sms_';
    public $sessionKey;
    
    const TemplateRegister = 'register';
    const TemplateChangeMobile = 'change-mobile';
    const TemplateChangePassword = 'change-password';
    const TemplateInformation = 'user-information';
    // 短信模板
    public static $templateSelector = [
        self::TemplateRegister,
        self::TemplateChangeMobile,
        self::TemplateChangePassword,
        self::TemplateInformation,
    ];

    // 短信验证码
    private $captcha;
    
    /**
     * 设置Session缓存Key
     * @param $sessionKey string 缓存Key
     * @return $this
     */
    private function setSessionKey($sessionKey)
    {
        $this->sessionKey = $sessionKey;
        return $this;
    }
    
    /**
     * 生成随机验证码
     * @return $this
     */
    private function generateCaptcha()
    {
        $this->captcha = rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9).rand(0, 9);
        return $this;
    }
    
    /**
     * 设置Session
     * @return boolean
     */
    private function setSession()
    {
        return \Yii::$app->session->set(static::SessionPrefix.$this->sessionKey, $this->captcha);
    }
    
    /**
     * 静态调用发送短验验证码
     * @param $mobile string 手机号
     * @param $type string 短信类型
     * @param $params string 模板参数
     * @return boolean
     */
    public static function captcha($mobile, $type = '')
    {
        $sender = new static();
        $sender->setSessionKey($type)->generateCaptcha()->setSession();
        $sender->response = $sender->baseApi->send($mobile, $type, ['code' => $sender->captcha]);
        if($sender->success()) {
            return true;
        }
        return false;
    }
    /**
     * 校验captcha是否准确
     * @param $captcha string 验证码
     * @param $key string 验证key
     * @return boolean
     */
    public static function validate($captcha, $key)
    {
        $sessionCode = \Yii::$app->session->get(static::SessionPrefix.$key);
        // 如果校验通过，删除验证码（同一个验证码只能使用一次）
        if(strtolower($sessionCode) == strtolower($captcha)) {
            \Yii::$app->session->remove(static::SessionPrefix.$key);
            return true;
        }
        return false;
    }
}