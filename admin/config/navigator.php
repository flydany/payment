<?php

return [
    '0' => [
        '001' => ['title' => 'Data', 'icon_class' => '', 'controller' => '', 'method' => ''],
        '002' => ['title' => 'Customer', 'icon_class' => '', 'controller' => '', 'method' => ''],
        '009' => ['title' => 'System', 'icon_class' => '', 'controller' => '', 'method' => ''],
    ],
    '001' => [
        '001001' => ['title' => 'Recharge', 'icon_class' => 'cart-plus', 'controller' => '', 'method' => ''],
        '001002' => ['title' => 'Withdraw', 'icon_class' => 'credit-card', 'controller' => '', 'method' => ''],
        '001003' => ['title' => 'Agreement', 'icon_class' => 'free-code-camp', 'controller' => '', 'method' => ''],
    ],
    '001001' => [
        '001001001' => ['title' => 'Recharge Statistics', 'icon_class' => 'home', 'controller' => 'recharge', 'method' => 'index'],
        '001001002' => ['title' => 'Recharge Record', 'icon_class' => 'home', 'controller' => 'recharge', 'method' => 'list'],
    ],
    '001002' => [
        '001002001' => ['title' => 'Withdraw Statistics', 'icon_class' => 'home', 'controller' => 'withdraw', 'method' => 'index'],
        '001002002' => ['title' => 'Withdraw Record', 'icon_class' => 'home', 'controller' => 'withdraw', 'method' => 'list'],
    ],
    '001003' => [
        '001003001' => ['title' => 'Agreement Statistics', 'icon_class' => 'home', 'controller' => 'agreement', 'method' => 'index'],
        '001003002' => ['title' => 'Agreement Record', 'icon_class' => 'home', 'controller' => 'agreement', 'method' => 'list'],
    ],
    
    '002' => [
        '002001' => ['title' => 'Project', 'icon_class' => 'shopping-cart', 'controller' => '', 'method' => ''],
        '002005' => ['title' => 'Merchant', 'icon_class' => 'thumb-tack', 'controller' => '', 'method' => ''],
    ],
    '002001' => [
        '002001001' => ['title' => 'Project List', 'icon_class' => 'home', 'controller' => 'project', 'method' => 'list'],
        '002001002' => ['title' => 'Project Contacts', 'icon_class' => 'home', 'controller' => 'project', 'method' => 'contacts'],
    ],
    
    '009' => [
        '009001' => ['title' => 'Manager', 'icon_class' => 'superpowers', 'controller' => 'admin', 'method' => 'list'],
        '009002' => ['title' => 'System', 'icon_class' => 'gear', 'controller' => 'admin', 'method' => 'role-list'],
    ],
    '009001' => [
        '009001001' => ['title' => 'Administrator List', 'icon_class' => 'user', 'controller' => 'admin', 'method' => 'list'],
        '009001002' => ['title' => 'Administrator Group', 'icon_class' => 'superpowers', 'controller' => 'admin', 'method' => 'role-list'],
    ],
    '009002' => [
        '009002001' => ['title' => 'Navigation List', 'icon_class' => 'leaf', 'controller' => 'navigator', 'method' => 'index'],
        '009002002' => ['title' => 'Maintain', 'icon_class' => 'home', 'controller' => '', 'method' => 'index'],
    ],
];