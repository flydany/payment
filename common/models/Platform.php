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
    public static $platformSelector = [
        self::PlatformYeepay => '易宝',
        self::PlatformAllin => '通联',
        self::PlatformBaofoo => '宝付',
        self::PlatformReapal => '融宝',
        self::PlatformFuiou => '富友',
    ];

    // 支付类型常量
    const PaytypeRecharge = 1;
    const PaytypeWithdarw = 2;
    const PaytypeAgreement = 3;
    public static $paytypeSelector = [
        self::PaytypeRecharge => '充值',
        self::PaytypeWithdarw => '提现',
        self::PaytypeAgreement => '协议支付',
    ];
}
