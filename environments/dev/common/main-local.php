<?php

return [
    'bootstrap' => ['gii', 'debug'],
    'modules' => [
        'gii' => [
            'class' => 'yii\gii\Module',
        ],
        'debug' => [
            'class' => 'yii\debug\Module',
        ],
    ],
    'components' => [
        'db' => require(__DIR__ . '/../common/database.php'),
        /**
        'redis' => require(__DIR__ . '/../common/redis.php'),
        'session' => [
            'class' => 'yii\redis\Session',
            'name' => 'payment',
            'timeout' => 86400,
            'redis' => require(__DIR__ . '/../common/redis.php')
        ],
         */
    ],
    
    'params' => require __DIR__ . '/params-local.php'
];
