<?php
/**
 * Created by PhpStorm.
 * User: flydany
 * Date: 2018/2/3
 * Time: 13:04
 */
namespace common\api\response;

use Yii;

/**
 * 接口提供商统一继承基类
 */
abstract class BaseApi {
    
    // 接口请求状态配置
    // 接口请求成功状态码配置
    const RequestSuccess = '200';
    // 接口统一配置，请求意外发生状态码，请求处理中
    const RequestIgnore = '201';
    // 请求数据异常
    const RequestParamError = '202';
    // 系统异常
    const SystemError = '203';
    
    /** 接口返回数据解析配置
     * @param $signKey string 签名字段
     * @param $requestCodeKey string 请求错误码字段
     * @param $requestMessageKey string 请求错误描述字段
     * @param $statusCodeKey string 返回值状态字段
     * @param $statusErrorCodeKey string 返回值错误码字段
     * @param $statusErrorMessageKey string 返回值错误描述字段
     */
    public $signKey = 'sign';
    public $requestCodeKey;
    public $requestMessageKey;
    public $requestErrorCodeKey;
    public $requestErrorMessageKey;
    public $statusCodeKey;
    public $statusErrorCodeKey;
    public $statusErrorMessageKey;
    public $orderNumberKey;
    // @rule 此处子类必须实现这些变量
    // 请求状态应答码定义
    public $requestSuccessCodes = [];
    public $requestFailCodes = [];
    // 交易状态应答码定义
    public $statusSuccessCodes = [];
    public $statusFailCodes = [];
    // 订单不存在的交易应答码
    public $requestExistCodes = [];
    
    // @param $partnerId string 商户编号
    public $partnerId;
    
    // ------------------------------------------
    // 签名相关配置
    // @describe 此处可实现自动签名、方便子类使用
    // ------------------------------------------
    // @param $unSignKeys array 不参与签名的字段
    // @param $isVerifySign boolean 是否参与验签
    // @param $generateSignFunction string 生成签名函数
    // @param $verifySignFunction string 校验签名函数
    // @param $sign string 签名结果
    // ------------------------------------------
    public $unSignKeys = ['sign'];
    public $isGenerateSign = true;
    public $generateSignFunction = 'generateSign';
    public $isVerifySign = true;
    public $verifySignFunction = 'verifySign';
    public $sign;
    // @param $beforeGenerateSign array[function, param] 数据签名之前执行函数
    // @param $beforeRequest array[function, param] 接口请求之前执行函数
    // @param $afterParseResponse array[function, param] 解析Body数据之前执行函数
    // @param $beforeTradeStatus array[function, param] 解析返回状态之前执行函数
    public $beforeGenerateSign;
    public $beforeRequest;
    public $afterParseResponse;
    public $beforeTradeStatus;
    
    // @param $publicKey string 签名公钥，用于校验返回数据
    // @param $privateKey string 签名私钥，用户签名数据
    // @param $privatePassword string 签名私钥证书密码
    public $publicKey;
    public $publicPassword;
    public $privateKey;
    public $privatePassword;
    
    // ------------------------------------------
    // 请求参数
    // ------------------------------------------
    // @param $http object 请求句柄
    // @param $method string 请求方式 默认：POST
    // @param $header string 请求头设置
    // @param $isReturnHeader boolean 是否返回头信息
    // @param $requestUri string 请求网站
    // @param $action string 接口网址
    // @param $params string 接口请求参数
    // ------------------------------------------
    /* @var $http Request */
    public $http;
    public $timeout = 60;
    public $method = 'POST';
    public $header;
    public $requestUri;
    public $action;
    public $params;
    public $charset = 'UTF-8';
    public $code;
    public $message;
    
    // 返回参数
    // @param $status string 交易状态
    // @param $response array 返回数据 [code, message, response]
    // @param $responseBody array 初始未解析数据 [code, resp]
    public $orderNumber;
    public $status;
    public $errorCode;
    public $errorMessage;
    public $response;
    public $responseBody;
    
