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
        'admin\assets\FontAwesomeAsset',
        'admin\assets\DialogAsset',
        'admin\assets\SelecterAsset',
    ];
    
    public $css = [
        'css/site.css',
    ];
    public $cssOptions = [
        'position' => View::POS_HEAD,
    ];
    
    public $js = [
        'js/dialog.js',
        'js/common.js',
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
}
