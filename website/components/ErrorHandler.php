<?php

namespace website\components;

use yii\web\Response;

class ErrorHandler extends \yii\web\ErrorHandler {
    
    /**
     * 异常输出格式化
     * @param \Error|\Exception $exception
     */
    protected function renderException($exception)
    {
        $code = $exception->getCode() == 0 ? 500 : $exception->getCode();
        $array = ['data' => '', 'code' => $code, 'message' => $exception->getMessage()];
        
        $response = \Yii::$app->response;
        if(in_array($response->format, [Response::FORMAT_JSON,Response::FORMAT_JSONP])) {
            $response->data = $array;
        }
        else {
            parent::renderException($exception);
        }
        
        $response->setStatusCode(200);
        $response->send();
    }
}