<?php

namespace admin\components;

use Yii;

class View extends \common\components\View {

    // 当前激活的导航
    public $activeNavigator;
    
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
    
    /**
     * 设置当前激活的导航
     * @param string $uri 导航路径
     * @return $this
     */
    public function setActiveNavigator($uri)
    {
        $this->activeNavigator = $uri;
        return $this;
    }
    public function activeNavigator()
    {
        if($this->activeNavigator) {
            return $this->activeNavigator;
        }
        return Yii::$app->request->getPathInfo();
    }
    
    /**
     * 展示提示
     * @param integer $number 数据编号
     * @return string
     */
    public function modifyNotice($number)
    {
        $number = $number ? ' of number <strong>'.$number.'</strong>' : '';
        return "you are editing the information{$number}.";
    }
}