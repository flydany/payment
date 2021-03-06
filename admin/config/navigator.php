<?php

return [
    '0' => [
        '001' => ['title' => 'Data', 'icon_class' => 'database', 'controller' => '#'],
        '002' => ['title' => 'Customer', 'icon_class' => 'user-plus', 'controller' => '#'],
        '009' => ['title' => 'System', 'icon_class' => 'gears', 'controller' => '#'],
    ],
    '001' => [
        '001001' => ['title' => 'Recharge', 'icon_class' => 'cloud-upload', 'controller' => '#'],
        '001002' => ['title' => 'Withdraw', 'icon_class' => 'cloud-download', 'controller' => '#'],
        '001003' => ['title' => 'Agreement', 'icon_class' => 'free-code-camp', 'controller' => '#'],
        '001004' => ['title' => 'Account Check', 'icon_class' => 'recycle', 'controller' => '#'],
    ],
    '001001' => [
        '001001001' => ['title' => 'Recharge Statistics', 'icon_class' => 'bar-chart', 'controller' => 'recharge/index'],
        '001001002' => ['title' => 'Recharge Record', 'icon_class' => 'book', 'controller' => 'recharge/list'],
    ],
    '001002' => [
        '001002001' => ['title' => 'Withdraw Statistics', 'icon_class' => 'area-chart', 'controller' => 'withdraw/index'],
        '001002002' => ['title' => 'Withdraw Record', 'icon_class' => 'book', 'controller' => 'withdraw/list'],
    ],
    '001003' => [
        '001003001' => ['title' => 'Agreement Statistics', 'icon_class' => 'area-chart', 'controller' => 'agreement/index'],
        '001003002' => ['title' => 'Agreement Record', 'icon_class' => 'book', 'controller' => 'agreement/list'],
    ],
    '001004' => [
        '001004001' => ['title' => 'Recharge', 'icon_class' => 'book', 'controller' => 'account-check/recharge'],
        '001004002' => ['title' => 'Withdraw', 'icon_class' => 'book', 'controller' => 'account-check/withdraw'],
        '001004003' => ['title' => 'Agreement', 'icon_class' => 'book', 'controller' => 'account-check/agreement'],
    ],
    
    '002' => [
        '002001' => ['title' => 'Project', 'icon_class' => 'shopping-cart', 'controller' => '#'],
        '002002' => ['title' => 'Platform', 'icon_class' => 'thumb-tack', 'controller' => '#'],
        '002003' => ['title' => 'Card', 'icon_class' => 'address-book', 'controller' => '#'],
    ],
    '002001' => [
        '002001001' => ['title' => 'Project List', 'icon_class' => 'book', 'controller' => 'project/list'],
        '002001002' => ['title' => 'Project Api', 'icon_class' => 'ravelry', 'controller' => 'project/api-list'],
        '002001003' => ['title' => 'Project Contacts', 'icon_class' => 'address-book', 'controller' => 'project/contacts-list'],
        '002001004' => ['title' => 'Project Merchant', 'icon_class' => 'shopping-bag', 'controller' => 'project/merchant-list'],
        '002001005' => ['title' => 'Bank Limited', 'icon_class' => 'minus-circle', 'controller' => 'project/bank-limit'],
    ],
    '002002' => [
        '002002001' => ['title' => 'Platform List', 'icon_class' => 'thumb-tack', 'controller' => 'platform/list'],
        '002002002' => ['title' => 'Merchant List', 'icon_class' => 'shopping-bag', 'controller' => 'platform/merchant-list'],
        '002002003' => ['title' => 'Merchant Banks', 'icon_class' => 'bank', 'controller' => 'platform/bank-list'],
        '002002004' => ['title' => 'Merchant Maintains', 'icon_class' => 'ban', 'controller' => 'platform/maintain-list'],
        '002002005' => ['title' => 'ErrorCode', 'icon_class' => 'ban', 'controller' => 'platform/error-code'],
        '002002006' => ['title' => 'Process Monitor', 'icon_class' => 'terminal', 'controller' => 'platform/process-monitor'],
    ],
    '002003' => [
        '002003001' => ['title' => 'Card List', 'icon_class' => 'thumb-tack', 'controller' => 'platform/list'],
        '002003002' => ['title' => 'Card Forbidden', 'icon_class' => 'shopping-bag', 'controller' => 'platform/merchant-list'],
    ],
    
    '009' => [
        '009001' => ['title' => 'Manager', 'icon_class' => 'superpowers', 'controller' => '#'],
        '009002' => ['title' => 'System', 'icon_class' => 'gear', 'controller' => '#'],
    ],
    '009001' => [
        '009001001' => ['title' => 'Administrator List', 'icon_class' => 'user', 'controller' => 'admin/list'],
        '009001002' => ['title' => 'Administrator Group', 'icon_class' => 'superpowers', 'controller' => 'admin/group-list'],
    ],
    '009002' => [
        '009002001' => ['title' => 'Navigation List', 'icon_class' => 'navicon', 'controller' => 'navigator/list'],
        '009002002' => ['title' => 'Maintain', 'icon_class' => 'home', 'controller' => 'system/index'],
    ],
];