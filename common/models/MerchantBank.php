<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "MerchantBank".
 */
class MerchantBank extends ActiveRecord {
    
    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['platform_id', 'merchant_id', 'merchant_number', 'bank_id', 'paytype', 'priority', 'holiday_priority', 'single_limit', 'day_limit', 'month_limit', 'day_count', 'month_count'], 'required'],
            [['platform_id', 'merchant_id', 'bank_id', 'paytype', 'priority', 'holiday_priority', 'single_limit', 'day_limit', 'month_limit', 'day_count', 'month_count', 'limit_threshold', 'admin_id', 'status', 'deleted_at'], 'integer'],
            [['merchant_number'], 'string', 'max' => 64],
            [['remark'], 'string', 'max' => 255],
            [['holiday_times', 'workday_times', 'weekend_times'], 'string', 'max' => 512],
        ];
    }
    /**
     * 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'platform_id' => 'platform id',
            'merchant_id' => 'merchant id',
            'merchant_number' => 'merchant number',
            'bank_id' => 'bank id',
            'paytype' => 'payment type',
            'priority' => 'workday priority',
            'holiday_priority' => 'holiday priority',
            'single_limit' => ' amount single limit',
            'day_limit' => 'amount day limit',
            'month_limit' => 'amount month limit',
            'day_count' => 'count day limit',
            'month_count' => 'count month limit',
            'limit_threshold' => 'threshold limit',
            'holiday_times' => 'holiday times',
            'workday_times' => 'workday times',
            'weekend_times' => 'weekend times',
            'admin_id' => 'operator',
            'status' => 'status',
            'remark' => 'remark',
            'deleted_at' => 'deleted at',
        ];
    }
    /**
     * update & insert data check config for html
     * @param $type string 页面操作类型
     * @param $encodeJson boolean 是否转成json串
     * @return string / array
     */
    public static function flyer($type = 'update')
    {
        $rule = [
            'param' => [
                'platform_id' => ['platform', ['int', 'required']],
                'merchant_id' => ['merchant id', ['maxlength' => 64, 'required']],
                'bank_id' => ['bank id', ['int', 'required']],
                'paytype' => ['payment type', ['in' => array_keys(Platform::$paytypeSelector), 'required']],
                'priority' => ['workday priority', ['int', 'required']],
                'holiday_priority' => ['holiday priority', ['int', 'required']],
                'single_limit' => ['amount single limit', ['int', 'required']],
                'day_limit' => ['amount day limit', ['int', 'required']],
                'month_limit' => ['amount day limit', ['int', 'required']],
                'day_count' => ['count day limit', ['int', 'required']],
                'month_count' => ['count month limit', ['int', 'required']],
                'limit_threshold' => ['threshold limit', ['int', 'required']],
                'holiday_times' => ['holiday usable times', ['json']],
                'workday_times' => ['workday usable times', ['json']],
                'weekend_times' => ['weekend usable times', ['json']],
                'admin_id' => ['operator', ['int']],
                'remark' => ['remark', ['maxlength' => 255]],
                'deleted_at' => ['deleted at', ['int']],
                'status' => ['status', ['in' => array_keys(static::$statusSelector), 'required']],
            ],
        ];
        return $rule;
    }
}