<?php

namespace common\components;

use common\helpers\Checker;

class Request extends \yii\web\Request {

    /**
     * @name 获得包含host的baseUrl地址
     * @return string
     */
    public function getAbsoluteBaseUrl()
    {
        return $this->getHostInfo() . $this->getBaseUrl();
    }

    // use key->database key to return data array
    public function getParamsConversion($relate, $method = 'post')
    {
        $data = [];
        foreach($relate as $key => $p) {
            $default = '';
            if(Checker::checker_int($key) === true) {
                $key = $p;
            }
            if(is_array($p)) {
                $key = $p[0];
                $default = $p[1];
            }
            $value = call_user_func([$this, $method], $key);
            if(($value === '' || $value === null) && $default) {
                $value = $default;
            }
            $data[$p] = $value;
        }
        return $data;
    }

    // use relate->param to return data array
    public function getParams($relate, $method = 'post')
    {
        $data = [];
        foreach($relate as $key => $p) {
            // $value = Yii::$app->request->{$method}($key);
            $value = call_user_func([$this, $method], $key);
            if(($value === '' || $value === null) && isset($p['default'])) {
                $value = $p['default'];
            }
            $data[$key] = $value;
        }
        return $data;
    }

    // get | post request param
    public function find($key)
    {
        $value = $this->get($key);
        if(empty($value)) {
            return $this->post($key);
        }
        return $value;
    }

    /**
     * @name 覆盖框架的获取IP地址的实现
     * @return string
     */
    public function getUserIP()
    {
        $cip = '';
        foreach(['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'] as $key) {
            if ( ! empty($_SERVER[$key])) {
                $cip = $_SERVER[$key];
            }
        }
        return $cip;
    }
}