    public function setTimeout($time)
    {
        $this->timeout = $time;
        return $this;
    }
    // 设置签名相关函数
    public function setGenerateSignFunction($function = 'generateSign')
    {
        $this->generateSignFunction = $function;
        return $this;
    }
    public function setVerifySignFunction($function = 'verifySign')
    {
        $this->verifySignFunction = $function;
        return $this;
    }
    /**
     * 设置接口请求地址
     * @param $action string 请求地址
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }
    /**
     * 设置接口请求方式
     * @param $method string 参数
     * @return $this object
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }
    /**
     * 设置接口请求参数
     * @param $params string|array 参数
     * @return $this object
     */
    public function setParams($params)
    {
        $this->params = [];
        if(is_array($params)) {
            $this->addParams($params);
        }
        else {
            $this->params = $params;
        }
        return $this;
    }
    /**
     * 添加请求参数
     * @param $params array 参数
     * @return $this object
     */
    public function addParams($params)
    {
        foreach($params as $key => $param) {
            $this->params[$key] = $param;
        }
        return $this;
    }
    /**
     * 设置请求头信息
     * @param $header array 头文件信息
     * @param $title string 头信息key
     * @return $this
     */
    public function setHeader($headers)
    {
        $this->header = [];
        $this->addHeader($headers);
        return $this;
    }
    /**
     * 追加头信息
     * @param $header string 头信息
     * @param $title string 头信息key
     * @return $this
     */
    public function addHeader($headers)
    {
        foreach($headers as $key => $header) {
            $this->header[$key] = $header;
        }
        return $this;
    }
    
    /**
     * @name 设置请求状态
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
     * 设置验签公钥
     * @param $publicKey string 公钥字符串
     * @param $dir string 公钥所属目录
     * @return $this
     */
    public function setPublicKey($publicKey, $dir)
    {
        $path = \Yii::getAlias('@common/api/'.$dir.'/cert/').$publicKey;
        if(@file_exists($path) == true) {
            $this->publicKey = file_get_contents($path);
        }
        else {
            $this->publicKey = $publicKey;
        }
        return $this;
    }
    /**
     * 设置加密私钥
     * @param $privateKey string 私钥字符串
     * @param $dir string 私钥所属目录
     * @return $this
     */
    public function setPrivateKey($privateKey, $dir = '')
    {
        $path = \Yii::getAlias('@common/api/'.$dir.'/cert/').$privateKey;
        if(@file_exists($path) == true) {
            $this->privateKey = ltrim(file_get_contents($path));
        }
        else {
            $this->privateKey = $privateKey;
        }
        return $this;
    }
    /**
     * 设置加密私钥证书密码
     * @param $privatePassword string 私钥证书密码
     * @return $this
     */
    public function setPrivatePassword($privatePassword)
    {
        $this->privatePassword = $privatePassword;
        return $this;
    }
    /**
     * 设置当前接口是否需要生成签名
     * @param $isGenerate bool 是否需要生成签名
     * @return $this
     */
    public function setIsGenerateSign($isGenerate = true)
    {
        $this->isGenerateSign = $isGenerate;
        return $this;
    }
    /**
     * 设置当前接口是否参与验签
     * @param $isVerify bool 是否不参与验签
     * @return $this
     */
    public function setIsVerifySign($isVerify = true)
    {
        $this->isVerifySign = $isVerify;
        return $this;
    }
    /**
     * 设置接口请求的URL
     * @param $requestUri string 接口请求的URL
     * @return $this
     */
    public function setRequestUri($requestUri)
    {
        $this->requestUri = $requestUri;
        return $this;
    }
    public function getRequestUri()
    {
        return $this->requestUri;
    }
    /**
     * 设置合作商户编号
     * @param $partnerId string 合作商户编号
     * @return $this
     */
    public function setPartnerId($partnerId)
    {
        $this->partnerId = $partnerId;
        return $this;
    }
    /**
     * 添加数据生成签名之前执行函数
     * @param $functions string/array 函数名称
     * @param $params array/string 函数参数值
     * @return $this|baseApi
     */
    public function addBeforeGenerateSignFunction($function, $params = [])
    {
        $this->beforeGenerateSign[] = [$function, $params];
        return $this;
    }
    /**
     * 添加接口请求之前执行函数
     * @param $functions string/array 函数名称
     * @param $params array/string 函数参数值
     * @return $this|baseApi
     */
    public function addBeforeRequestFunction($function, $params = [])
    {
        $this->beforeRequest[] = [$function, $params];
        return $this;
    }
    /**
     * 添加数据解析之后执行函数
     * @param $functions string/array 函数名称
     * @param $params array/string 函数参数值
     * @return $this|baseApi
     */
    public function addAfterParseResponseFunction($function, $params = [])
    {
        $this->afterParseResponse[] = [$function, $params];
        return $this;
    }
    /**
     * 添加返回值状态解析之前执行函数
     * @param $functions string/array 函数名称
     * @param $params array/string 函数参数值
     * @return $this|baseApi
     */
    public function addBeforeTradeStatusFunction($function, $params = [])
    {
        $this->beforeTradeStatus[] = [$function, $params];
        return $this;
    }
    /**
     * 执行钩子函数
     * @param $functions array 函数列表
     * @param $break Boolean 当程序返回false时，程序是否退出：true -> 退出，false -> 不退出
     * @return $result Boolean
     */
    protected function trigger($functions, $break = true)
    {
        // 不存在前置钩子函数 返回true
        if(empty($functions)) {
            return true;
        }
        // 逐个执行
        foreach($functions as $call) {
            // 如果函数未定义 抛出一个异常
            if( ! method_exists($this, $call[0])) {
                return false;
            }
            // 如果函数返回 false 跳出循环，返回 false
            $response = call_user_func_array([$this, $call[0]], [$call[1]]);
            if($response !== true && $response !== NULL) {
                if($break) {
                    return $response;
                }
            }
        }
        return true;
    }
    
