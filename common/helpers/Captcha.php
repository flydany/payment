<?php

namespace common\helpers;

use Yii;

// 图形验证码
class Captcha {

    // 缓存前缀
    const SessionPrefix = 'captcha_image_';
    // 随机因子
    private $randomCharset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
    private $code;
    private $length = 4;
    private $width = 130;
    private $height = 50;
    private $img;
    private $font;
    private $fontSize = 20;
    private $fontColor;
    private $sessionKey = 'admin_default';

    // 构造方法初始化
    public function __construct($config = [])
    {
        $this->font = Yii::getAlias('@common/font/GOUDYSTO.TTF');
        // 初始化配置
        if( ! empty($config)) {
            foreach(['length', 'width', 'height', 'fontSize', 'sessionKey', 'font'] as $key) {
                if(isset($config[$key])) {
                    $this->{$key} = $config[$key];
                }
            }
        }
    }

    /**
     * 生成随机码
     * @return $this
     */
    private function createCode()
    {
        $_len = strlen($this->randomCharset) - 1;
        for ($i = 0; $i < $this->length; ++$i) {
            $this->code .= $this->randomCharset[mt_rand(0, $_len)];
        }
        $this->setSession();
        return $this;
    }

    /**
     * 设置Session
     * @return boolean
     */
    private function setSession()
    {
        return \Yii::$app->session->set(static::SessionPrefix.$this->sessionKey, $this->code);
    }
    /**
     * 校验captcha是否准确
     * @return boolean
     */
    public static function validate($code, $key)
    {
        $sessionCode = \Yii::$app->session->get(static::SessionPrefix.$key);
        // 如果校验通过，删除验证码（同一个验证码只能使用一次）
        if(strtolower($sessionCode) == strtolower($code)) {
            \Yii::$app->session->remove(static::SessionPrefix.$key);
            return true;
        }
        return false;
    }
    
    /**
     * 生成背景图片
     * @return $this
     */
    private function createBackground()
    {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color = imagecolorallocate($this->img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
        return $this;
    }
    /**
     * 生成文字
     * @return $this
     */
    private function createText()
    {
        $_x = $this->width / $this->length;
        for ($i = 0; $i < $this->length; ++$i) {
            $this->fontColor = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imagettftext($this->img, $this->fontSize, mt_rand(-30, 30), $_x * $i + mt_rand(1, 5), $this->height / 1.4, $this->fontColor, $this->font, $this->code[$i]);
        }
        return $this;
    }
    /**
     * 生成线条、雪花
     * @return $this
     */
    private function createLine()
    {
        //线条
        for ($i = 0; $i < 6; ++$i) {
            $color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline($this->img, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, $this->width), mt_rand(0, $this->height), $color);
        }
        //雪花
        for ($i = 0; $i < 100; ++$i) {
            $color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring($this->img, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '*', $color);
        }
        return $this;
    }
    /**
     * 输出图形验证码
     * @return $this
     */
    private function outPut()
    {
        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
    }
    /**
     * 对外生成
     * @return $this
     */
    public function doimg()
    {
        $this->createBackground();
        $this->createCode();
        $this->createLine();
        $this->createText();
        $this->outPut();
    }
    /**
     * 获取验证码
     * @return $this
     */
    public function getCode()
    {
        return strtolower($this->code);
    }

    /**
     * 生成图形验证码
     * @return data
     */
    public static function send($keyName)
    {
        $captcha = new Captcha(['sessionKey' => $keyName]);
        $captcha->doimg();
    }
}