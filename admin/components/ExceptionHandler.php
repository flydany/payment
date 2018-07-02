<?php

namespace admin\components;

use Yii;
use yii\web\Response;

class ExceptionHandler extends \yii\web\ErrorHandler {
    
    /**
     * 异常输出格式化
     * @param \Exception $exception
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
            // $controller = \Yii::$app->controller;
            // $controller->layout = 'simple.php';
            // Yii::$app->getResponse()->content = $controller->error("当前页面：{$controller->id} / {$controller->action->id} <br>出现严重异常，请联系管理员：flydany@yeah.net");
        }
        
        $response->setStatusCode(200);
        $response->send();
    }
}