    /**
     * 充值配置参数
     * @return $this
     */
    public function resetSystem()
    {
        $this->beforeRequest = [];
        $this->beforeGenerateSign = [];
        $this->afterParseResponse = [];
        $this->beforeTradeStatus = [];
        $this->response = [];
        $this->responseBody = '';
        $this->http = NULL;
        $this->params = [];
        // $this->isReturnHeader = false;
        // $this->method = static::RequestMethodPost;
        $this->header = [];
        $this->orderNumber = '';
        $this->status = '';
        $this->errorCode = '';
        $this->errorMessage = '';
        // $this->isVerifySign = false;
        // $this->setGenerateSignFunction();
        // $this->setVerifySignFunction();
        return $this;
    }
    // 金额单位转换，分 -> 元
    // @param $rules array 需要转换的数据规则
    // @return boolean/NULL
    protected function convertCertToYuan($rules)
    {
        // 如果rules为空，直接返回
        if(empty($rules)) {
            return true;
        }
        foreach($rules as $key) {
            if( ! isset($this->params[$key])) {
                continue;
            }
            $this->params[$key] = (string)bcdiv($this->params[$key], 100, 2);
        }
        return true;
    }
    // 金额单位转换，分 -> 元
    // @param $rules array 需要转换的数据规则
    // @return boolean/NULL
    protected function convertYuanToCert($rules)
    {
        // 如果rules为空，直接返回
        if(empty($rules)) {
            return true;
        }
        foreach($rules as $key => $rule) {
            if(Checker::checker_int($key)) {
                if( ! isset($this->response['response'][$rule])) {
                    continue;
                }
                $this->response['response'][$rule] = (string)bcmul($this->response['response'][$rule], 100, 0);
            }
            else {
                if( ! isset($this->response['response'][$key]) || ! is_array($rule)) {
                    continue;
                }
                foreach($this->response['response'][$key] as $k => $item) {
                    foreach($rule as $v) {
                        if( ! isset($this->response['response'][$key][$k][$v])) {
                            continue;
                        }
                        $this->response['response'][$key][$k][$v] = (string)bcmul($this->response['response'][$key][$k][$v], 100, 0);
                    }
                }
            }
        }
        return true;
    }
    /**
     * 重置各种请求参数
     * @return $this object
     */
    public function reset()
    {
        return $this;
    }
    /**
     * 组合请求参数
     * @return $this object
     */
    public function initParams()
    {
        $this->setParams([]);
        return $this;
    }
    /**
     * 生成签名数据
     * @param $params array 需要校验签名的参数
     * @return bool
     */
    public function startGenerateSign()
    {
        // 判断是否需要验签
        if($this->isGenerateSign) {
            $this->sign = $this->{$this->generateSignFunction}();
            // $this->generateSign();
            $this->addParams([$this->signKey => $this->sign]);
        }
        return true;
    }
    /**
     * 生成签名数据
     * @return string
     */
    public function generateSign()
    {
        return $this;
    }
    /**
     * 校验返回数据签名是否正确
     * @param $params array 需要校验签名的参数
     * @return bool
     */
    public function startVerifySign($params)
    {
        // 判断是否需要验签
        if($this->isVerifySign) {
            return $this->{$this->verifySignFunction}($params);
            // return $this->verifySign($params);
        }
        return true;
    }
    /**
     * 校验返回数据签名是否正确
     * @param $params array 需要校验签名的参数
     * @return bool
     */
    public function verifySign($params)
    {
        return true;
    }
    
