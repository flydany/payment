<?php

return [
    /**
     * 分类 相关，如：父级分类编号
     * @describe 数据库存储字段编号值
     */
    'articleCategories' => 1,
    'designCategories' => 2,
    'designModelCategories' => 3,

    // 网站路径
    'domain' => $_SERVER['SERVER_NAME'],
    
    // 静态资源路径
    'resourceUrl' => 'http://resource.design.net:8080',
    
    // 支付宝配置
    'alipay' => [
        'requestUri' => 'https://openapi.alipay.com/gateway.do',
        'partnerId' => '2018031802399008',
        'privateKey' => 'alipay.private.pem',
        'publicKey' => 'alipay.public.pem',
    ],
    
    // sms短信服务商配置
    'jiguang' => [
        'partnerId' => 'adafae75e6203a85e640f035',
        'key' => 'aa336d3b1e4697a09c460cc0',
    ],
    
    // 邮件发送人
    'mail' => [
        'from' => 'flydany@yeah.net',
    ],
];