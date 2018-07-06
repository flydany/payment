<?php

namespace admin\assets;

use yii\web\AssetBundle;
use common\components\View;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle {
    
    public $basePath = '@webroot/static';
    public $baseUrl = '@web/static';
    
    public $depends = [
        'admin\assets\BootstrapAsset',
    ];
    
    public $css = [
        'font.awesome/css/font-awesome.css',
        // 'css/theme.css',
        'css/site.css',
    ];
    public $cssOptions = [
        'position' => View::POS_HEAD,
    ];
    
    public $js = [
        // 'layer/layer.js',
        'bootstrap.dialog/js/bootstrap-dialog.min.js',
        'js/common.js',
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
}
