<?php

namespace common\components;

use Yii;
use yii\helpers\Url;
use common\helpers\Render;

class View extends \yii\web\View {

    // @name 面包屑导航列表
    public $crumbs = [];

    /**
     * @name 添加面包屑导航
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
     * 获取当前控制器
     * @return \yii\web\Controller
     */
    public function getController()
    {
        return Yii::$app->controller;
    }
    
    /**
     * 重写JS引入规则
     * @param string $js js路径
     * @param int $position 引入顺序
     * @param boolean|null $key 是否MD5 KEY
     */
    public function registerJs($js, $position = self::POS_LOAD, $key = null)
    {
        return parent::registerJs($js, $position, $key);
    }
}