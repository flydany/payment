<?php

namespace admin\assets;

/**
 * Main backend application asset bundle.
 */
class BootstrapAsset extends \yii\bootstrap\BootstrapAsset {
    
    public $js = [
        'js/bootstrap.js',
    ];
    
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
