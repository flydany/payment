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

    // 银行列表
    public static $bankSelector = [
        '1' => '中国银行',
        '2' => '农业银行',
        '3' => '工商银行',
        '4' => '建设银行',
        '5' => '邮政储蓄银行',
        '6' => '交通银行',
        '7' => '招商银行',
        '8' => '光大银行',
        '9' => '兴业银行',
        '10' => '民生银行',
        '11' => '中信银行',
        '12' => '浦发银行',
        '13' => '平安银行',
        '14' => '华夏银行',
        '15' => '广发银行',
        '16' => '北京银行',
        '17' => '上海银行',
        '18' => '南京银行',
        '19' => '成都银行',
        '20' => '渤海银行',
        '21' => '宁波银行',
        '22' => '江西银行',
        '23' => '上海农商银行',
    ];
}