    /**
     * 不同接口解析参数
     * @param $this->responseBody string 接口返回原始数据
     * @return mixed
     */
    public function parseHttpResponse()
    {
        return $this;
    }
    /**
     * 发起接口调用 - 统一入口
     * @param $url string 接口调用地址
     * @param $rules array 请求参数校验规则
     * @param $params array 请求参数
     * @return array [code, message, response]
     */
    public function request($url, $params = [], $rules = [])
    {
        try {
            if ($rules) {
                $checker = Checker::authentication($rules, $params);
                if ($checker['code'] !== Checker::SuccessCode) {
                    $this->response = ['code' => static::RequestParamError, 'message' => '数据异常: ' . $checker['message'], 'response' => ''];
                    return $this->response;
                }
            }
            // 重置、初始化接口请求、处理结果参数
            // 设置接口请求地址
            $this->reset()->setAction($url)->initParams()->addParams($params);
            // 签名生成前执行函数
            // $this->trigger($this->beforeGenerateSign, true);
            // 签名生成前执行函数
            if ($this->trigger($this->beforeGenerateSign, true) === false) {
                $this->response = ['code' => static::SystemError, 'message' => '执行钩子函数失败', 'response' => []];
                return $this->response;
            }
            // 对数据进行签名加密
            $this->startGenerateSign();
    
            // 请求接口前执行函数
            if ($this->trigger($this->beforeRequest, true) === false) {
                $this->response = ['code' => static::SystemError, 'message' => '执行钩子函数失败', 'response' => []];
                return $this->response;
            }
            // 请求服务，获取接口处理结果
            if (strtoupper($this->method) == 'POST') {
                $this->responseBody = $this->post();
            } else {
                $this->responseBody = $this->get();
            }
            // 解析接口返回结果
            $this->parseResponse();
            // 解析数据之后执行函数
            $this->trigger($this->afterParseResponse, false);
        }
        catch (\Exception $exception) {
            Yii::info('程序异常：'.$exception->__toString());
            $this->response = ['code' => static::SystemError, 'message' => '程序异常', 'response' => ''];
        }
        return $this->response;
    }
    
