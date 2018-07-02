<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ProjectMerchant".
 */
class ProjectMerchant extends ActiveRecord {
    
    const StatusUsable = 0;
    const StatusForbidden = 1;
    public static $statusSelector = [
        self::StatusUsable => ['title' => '可用', 'status' => 'green'],
        self::StatusForbidden => ['title' => '禁用', 'status' => 'red'],
    ];

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['project_id', 'platform_id', 'merchant_id', 'paytype', 'status'], 'required'],
            [['project_id', 'platform_id', 'paytype', 'status'], 'integer'],
            [['merchant_id'], 'string', 'max' => 64],
            [['remark'], 'string', 'max' => 255],
        ];
    }
    /**
     * 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'project_id' => '项目',
            'platform_id' => '通道号',
            'merchant_id' => '商户号',
            'paytype' => '类型',
            'status' => '状态',
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
                'project_id' => ['项目', ['maxlength' => 255, 'required']],
                'platform_id' => ['通道号', ['inkey' => Platform::$platformSelector, 'required']],
                'merchant_id' => ['商户号', ['maxlength' => 64, 'required']],
                'paytype' => ['支付类型', ['maxlength' => Platform::$paytype, 'required']],
                'status' => ['状态', ['inkey' => static::$statusSelector, 'required']],
                'remark' => ['备注', ['maxlength' => 255, 'required']],
            ],
        ];
        return $rule;
    }
    
    /**
     * 获取项目
     * @return object
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['project_id' => 'id']);
    }
    
    /**
     * 获取商户号配置
     * @return object
     */
    public function getMerchant()
    {
        return $this->hasOne(Merchant::className(), ['project_id' => 'id', 'merchant_id' => 'merchant_id', 'paytype' => 'paytype']);
    }

    /**
     * 获取银行限额
     * @return object
     */
    public function getMerchantBanks()
    {
        return $this->hasMany(MerchantBank::className(), ['and', ['platform_id' => 'platform_id', 'paytype' => 'paytype'], ['or', ['merchant_id' => 'merchant_id'], ['merchant_id' => '']]]);

        return MerchantBank::find()->where(['platform_id' => $this->platform_id, 'paytype' => $this->paytype, 'status' => static::StatusUsable])
            ->andWhere(['or', ['merchant_id' => $this->merchant_id], ['merchant_id' => '']])
            ->asArray()->all();
    }
}