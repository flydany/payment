<?php

return [
    
    // 网站路径
    'domain' => $_SERVER['SERVER_NAME'],
    
    // 支付宝配置
    'alipay' => [
        'requestUri' => 'https://openapi.alipay.com/gateway.do',
        'partnerId' => '',
        'privateKey' => '',
        'publicKey' => '',
    ],
    
    // sms短信服务商配置
    'jiguang' => [
        'partnerId' => '',
        'key' => '',
    ],
    
    // 邮件发送人
    'mail' => [
        'from' => '',
    ],
];