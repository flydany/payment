<?php

return [
    '0' => [
        '001' => ['title' => 'Information', 'icon_class' => 'home', 'controller' => '', 'method' => ''],
        '002' => ['title' => 'Configuration', 'icon_class' => 'gear', 'controller' => '', 'method' => ''],
        '003' => ['title' => 'Manager', 'icon_class' => 'user-group', 'controller' => '', 'method' => ''],
        '009' => ['title' => 'System', 'icon_class' => 'repair', 'controller' => '', 'method' => ''],
    ],
    '001' => [
        '001001' => ['title' => '充值管理', 'icon_class' => 'home', 'controller' => '', 'method' => ''],
        '001002' => ['title' => '提现管理', 'icon_class' => 'home', 'controller' => '', 'method' => ''],
        '001003' => ['title' => '代扣管理', 'icon_class' => 'home', 'controller' => '', 'method' => ''],
    ],
    '001001' => [
        '001001001' => ['title' => '充值概览', 'icon_class' => 'home', 'controller' => 'recharge', 'method' => 'index'],
        '001001002' => ['title' => '充值列表', 'icon_class' => 'home', 'controller' => 'recharge', 'method' => 'list'],
    ],
    
    '002' => [
        '002001' => ['title' => '项目管理', 'icon_class' => 'leaf', 'controller' => '', 'method' => ''],
        '002005' => ['title' => '通道管理', 'icon_class' => 'home', 'controller' => '', 'method' => ''],
    ],
    '002001' => [
        '002001001' => ['title' => '项目列表', 'icon_class' => 'home', 'controller' => 'project', 'method' => 'list'],
        '002001002' => ['title' => '项目联系人', 'icon_class' => 'home', 'controller' => 'project', 'method' => 'contacts'],
    ],
    
    '003' => [
        '009001' => ['title' => '管理员列表', 'icon_class' => 'user', 'controller' => 'admin', 'method' => 'list'],
    ],
    
    '009' => [
        '009001' => ['title' => '系统管理', 'icon_class' => 'user', 'controller' => 'admin', 'method' => 'list'],
        '009002' => ['title' => '系统设置', 'icon_class' => 'superpowers', 'controller' => 'admin', 'method' => 'role-list'],
    ],
    '009001' => [
        '009001001' => ['title' => '管理员列表', 'icon_class' => 'user', 'controller' => 'admin', 'method' => 'list'],
        '009001002' => ['title' => '权限列表', 'icon_class' => 'superpowers', 'controller' => 'admin', 'method' => 'role-list'],
    ],
    '009002' => [
        '009002001' => ['title' => '导航设置', 'icon_class' => 'leaf', 'controller' => 'navigator', 'method' => 'index'],
        '009002002' => ['title' => '维护管理', 'icon_class' => 'home', 'controller' => '', 'method' => 'index'],
    ],
];