<?php
/**
 * Created by PhpStorm.
 * User: flydany
 * Date: 2018/2/3
 * Time: 13:14
 */
namespace common\helpers;

use Yii;

class Request {
    
    const RequestSuccess = 200;
    const ReqeustError = 500;
    public $connectTimeout = 5;
    public $timeout = 30;
    public $curl;
    public $method = "GET";
    public $charset = 'UTF-8';
    public $action;
    public $param;
    public $header;
    
    /**
     * 返回相关配置
     * @param $response string 返回值
     * @param $responseHeaderSize int 返回header大小
     */
    public $response;
    public $responseHeaderSize;
    
    public $code;
    public $message;
    
    /**
     * 设置请求超时时间
     * @param $time int 请求超时时间
     * @return $this
     */
    public function setTimeout($time)
    {
        $this->timeout = $time;
        return $this;
    }
    
    /**
     * 设置请求模式
     * @param $method string 请求模式
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = strtoupper($method);
        return $this;
    }
    
    /**
     * 设置请求状态
     * @param $code string 状态码
     * @param $message string 描述
     * @return $this
     */
    public function setStatus($code, $message = '')
    {
        $this->code = $code;
        $this->message = $message;
        return $this;
    }
    
    /**
     * 设置请求地址
     * @param $action string 请求地址
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }
    
    /**
     * 设置接口请求参数
     * @param $param string | array 参数
     * @return $this object
     */
    public function setParam($param)
    {
        $this->param = [];
        if(is_array($param)) {
            $this->addParam($param);
        }
        else {
            $this->param = $param;
        }
        return $this;
    }
    /**
     * 添加请求参数
     * @param $param array 参数
     * @return $this object
     */
    public function addParam($param)
    {
        foreach($param as $key => $param) {
            $this->param[$key] = $param;
        }
        return $this;
    }
    /**
     * 设置接口请求头信息
     * @param $header string | array 头信息
     * @return $this object
     */
    public function setHeader($header)
    {
        $this->header = [];
        if(is_array($header)) {
            $this->addHeader($header);
        }
        else {
            $this->header = $header;
        }
        return $this;
    }
    /**
     * 添加请求头信息
     * @param $header array 头信息
     * @return $this object
     */
    public function addHeader($header)
    {
        foreach($header as $key => $value) {
            $this->header[$key] = $value;
        }
        return $this;
    }
    
    /**
     * 初始化系统参数
     * @return $this
     */
    public function initSystem()
    {
        $this->code = '';
        $this->message = '';
        $this->response = [];
        return $this;
    }
    
    /*
     * 发送HTTP REQUEST请求
     * @return string
     * */
    public function send()
    {
        $this->initSystem();
        $this->curl = curl_init();
        if($this->method == 'GET') {
            $split = strpos($this->action, '?') ? '&' : '?';
            if(is_array($this->param)) {
                $this->action = $this->action.$split.implode('&', $this->param);
            }
            else {
                $this->action = $this->action.$split.$this->param;
            }
        }
        else {
            curl_setopt ($this->curl, CURLOPT_POST, true);
            curl_setopt ($this->curl, CURLOPT_POSTFIELDS, $this->param);
        }
        curl_setopt ($this->curl, CURLOPT_URL, $this->action);
        
        // 设置头信息
        if( ! empty($this->userAgent)) {
            curl_setopt ($this->curl, CURLOPT_USERAGENT, $this->userAgent);
        }
        // 是否返回头信息
        curl_setopt ($this->curl, CURLOPT_HEADER, 0);
        if( ! empty($this->header)) {
            curl_setopt ($this->curl, CURLOPT_HTTPHEADER, $this->header);
        }
        else {
            // $header = ["Content-Type: application/x-www-form-urlencoded; charset={$this->charset}"];
            // curl_setopt ($this->curl, CURLOPT_HTTPHEADER, $header);
        }
        
        // 结果返回到变量中，而不是输出到屏幕
        curl_setopt ($this->curl, CURLOPT_RETURNTRANSFER, true);
        
        // 设置连接超时时间 和超时时间
        curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, $this->timeout);
        
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
        $this->response = curl_exec($this->curl);
        if ( $this->response === false ) {
            $this->setStatus(static::ReqeustError, '请求失败');
        }
        else {
            $this->setStatus(curl_getinfo($this->curl, CURLINFO_HTTP_CODE), 'Connection Success');
        }
        
        curl_close($this->curl);
        return $this->response;
    }
    
    /**
     * 通过POST请求接口
     * @param $action string 接口地址
     * @param $param array 请求参数
     * @param $header array 头信息
     * @return mixed
     */
    public static function post($action, $param, $header = [])
    {
        $request = new static();
        $request->setMethod('POST');
        $request->setAction($action);
        $request->setParam($param);
        $request->setHeader($header);
        return $request->send();
    }
    /**
     * 通过GET请求接口
     * @param $action string 接口地址
     * @param $param array 请求参数
     * @param $header array 头信息
     * @return mixed
     */
    public static function get($action, $param, $header = [])
    {
        $request = new static();
        $request->setMethod('GET');
        $request->setAction($action);
        $request->setParam($param);
        $request->setHeader($header);
        return $request->send();
    }
}