<?php
/**
 * Created by PhpStorm.
 * User: flydany
 * Date: 2018/2/3
 * Time: 13:04
 */
namespace common\api\responses;

use Yii;

/**
 * 接口提供商统一继承基类
 */
abstract class BaseApi {
    
    // 接口请求状态配置
    // 接口请求成功状态码配置
    const RequestSuccess = '200';
    
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
}