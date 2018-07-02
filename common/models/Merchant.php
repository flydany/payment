<?php

namespace common\models;

use Yii;

class Merchant extends FlyerActiveRecord {
    
    // 私钥格式
    const PrivateTypePFX = 'pfx';
    const PrivateTypeCER = 'cer';
    const PrivateTypePEM = 'pem';
    const PrivateTypeP12 = 'p12';
    const PrivateTypeKEY = 'key';
    const PrivateTypeDER = 'der';
    public static $privateTypeSelector = [
        self::PrivateTypePFX => 'x-pkcs12 // PFX',
        self::PrivateTypeCER => 'x-x509-ca-cert // CER',
        self::PrivateTypePEM => 'x-x509-ca-cert // PEM',
        self::PrivateTypeP12 => 'x-pkcs12 // P12',
        self::PrivateTypeKEY => 'octet-stream // KEY',
    ];
    
    // 配置状态
    const StatusInit = 0;
    const StatusOnline = 90;
    const StatusForbidden = 92;
    public static $stateSelector = [
        self::StatusInit => '初始化',
        self::StatusOnline => '已上线',
        self::StatusClose => '已停用',
    ];
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'platform_id', 'merchant_id','paytype'], 'required'],
            [['platform_id', 'paytype', 'status', 'deleted_at'], 'integer'],
            [['private_type'], 'string', 'max' => 8],
            [['title', 'request_uri','remark', 'rate', 'min', 'max', 'base_fee'], 'string', 'max' => 255],
            [['merchant_id','private_password'], 'string', 'max' => 64],
            [['private_key', 'public_key', 'configuration'], 'string', 'max' => 65535],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => '标题',
            'platform_id' => '通道号',
            'merchant_id' => '商户号',
            'paytype' => '支付类型',
            'request_uri' => '请求地址',
            'private_key' => '商户私钥串',
            'private_password' => '私钥密码',
            'private_type' => '私钥格式',
            'public_key' => '通道公钥串',
            'configuration' => '其他',
            'rate' => '费率',
            'min' => '最低费用',
            'max' => '费用上限',
            'base_fee' => '基础费用',
            'status' => '状态',
            'deleted_at' => '删除时间',
            'remark' => '备注',
        ];
    }
    /**
     * update & insert data check config for html
     * @param string $type 页面操作类型
     * @return string|array
     */
    public static function flyer($type = 'update')
    {
        $rule = [
            'param' => [
                'title' => ['标题', ['maxlength' => 255, 'required']],
                'platform_id' => ['通道号', ['inkey' => Platform::$platformSelector, 'required']],
                'merchant_id' => ['商户号', ['maxlength' => 64, 'required']],
                'paytype' => ['支付类型', ['inkey' => Platform::$paytypeSelector, 'required']],
                'request_uri' => ['请求地址', ['url', 'required']],
                'private_key' => ['商户私钥串', ['maxlength' => 65535]],
                'private_password' => ['私钥密码', ['maxlength' => 64]],
                'private_type' => ['私钥格式', ['inkey' => static::$privateTypeSelector]],
                'public_key' => ['通道公钥串', ['maxlength' => 65535]],
                // 'name' => ['其他参数KEY', ['maxlength' => 64]],
                // 'value' => ['其他参数VALUE', ['maxlength' => 1024]],
                'remark' => ['备注', ['maxlength' => 255]],
                'rate' => ['费率', ['maxlength' => 255]],
                'min' => ['最低费用', ['maxlength' => 255]],
                'max' => ['费用上限', ['maxlength' => 255]],
                'base_fee' => ['基础费用', ['maxlength' => 255]],
                'status' => ['状态', ['inkey' => static::$statusSelector]],
            ],
        ];
        return $rule;
    }

    /**
     * 获取配置
     * @param integer $route 通道编号
     * @param string $merchant 商户号
     * @param integer $paytype 配置类型
     * @return array
     */
    public static function finder($route, $merchant, $paytype = Platform::PaytypeDebit)
    {
        $paytypes = [$paytype, Platform::PaytypeFitAll];
        $configuration = static::find()->where(['platform_id' => $route, 'paytype' => $paytypes, 'merchant_id' => $merchant])
            ->andWhere(['deleted_at' => '0', 'status' => static::StatusOnline])
            ->orderBy(['paytype' => 'desc', 'id' => 'desc'])->one();
        if(empty($configuration)) {
            return false;
        }
        return $configuration->builder();
    }

    /**
     * 格式化配置参数
     * @return array
     */
    public function builder()
    {
        $params = [
            'partnerId' => $this->merchant_id,
            'requestUri' => $this->request_uri,
            'privateKey' => base64_decode($this->private_key),
            'privatePassword' => $this->private_password,
            'publicKey' => base64_decode($this->public_key),
            'feeRule' => [
                'rate' => json_decode($this->rate, true),
                'min' => json_decode($this->min, true),
                'max' => json_decode($this->max, true),
                'baseFee' => json_decode($this->base_fee, true),
            ],
        ];
        $oT = json_decode($this->configuration, true);
        if(empty($oT)) {
            return $params;
        }
        foreach($oT as $key => $value) {
            $params[$key] = $value;
        }
        return $params;
    }
}
