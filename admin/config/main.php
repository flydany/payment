<?php

return [
    'id' => 'basic-admin',
    'name' => 'Pay Point',
    'basePath' => dirname(__DIR__),
    'defaultRoute' => 'site/index',
    'controllerNamespace' => 'admin\controllers',
    'bootstrap' => ['log'],
    'components' => [
        'errorHandler' => [
            'class' => 'admin\components\ExceptionHandler',
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => require(__DIR__ . '/params.php'),
];