<?php

// 定义成功代码
defined('SuccessCode') or define('SuccessCode', '200');

$env = get_cfg_var('env');
$env = in_array($env, ['dev', 'test', 'prod']) ? $env : 'dev';
defined('YII_ENV') or define('YII_ENV', $env);
defined('YII_DEBUG') or define('YII_DEBUG', $env == 'prod' ? false : true);

require(__DIR__ . '/../../vendor/autoload.php');
require(__DIR__ . '/../../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');
require(__DIR__ . '/../../admin/components/Application.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../../environments/' . YII_ENV . '/common/main-local.php'),
    require(__DIR__ . '/../../environments/' . YII_ENV . '/admin/main-local.php')
);

(new admin\components\Application($config))->run();
