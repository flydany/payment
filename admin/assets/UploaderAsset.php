<?php

namespace admin\assets;

use yii\web\AssetBundle;


/**
 * Main backend application asset bundle.
 */
class UploaderAsset extends AssetBundle {

    public $basePath = '@webroot/static/uploader';
    public $baseUrl = '@web/static/uploader';

    public $js = [
        'loader.core.class.js',
    ];

    public $css = [
        'loader.css',
    ];
}
