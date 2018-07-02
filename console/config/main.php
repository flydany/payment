<?php
$params = array_merge(
    require(__DIR__ . '/params.php')
);

return [
    'id' => 'basic-admin',
    'name' => '大明钱庄',
    'language' => 'en',
    'timeZone' => 'Asia/Shanghai',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'console\controllers',
    'defaultRoute' => 'welcome/index',
    'components' => [
        'db' => require(__DIR__ . '/../../common/config/db.php'),
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => '127.0.0.1',
            'port' => 6379,
            'database' => 1,
        ],
        'session' => [
            'class' => 'yii\redis\Session',
            'timeout' => 86400,
            'redis' => [
                'class' => 'yii\redis\Connection',
                'hostname' => '127.0.0.1',
                'port' => 6379,
            ],
        ],
    ],
    'params' => $params,
];