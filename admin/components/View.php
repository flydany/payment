<?php

namespace admin\components;

use Yii;

class View extends \common\components\View {

    
    /**
     * 获取登录管理员
     * @return Object
     */
    public function getAdmin()
    {
        return Yii::$app->getAdmin();
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