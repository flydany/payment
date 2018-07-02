<?php

namespace common\models;

use Yii;

class Platform extends ActiveRecord {
    
    // 通道常量
    const PlatformYeepay = 1;
    const PlatformAllin = 2;
    const PlatformBaofoo = 3;
    const PlatformReapal = 4;
    const PlatformFuiou = 5;
    public static $stateSelector = [
        self::PlatformYeepay => ['title' => '易宝', 'status' => 'green'],
        self::PlatformAllin => ['title' => '通联', 'status' => 'blue'],
        self::PlatformBaofoo => ['title' => '宝付', 'status' => 'red'],
        self::PlatformReapal => ['title' => '融宝', 'status' => 'blue'],
        self::PlatformFuiou => ['title' => '富友', 'status' => 'green'],
    ];

    // 支付类型常量
    const PaytypeFitAll = 0;
    const PaytypeRecharge = 1;
    const PaytypeWithdarw = 2;
    const PaytypeDebit = 3;
    const PaytypeAgreement = 4;
    public static $paytypeSelector = [
        self::PaytypeFitAll => ['title' => '全适用', 'status' => 'gray'],
        self::PaytypeRecharge => ['title' => '充值', 'status' => 'blue'],
        self::PaytypeWithdarw => ['title' => '提现', 'status' => 'red'],
        self::PaytypeDebit => ['title' => '代扣', 'status' => 'purple'],
        self::PaytypeAgreement => ['title' => '协议支付', 'status' => 'green'],
    ];
}
