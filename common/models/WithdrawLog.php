<?php

namespace common\models;

use Yii;
use common\helpers\Util;

/**
 * This is the model class for table "WithdrawLog".
 */
class WithdrawLog extends ActiveRecord {

    // @name 日志操作人类型定义
    const HandlerUser = 0;
    const HandlerAdmin = 1;
    const HandlerSystem = 2;
    const HandlerOuter = 3;
    public static $handlerSelector = [
        self::HandlerUser => ['title' => '用户', 'status' => 'green'],
        self::HandlerAdmin => ['title' => '管理员', 'status' => 'purple'],
        self::HandlerSystem => ['title' => '系统', 'status' => 'blue'],
        self::HandlerOuter => ['title' => '第三方', 'status' => 'red'],
    ];

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['withdraw_id', 'handler', 'remark'], 'required'],
            [['withdraw_id', 'handler', 'operator'], 'integer'],
            [['ip'], 'string', 'max' => 32],
            [['remark'], 'string', 'max' => 65535],
        ];
    }
    /**
     * @name 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'withdraw_id' => '提现记录',
            'handler' => '操作类型',
            'ip' => '请求IP',
            'operator' => '操作人',
            'remark' => '备注',
        ];
    }

    /**
     * @name 添加日志
     * @param $param array 数据数组
     * @return boolean
     */
    public static function logger($param)
    {
        $logger = new static();
        $param['operator'] = Yii::$app->isLogin() ? Yii::$app->admin['id'] : 0;
        $param['ip'] = Util::clientIp();
        $logger->load([static::className() => $param], static::className());
        return $logger->save();
    }
}