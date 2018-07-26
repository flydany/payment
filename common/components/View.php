<?php

namespace common\components;

use admin\assets\AppAsset;
use Yii;
use yii\helpers\Url;
use common\helpers\Render;

class View extends \yii\web\View {

    // @name 面包屑导航列表
    public $crumbs = [];

    /**
     * 添加面包屑导航
     * @param $title string 导航名称
     * @param $url string 导航地址
     * @return $this
     */
    public function addCrumbs($title, $url = '')
    {
        $this->crumbs[] = [
            'url' => $url ? Url::to('@web/'.$url) : '',
            'title' => $title,
        ];
        return $this;
    }

    /**
     * 获取Request
     * @return \yii\console\Request|\yii\web\Request
     */
    public function getRequest()
    {
        return Yii::$app->getRequest();
    }
    
    /**
     * 获取当前控制器
     * @return \yii\web\Controller
     */
    public function getController()
    {
        return Yii::$app->controller;
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