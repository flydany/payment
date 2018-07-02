<?php

$host = 'http://upload.design.net:8080';
return [
    // 合作者 上传配置
    'cooperator' => [
        'host' => $host,
        'path' => 'cooperator/{yyyy}{mm}/',
        'allow' => ['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif'],
        'size' => 1024 * 1024,
        'width' => 1024,
        'height' => 1024,
    ],
    // 外链 上传配置
    'externalLink' => [
        'host' => $host,
        'path' => 'external-link/{yyyy}{mm}/',
        'allow' => ['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif'],
        'size' => 1024 * 1024,
        'width' => 1024,
        'height' => 1024,
    ],
    // 产品图片 上传配置
    'design' => [
        'host' => $host,
        'path' => 'design/{yyyy}{mm}/',
        'allow' => ['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif'],
        'size' => 1024 * 1024 * 10,
        'width' => 1024,
        'height' => 1024,
    ],
    // 文章 上传配置
    'article' => [
        'host' => $host,
        'path' => 'article/{yyyy}{mm}/',
        'allow' => ['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif'],
        'size' => 1024 * 1024,
        'width' => 1024,
        'height' => 1024,
    ],
    // 设计师相关 上传配置
    'designer' => [
        'host' => $host,
        'path' => 'designer/{yyyy}{mm}/',
        'allow' => ['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif'],
        'size' => 1024 * 1024,
        'width' => 1024,
        'height' => 1024,
    ],
    // 设计师评级作品 上传配置
    'designerUpload' => [
        'host' => $host,
        'path' => 'designer-upload/{yyyy}{mm}/',
        'allow' => ['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif'],
        'size' => 1024 * 1024 * 4,
        'width' => 1024,
        'height' => 1024,
    ],
    // 雇佣申诉 上传配置
    'employmentAppeal' => [
        'host' => $host,
        'path' => 'employment-appeal/{yyyy}{mm}/',
        'allow' => ['image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png', 'image/gif'],
        'size' => 1024 * 1024 * 4,
        'width' => 1024,
        'height' => 1024,
    ]
];