    /**
     * 发起FORM请求 - 统一入口
     * @param $url string 接口调用地址
     * @param $rules array 请求参数校验规则
     * @param $params array 请求参数
     * @return array [code, message, response]
     */
    public function builder($url, $params = [], $rules = [])
    {
        try {
            Yii::info('请求参数：'.json_encode(['action' => $url, 'params' => (array)$params]));
            if($rules) {
                $checker = Checker::authentication($rules, $params);
                if($checker['code'] !== Checker::SuccessCode) {
                    Yii::info('请求数据异常：'.json_encode($checker));
                    $this->response = ['code' => static::RequestParamError, 'message' => '数据异常: '.$checker['message'], 'response' => []];
                    return $this->response;
                }
            }
            // 重置、初始化接口请求、处理结果参数
            // 设置接口请求地址
            $this->reset()->setAction($url)->initParams()->addParams($params);
            // 签名生成前执行函数
            // $this->trigger($this->beforeGenerateSign, true);
            // 签名生成前执行函数
            if($this->trigger($this->beforeGenerateSign, true) === false) {
                $this->response = ['code' => static::SystemError, 'message' => '执行钩子函数失败', 'response' => []];
                return $this->response;
            }
            // 对数据进行签名加密
            $this->startGenerateSign();
            
            // 请求接口前执行函数
            if($this->trigger($this->beforeRequest, true) === false) {
                $this->response = ['code' => static::SystemError, 'message' => '执行钩子函数失败', 'response' => []];
                return $this->response;
            }
            
            // 组装FORM表单
            $this->response = ['code' => static::RequestSuccess, 'message' => '申请成功', 'response' => ['javascript' => $this->formBuilder()]];
        }
        catch (\Exception $exception) {
            Yii::info('程序异常：'.$exception->__toString());
            $this->response = ['code' => static::SystemError, 'message' => '程序异常', 'response' => []];
        }
        return $this->response;
    }
    /**
     * 创建表单
     * @return string
     */
    public function formBuilder()
    {
        $formString = '<meta http-equiv="content-type" content="text/html; charset='.$this->charset.'">';
        $formString .= "<form action='{$this->action}' method='post' target='_blank'>";
        
        foreach($this->params as $key => $value) {
            $formString .= "<input type='text' name='{$key}' value='{$value}'>";
        }
        
        $formString .= '<input id="submit" type="submit" value="submit">';
        $formString .= '</form>';
        $formString .= '<script>document.getElementById("submit").click();</script>';
        return $formString;
    }
    
    /**
     * 解析接口返回结果
     * @param $httpResponse array 接口返回原始结果 [code, response]
     * @return $this->response array 返回结果
     */
    public function parseResponse()
    {
        // @rule 如果Request 返回 false 或者 接口返回为空 以申请成功处理
        if(empty($this->responseBody)) {
            $this->response = ['code' => static::RequestIgnore, 'message' => '接口请求失败', 'response' => []];
            return $this;
        }
        // 接口请求异常
        if( ! $this->checkHttpResponseCode()) {
            // 需要以请求成功处理的异常返回码
            $this->response = ['code' => static::RequestIgnore, 'message' => '接口请求异常', 'response' => $this->responseBody];
            return $this;
        }
        // 区别解析不同接口的返回数据
        $this->parseHttpResponse();
        // 校验数据签名
        if($this->startVerifySign($this->responseBody)) {
            // 校验返回值之前执行函数
            $this->trigger($this->beforeTradeStatus, false);
            // 校验返回数据交易状态判断字段格式是否异常
            $this->parseRequestSuccessStatus();
        }
        // 签名校验未通过
        else {
            $this->parseRequestFailStatus();
        }
        return $this;
    }
    /**
     * 解析返回数据错误码
     * @return $this
     */
    public function parseRequestSuccessStatus()
    {
        if(($code = Render::value($this->responseBody, $this->requestCodeKey)) !== null) {
            $this->response = ['code' => $code, 'message' => Render::value($this->responseBody, $this->requestMessageKey), 'response' => $this->responseBody];
            // 解析交易状态
            $this->parseTradeStatus();
        }
        // 交易状态判断字段格式异常
        else {
            // 适应一些变态接口不同阶段返回错误码、错误描述不同字段的需求
            if(($code = Render::value($this->responseBody, $this->requestErrorCodeKey)) != null) {
                $this->response = ['code' => $code, 'message' => Render::value($this->responseBody, $this->requestErrorMessageKey), 'response' => $this->responseBody];
                // 解析交易状态
                $this->parseTradeStatus();
            }
            else {
                $this->response = ['code' => static::RequestIgnore, 'message' => '返回数据格式异常', 'response' => $this->responseBody];
            }
        }
        return $this;
    }
    /**
     * 解析交易状态
     * @return $this
     */
    public function parseTradeStatus()
    {
        $traderStatus = [
            'status' => 'statusCodeKey', 'errorCode' => 'statusErrorCodeKey',
            'errorMessage' => 'statusErrorMessageKey', 'orderNumber' => 'orderNumberKey'
        ];
        foreach($traderStatus as $trade => $key) {
            $this->{$trade} = Render::value($this->response['response'], $this->{$key});
        }
        return $this;
    }
    
