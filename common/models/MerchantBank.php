<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "MerchantBank".
 */
class MerchantBank extends ActiveRecord {

    const StatusNormal = '0';
    const StatusForbidden = '1';
    public static $statusSelector = [
        self::StatusNormal =>  'normal',
        self::StatusForbidden => 'forbidden',
    ];

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['platform_id', 'bank_id', 'paytype', 'priority', 'weekend_priority', 'single_amount', 'day_amount', 'month_amount', 'day_count', 'month_count'], 'required'],
            [['platform_id', 'bank_id', 'paytype', 'priority', 'weekend_priority', 'priority_threshold', 'single_amount', 'day_amount', 'month_amount', 'day_count', 'month_count', 'admin_id', 'status', 'deleted_at'], 'integer'],
            [['merchant_number'], 'string', 'max' => 64],
            [['remark'], 'string', 'max' => 255],
            [['weekday_times', 'weekend_times', 'holiday_times'], 'string', 'max' => 512],
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
            'merchant_number' => 'merchant number',
            'bank_id' => 'bank id',
            'paytype' => 'payment type',
            'priority' => 'weekday priority',
            'weekend_priority' => 'weekend priority',
            'priority_threshold' => 'priority threshold',
            'single_amount' => ' amount single limit',
            'day_amount' => 'amount day limit',
            'month_amount' => 'amount month limit',
            'day_count' => 'count day limit',
            'month_count' => 'count month limit',
            'weekday_times' => 'workday times',
            'weekend_times' => 'weekend times',
            'holiday_times' => 'holiday times',
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
                'merchant_number' => ['merchant id', ['maxlength' => 64]],
                'paytype' => ['payment type', ['in' => array_keys(Platform::$paytypeSelector), 'required']],
                'bank_id' => ['bank id', ['int', 'required']],
                'priority' => ['weekday priority', ['int', 'required']],
                'weekend_priority' => ['weekend priority', ['int', 'required']],
                'priority_threshold' => ['threshold limit', ['int', 'required']],
                'single_amount' => ['amount single limit', ['float', 'required']],
                'day_amount' => ['amount day limit', ['float', 'required']],
                'month_amount' => ['amount day limit', ['float', 'required']],
                'day_count' => ['count day limit', ['int', 'required']],
                'month_count' => ['count month limit', ['int', 'required']],
                'weekday_start' => ['weekday usable start time', ['date' => 'H:i:s']],
                'weekday_end' => ['weekday usable end time', ['date' => 'H:i:s']],
                'weekend_start' => ['weekend usable start time', ['date' => 'H:i:s']],
                'weekend_end' => ['weekend usable end time', ['date' => 'H:i:s']],
                'holiday_start' => ['holiday usable start time', ['date' => 'H:i:s']],
                'holiday_end' => ['holiday usable end time', ['date' => 'H:i:s']],
                'remark' => ['remark', ['maxlength' => 255]],
                'status' => ['status', ['in' => array_keys(static::$statusSelector), 'required']],
            ],
        ];
        return $rule;
    }

    /**
     * 设置操作人
     * @param bool $insert 是否创建
     * @return boolean
     */
    public function beforeSave($insert)
    {
        $this->admin_id = (Yii::$app->isLogin() && ! empty(Yii::$app->admin)) ? Yii::$app->admin->id : 0;
        return parent::beforeSave($insert);
    }

    /**
     * 获取操作人
     * @return \yii\db\ActiveQuery
     */
    public function getOperator()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }

    /**
     * 判断用户是否有权限
     * @return boolean
     */
    public function getHasPermission()
    {
        if(Yii::$app->admin->isSupper) {
            return true;
        }
        return Merchant::find()->select('merchant_number')
            ->where(['platform_id' => $this->platform_id, 'merchant_number' => ['', $this->merchant_number], 'paytype' => [0, $this->paytype], 'id' => Yii::$app->admin->getPowers(AdminResource::TypeMerchant)])
            ->exists();
    }
}