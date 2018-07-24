<?php

namespace admin\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class UploaderFileAsset extends AssetBundle {

    public $basePath = '@webroot/static/uploader';
    public $baseUrl = '@web/static/uploader';

    public $js = [
        'loader.core.single.class.js',
        'loader.file.class.js',
    ];

    public $depends = [
        'admin\assets\UploaderAsset',
    ];
}
