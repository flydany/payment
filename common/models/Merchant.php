<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

class Merchant extends ActiveRecord {
    
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
    const StatusNormal = '0';
    const StatusForbidden = '1';
    public static $statusSelector = [
        self::StatusNormal =>  'normal',
        self::StatusForbidden => 'forbidden',
    ];
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'platform_id', 'merchant_number','paytype'], 'required'],
            [['platform_id', 'paytype', 'status', 'deleted_at'], 'integer'],
            [['private_type'], 'string', 'max' => 8],
            [['domain','remark', 'rate', 'min', 'max', 'base_fee'], 'string', 'max' => 255],
            [['title', 'merchant_number','private_password'], 'string', 'max' => 64],
            [['private_key', 'public_key', 'parameters'], 'string', 'max' => 65535],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'title' => 'title',
            'platform_id' => 'platform number',
            'merchant_number' => 'merchant number',
            'paytype' => 'pay type',
            'request_uri' => 'request domain',
            'private_key' => 'private key',
            'private_password' => 'private key password',
            'private_type' => 'private key type',
            'public_key' => 'public key',
            'parameters' => 'parameters',
            'rate' => 'rate',
            'min' => 'min fee',
            'max' => 'max fee',
            'base_fee' => 'base fee',
            'status' => 'status',
            'deleted_at' => 'deleted at',
            'remark' => 'remark',
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
                'title' => ['title', ['maxlength' => 64, 'required']],
                'platform_id' => ['platform number', ['inkey' => Platform::$platformSelector, 'required']],
                'merchant_number' => ['merchant number', ['maxlength' => 64, 'required']],
                'paytype' => ['pay type', ['inkey' => Platform::$paytypeSelector, 'required']],
                'domain' => ['request domain', ['url', 'required']],
                'private_key' => ['private key', ['maxlength' => 65535]],
                'private_password' => ['private key password', ['maxlength' => 64]],
                'private_type' => ['private key type', ['inkey' => static::$privateTypeSelector]],
                'public_key' => ['public key', ['maxlength' => 65535]],
                'parameter_name' => ['parameter name', ['maxlength' => 64]],
                'parameter_value' => ['parameter value', ['maxlength' => 1024]],
                'remark' => ['remark', ['maxlength' => 255]],
                'rate' => ['rate', ['maxlength' => 255]],
                'min' => ['min fee', ['maxlength' => 255]],
                'max' => ['max fee', ['maxlength' => 255]],
                'base_fee' => ['base fee', ['maxlength' => 255]],
                'status' => ['status', ['inkey' => static::$statusSelector]],
            ],
        ];
        return $rule;
    }

    /**
     * 新建商户号，赋予创建人权限
     * @param boolean $insert 是否创建
     * @param array $changedAttributes 改变的属性
     * @return boolean|void
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if($insert && ( ! Yii::$app->admin->isSupper)) {
            AdminResource::creator(Yii::$app->admin->id, $this->id, AdminResource::TypeMerchant);
        }
    }

    /**
     * 获取项目已存在的负责人
     * @return array
     */
    public function getIdentities()
    {
        return AdminResource::find()->select('identity')->where(['item_id' => $this->id, 'type' => AdminResource::TypeMerchant])->column();
    }

    /**
     * 判断当前用户是否有此项目的权限
     * @return boolean
     */
    public function getHasPermission()
    {
        if(Yii::$app->admin->isSupper) {
            return true;
        }
        return AdminResource::find()->where(['item_id' => $this->id, 'identity' => array_merge(Yii::$app->admin->identities, [Yii::$app->admin->id]), 'type' => AdminResource::TypeMerchant])->exists();
    }

    /**
     * 获取配置
     * @param integer $route 通道编号
     * @param string $merchant 商户号
     * @param integer $paytype 配置类型
     * @return array
     */
    public static function configer($route, $merchant, $paytype = Platform::PaytypeDebit)
    {
        $paytypes = [$paytype, Platform::PaytypeFitAll];
        $parameters = static::find()->where(['platform_id' => $route, 'paytype' => $paytypes, 'merchant_id' => $merchant])
            ->andWhere(['deleted_at' => '0', 'status' => static::StatusOnline])
            ->orderBy(['paytype' => 'desc', 'id' => 'desc'])->one();
        if(empty($parameters)) {
            return false;
        }
        return $parameters->builder();
    }

    /**
     * 格式化配置参数
     * @return array
     */
    public function builder()
    {
        $params = [
            'partnerId' => $this->merchant_id,
            'domain' => $this->domain,
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
        $oT = json_decode($this->parameters, true);
        if(empty($oT)) {
            return $params;
        }
        foreach($oT as $key => $value) {
            $params[$key] = $value;
        }
        return $params;
    }

    /**
     * 获取商户列表
     * @return array
     */
    public static function selector()
    {
        return ArrayHelper::map(
            array_map(
                function($value) {
                    return ['id' => $value['id'], 'title' => $value['id'].'.'.$value['title'].'.'.$value['merchant_number']];
                },
                static::find()->select('id, title, merchant_number')->where(['status' => static::StatusNormal])->filterResource(AdminResource::TypeMerchant)->orderBy('id desc')->asArray()->all()),
            'id', 'title'
        );
    }
}
