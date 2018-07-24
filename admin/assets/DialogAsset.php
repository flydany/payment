<?php

namespace admin\assets;

use yii\web\AssetBundle;


/**
 * Main backend application asset bundle.
 */
class DialogAsset extends AssetBundle {

    public $basePath = '@webroot/static/bootstrap.dialog';
    public $baseUrl = '@web/static/bootstrap.dialog';

    public $js = [
        'js/bootstrap-dialog.min.js',
    ];

    public $css = [
        'css/bootstrap-dialog.min.css',
    ];
}
