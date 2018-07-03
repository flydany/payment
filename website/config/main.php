<?php

return [
    'id' => 'basic-website',
    'name' => '支付中心',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'welcome/index',
    'controllerNamespace' => 'website\controllers',
    'bootstrap' => ['log'],
    'components' => [
        'errorHandler' => [
            'class' => 'website\components\ErrorHandler',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'view' => [
            'class' => 'website\components\View',
        ],
    ],
    'params' => require(__DIR__ . '/params.php'),
];