<?php

namespace website\components;

use Yii;

class View extends \common\components\View {

    
    /**
     * 获取登录用户
     * @return Object
     */
    public function getUser()
    {
        return Yii::$app->getUser();
    }

    /**
     * 判断是否已经登录
     * @return boolean
     */
    public function isLogin()
    {
        return Yii::$app->isLogin();
    }
}