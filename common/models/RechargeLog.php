<?php

namespace common\models;

use Yii;
use common\helpers\Util;

/**
 * This is the model class for table "RechargeLog".
 */
class RechargeLog extends ActiveRecord {

    const StatusSuccess = '0';
    const StatusFailed = '1';
    public static $statusSelector = [
        self::StatusSuccess => 'success',
        self::StatusFailed => 'failed',
    ];

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['recharge_id', 'event', 'operation', 'ip'], 'required'],
            [['recharge_id', 'admin_id', 'status'], 'integer'],
            [['ip'], 'string', 'max' => 32],
            [['event'], 'string', 'max' => 64],
            [['operation'], 'string', 'max' => 65535],
        ];
    }
    /**
     * 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'recharge_id' => 'recharge number',
            'event' => 'event',
            'admin_id ' => 'admin',
            'ip' => 'ip',
            'operation' => 'operation',
        ];
    }

    /**
     * 获取管理员信息
     * @return \yii\db\ActiveQuery
     */
    public function getOperator()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }

    /**
     * 添加日志
     * @param $param array 数据数组
     * @return boolean
     */
    public static function logger($param)
    {
        $logger = new static();
        $param['admin_id'] = (Yii::$app->isLogin() && Yii::$app->admin) ? Yii::$app->admin->id : 0;
        $param['ip'] = Util::clientIp();
        $logger->loads($param);
        return $logger->save();
    }
}