    /**
     * 验签失败，组装返回错误码
     * @return $this
     */
    public function parseRequestFailStatus()
    {
        // 适应一些变态接口不同阶段返回错误码、错误描述不同字段的需求
        if(($code = Render::value($this->responseBody, $this->requestErrorCodeKey)) !== null) {
            $this->response = ['code' => $code, 'message' => Render::value($this->responseBody, $this->requestErrorMessageKey), 'response' => $this->responseBody];
        }
        else {
            $this->response = ['code' => static::RequestIgnore, 'message' => '数据校验未通过', 'response' => $this->responseBody];
        }
        return $this;
    }
    
    /**
     * 异常返回码判断
     * @return boolean
     */
    public function checkHttpResponseCode()
    {
        return in_array($this->code, ['200', '500', '504']);
    }
    
    /**
     * 发起post请求接口
     * @param $this->action string 请求网址
     * @param $this->params array 请求参数
     * @return array 请求结果
     */
    public function post()
    {
        // 初始化CURL请求配置
        $this->http = new Request();
        // 设置请求url、method、设置请求参数
        $this->http->setAction($this->action);
        $this->http->setMethod('POST');
        $this->http->setParam($this->params);
        // 设置编码格式、头信息
        $this->http->charset = $this->charset;
        $this->http->setHeader($this->header);
        $this->http->setTimeout($this->timeout);
        if(strtoupper($this->charset) != 'UTF-8') {
            Yii::info('POST请求开始', var_export(['params' => mb_convert_encoding($this->params, 'UTF-8', $this->charset), 'url' => $this->action, 'header' => $this->header], true));
        }
        else {
            Yii::info('POST请求开始', var_export(['params' => $this->params, 'url' => $this->action, 'header' => $this->header], true));
        }
        // 发送请求
        $response = $this->http->send();
        // 请求返回数据
        if(strtoupper($this->charset) != 'UTF-8') {
            $response = mb_convert_encoding($response, 'UTF-8', $this->charset);
        }
        Yii::info('POST请求结束', var_export(['response' => $response], true));
        $this->setStatus($this->http->code, $this->http->message);
        return $response;
    }
    
    /**
     * 发起get请求接口
     * @param $this->action string 请求网址
     * @param $this->params array 请求参数
     * @return array 请求结果
     */
    public function get()
    {
        // 初始化CURL请求配置
        $this->http = new Request();
        // 设置请求url、method、设置请求参数
        $this->http->setAction($this->action);
        $this->http->setMethod('GET');
        $this->http->setParam($this->params);
        // 设置编码格式、头信息
        $this->http->charset = $this->charset;
        $this->http->setHeader($this->header);
        $this->http->setTimeout($this->timeout);
        if(strtoupper($this->charset) != 'UTF-8') {
            Yii::info('GET请求开始', var_export(['params' => mb_convert_encoding($this->params, 'UTF-8', $this->charset), 'url' => $this->action, 'header' => $this->header], true));
        }
        else {
            Yii::info('GET请求开始', var_export(['params' => $this->params, 'url' => $this->action, 'header' => $this->header], true));
        }
        // 发送请求
        $response = $this->http->send();
        $this->setStatus($this->http->code, $this->http->message);
        // 请求返回数据
        if(strtoupper($this->charset) != 'UTF-8') {
            $response = mb_convert_encoding($response, 'UTF-8', $this->charset);
        }
        Yii::info('GET请求结束', var_export(['response' => $response], true));
        return $response;
    }
}