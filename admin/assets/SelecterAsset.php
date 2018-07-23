<?php

namespace admin\assets;

use yii\web\AssetBundle;


/**
 * Main backend application asset bundle.
 */
class SelecterAsset extends AssetBundle {

    public $basePath = '@webroot/static/bootstrap.select';
    public $baseUrl = '@web/static/bootstrap.select';

    public $js = [
        'js/bootstrap-select.min.js',
    ];

    public $css = [
        'css/bootstrap-select.min.css',
    ];
}
