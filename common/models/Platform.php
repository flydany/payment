<?php

namespace common\models;

use Yii;
use common\models\interfaces\ResourceInterface;

class Platform extends \yii\base\Model implements ResourceInterface {
    
    // 通道常量
    const PlatformYeepay = '1';
    const PlatformAllin = '2';
    const PlatformBaofoo = '3';
    const PlatformReapal = '4';
    const PlatformFuiou = '5';
    public static $platformSelector = [
        self::PlatformYeepay => 'yeepay',
        self::PlatformAllin => 'allin',
        self::PlatformBaofoo => 'baofoo',
        self::PlatformReapal => 'reapeal',
        self::PlatformFuiou => 'fuiou',
    ];

    // 支付类型常量
    const PaytypeFit = '0';
    const PaytypeRecharge = '1';
    const PaytypeWithdraw = '2';
    const PaytypeAgreement = '3';
    public static $paytypeSelector = [
        self::PaytypeRecharge => 'recharge',
        self::PaytypeWithdraw => 'withdraw',
        self::PaytypeAgreement => 'agreement',
    ];

    /**
     * 通道实例化配置
     * @param integer $id 编号
     * @param string $title 名称
     */
    public $id;
    public $title;
    public static function builder($id)
    {
        if(empty(static::$platformSelector[$id])) {
            return null;
        }
        $platform = new Platform();
        $platform->id = $id;
        $platform->title = static::$platformSelector[$id];
        return $platform;
    }

    // 银行列表
    public static $bankSelector = [
        '1' => '中国银行',
        '2' => '农业银行',
        '3' => '工商银行',
        '4' => '建设银行',
        '5' => '邮政储蓄银行',
        '6' => '交通银行',
        '7' => '招商银行',
        '8' => '兴业银行',
        '9' => '光大银行',
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

    /**
     * 返回权限类型
     * @return mixed
     */
    public static function resourceType()
    {
        return AdminResource::TypePlatform;
    }

    /**
     * 返回权限标识
     * @return mixed
     */
    public function getPower()
    {
        return $this->id;
    }

    /**
     * 判断当前用户是否有此项目的权限
     * @return boolean
     */
    public function getHasPermission()
    {
        return AdminResource::hasPermission($this->power, static::resourceType());
    }

    /**
     * 获取商户号已存在的负责人
     * @return array
     */
    public function getIdentities()
    {
        return AdminResource::identities($this->power, static::resourceType());
    }
}
