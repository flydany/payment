<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ProjectMerchant".
 */
class MerchantBank extends ActiveRecord {
    
    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['merchant_id', 'bank_id', 'paytype', 'priority', 'holiday_priority', 'single_limit', 'day_limit', 'month_limit', 'day_count', 'month_count'], 'required'],
            [['merchant_id', 'bank_id', 'paytype', 'priority', 'holiday_priority', 'single_limit', 'day_limit', 'month_limit', 'day_count', 'month_count', 'limiter_threshold', 'admin_id', 'status', 'deleted_at'], 'integer'],
            [['holiday_times', 'workday_times', 'weekend_times'], 'string', 'max' => 512],
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
            'merchant_id' => '商户编号',
            'bank_id' => '银行',
            'paytype' => '类型',
            'priority' => '工作日优先级',
            'holiday_priority' => '非工作日优先级',
            'single_limit' => '单笔限额(元)',
            'day_limit' => '单日限额(元)',
            'month_limit' => '单月限额(元)',
            'day_count' => '单日次数',
            'month_count' => '单月次数',
            'limiter_threshold' => '限流阀值',
            'holiday_times' => '节假日时间',
            'workday_times' => '工作日时间',
            'weekend_times' => '休息日时间',
            'admin_id' => '操作者',
            'status' => '状态',
            'remark' => '备注',
            'deleted_at' => '删除时间',
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
                'project_id' => ['项目', ['maxlength' => 255, 'required']],
                'identity' => ['身份标识', ['inkey' => static::$identitySelector, 'required']],
                'name' => ['联系人', ['maxlength' => 64, 'required']],
                'mobile' => ['手机号', ['maxlength' => 32, 'required']],
                'email' => ['邮箱', ['maxlength' => 255, 'required']],
            ],
        ];
        return $rule;
    }
}