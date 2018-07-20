<?php

namespace common\models;

use Yii;
use yii\db\Exception;

/**
 * This is the model class for table "Withdraw".
 */
class Withdraw extends ActiveRecord {

    // @name 状态
    const StatusInit = '0';
    const StatusWithdrawing = '1';
    const StatusSuccess = '2';
    const StatusFailed = '3';
    public static $statusSelector = [
        self::StatusInit => ['title' => '待审核', 'status' => 'blue'],
        self::StatusWithdrawing => ['title' => '提现中', 'status' => 'orange'],
        self::StatusSuccess => ['title' => '提现成功', 'status' => 'green'],
        self::StatusFailed => ['title' => '提现失败', 'status' => 'red'],
    ];

    // @name 类型
    const PlatformAlipay = '1';
    const PlatformWeChat = '2';
    public static $platformSelector = [
        self::PlatformAlipay => ['title' => '支付宝', 'status' => 'blue'],
        self::PlatformWeChat => ['title' => '微信', 'status' => 'green'],
    ];

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['user_id', 'platform', 'amount', 'fee', 'order_number', 'status'], 'required'],
            [['user_id', 'platform', 'amount', 'fee', 'success_at', 'deleted_at'], 'integer'],
            [['outer_order_number', 'account', 'remark'], 'string', 'max' => 128],
        ];
    }
    /**
     * @name 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户',
            'order_number' => '订单号',
            'platform' => '提现方式',
            'amount' => '金额',
            'fee' => '手续费',
            'status' => '状态',
            'success_at' => '成功时间',
            'outer_order_number' => '第三方订单号',
            'account' => '充值账号',
            'remark' => '备注',
            'deleted_at' => '删除时间',
        ];
    }

    /**
     * @name 获取用户信息
     * @return object
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @name 获取用户账户信息
     * @return object
     */
    public function getUserAccount()
    {
        return $this->hasOne(UserAccount::className(), ['user_id' => 'user_id']);
    }

    /**
     * @name 提现是否完成
     * @return boolean
     */
    public function complete()
    {
        return in_array($this->status, [static::StatusSuccess, static::StatusFailed]);
    }

    /**
     * @name 设置开始给用户打款状态
     * @param string 备注
     * @return boolean
     */
    public function withdrawing($remark = '')
    {
        if($this->deleted()) {
            return false;
        }
        if( ! in_array($this->status, [static::StatusInit])) {
            return false;
        }
        $this->remark = $remark;
        $this->status = static::StatusWithdrawing;
        $this->success_at = time();
        $this->updated_at = time();
        return $this->transaction(function($db) {
            if( ! $this->save()) {
                throw new \Exception('update withdraw status error');
            }
            // 记录日志
            if( ! $this->logger(WithdrawLog::HandlerAdmin, '开始给用户打款')) {
                throw new \Exception('create withdraw log error');
            }
            return true;
        });
    }

    /**
     * @name 设置提现成功
     * @param string 备注
     * @return boolean
     */
    public function success($remark = '')
    {
        if($this->deleted()) {
            return false;
        }
        if( ! in_array($this->status, [static::StatusWithdrawing])) {
            return false;
        }
        $this->remark = $remark;
        $this->status = static::StatusSuccess;
        $this->updated_at = time();
        return $this->transaction(function($db) {
            if( ! $this->save()) {
                throw new \Exception('update withdraw status error');
            }
            // 记录日志
            if( ! $this->logger(WithdrawLog::HandlerAdmin, '已完成打款操作')) {
                throw new \Exception('create withdraw log error');
            }
            // 更新用户账户表
            if( ! $this->userAccount->withdraw($this->amount)) {
                throw new Exception('change balance failed');
            }
            return true;
        });
    }

    /**
     * @name 拒绝提现申请
     * @param string 备注
     * @return boolean
     */
    public function refuse($remark = '')
    {
        if($this->deleted()) {
            return false;
        }
        $this->remark = $remark;
        $this->status = static::StatusFailed;
        $this->updated_at = time();
        return $this->transaction(function ($db) {
            if( ! $this->save()) {
                throw new \Exception('update withdraw status error');
            }
            // 记录日志
            if( ! $this->logger(WithdrawLog::HandlerAdmin, '拒绝提现申请')) {
                throw new \Exception('create withdraw log error');
            }
            // 更新用户账户表
            if( ! $this->designerAccount->refuseWithdraw($this->amount)) {
                throw new \Exception('update user account error');
            }
            return true;
        });
    }

    /**
     * @name 添加日志
     * @param $type int 操作类型
     * @param $remark string 备注
     * @return boolean
     */
    public function logger($type, $remark = '')
    {
        $param = [
            'handler' => $type,
            'remark' => $remark,
            'withdraw_id' => $this->id,
        ];
        return WithdrawLog::logger($param);
    }
    
    /**
     * 创建提现订单
     * @param $params array 订单参数
     * @return mixed
     */
    public static function creator($params)
    {
        if(empty($params)) {
            return false;
        }
        $withdraw = new static();
        $withdraw->user_id = Yii::$app->user->id;
        $withdraw->order_number = static::generateOrderNumber();
        $withdraw->loads($params);
        $withdraw->status = static::StatusInit;
        if( ! ($withdraw->validate() && $withdraw->save())) {
            return false;
        }
        $withdraw->logger(WithdrawLog::HandlerUser, '创建提现订单');
        return $withdraw;
    }
    
    /**
     * 生成订单号
     * @return string
     */
    public static function generateOrderNumber()
    {
        return strtoupper('W'.base_convert((microtime(true) * 10000).rand(10000, 99999).rand(10000, 99999), 10, 36));
    }
}