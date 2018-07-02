<?php

/**
 * this base this was create for common this property
 * & return json data
 */
 
namespace console\controllers;

use Yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

class Controller extends \yii\web\Controller {

    // 不启用csrf验证
    public $enableCsrfValidation = false;
    
    // 打印日志
    // @return string error html
    public function message($message)
    {
        $time = date('Y-m-d H:i:s');
        echo "[{$time}] {$message}\n";
        return true;
    }

    // get session
    // @return object yii session
    public function getSession()
    {
        return Yii::$app->getSession();
    }
    
    // get application
    // @return object yii app
    public function getApp()
    {
        return Yii::$app;
    }

    // 打印数组
    public function v($data)
    {
        echo '<pre>'; print_r($data); echo '</pre>';
    }
}
