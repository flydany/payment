<?php

namespace admin\components;

use Yii;
use admin\assets\AppAsset;

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
    
    /**
     * 重写JS文件引入规则
     * @param string $js js路径
     * @param array $options 配置信息
     * @param boolean|null $key 是否MD5 KEY
     * @return script tag
     */
    public function registerJavascript($url, $options = [], $key = null)
    {
        if(strpos('@static/', $url) >= 0) {
            $url = str_replace('@static', '@web/static/', $url);
        }
        $options = array_merge([
            AppAsset::className(),
            'depends' => 'admin\assets\AppAsset',
            'position' => static::POS_HEAD,
        ], $options);
        return parent::registerJsFile($url, $options, $key);
    }

    /**
     * 重写css文件引入规则
     * @param string $js js路径
     * @param array $options 配置信息
     * @param boolean|null $key 是否MD5 KEY
     * @return link tag
     */
    public function registerCsser($url, $options = [], $key = null)
    {
        if(strpos('@static/', $url) >= 0) {
            $url = str_replace('@static', '@web/static/', $url);
        }
        $options = array_merge([
            AppAsset::className(),
            'depends' => 'admin\assets\AppAsset',
            'position' => static::POS_HEAD,
        ], $options);
        return parent::registerCssFile($url, $options, $key);
    }
}