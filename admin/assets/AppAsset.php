<?php

namespace admin\assets;

use common\components\View;
use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot/static';
    public $baseUrl = '@web/static';
    
    public $depends = [
        // 'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
    
    public $css = [
        'font.awesome/css/font-awesome.css',
        'css/site.css',
    ];
    public $cssOptions = [
        'position' => View::POS_HEAD,
    ];
    
    public $js = [
        'jquery/jquery-2.0.3.min.js',
        'layer/layer.js',
        'js/common.js',
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
}
