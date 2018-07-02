<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Recharge".
 */
class Recharge extends ActiveRecord {

    // Cash's status defined
    const StatusInit = 0;
    const StatusPaying = 1;
    const StatusSuccess = 2;
    const StatusFailed = 3;
    public static $statusSelector = [
        self::StatusInit => ['title' => '待支付', 'status' => 'blue'],
        self::StatusPaying => ['title' => '付款中', 'status' => 'orange'],
        self::StatusSuccess => ['title' => '充值成功', 'status' => 'green'],
        self::StatusFailed => ['title' => '充值失败', 'status' => 'red'],
    ];

    // User's role defined
    const PlatformAlipay = 1;
    const PlatformWeChat = 2;
    public static $platformSelector = [
        self::PlatformAlipay => ['title' => '支付宝', 'status' => 'blue'],
        self::PlatformWeChat => ['title' => '微信', 'status' => 'green'],
    ];

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['user_id', 'platform', 'amount', 'fee', 'order_number', 'status', 'realname', 'mobile'], 'required'],
            [['user_id', 'platform', 'amount', 'fee', 'success_at', 'deleted_at'], 'integer'],
            [['mobile'], 'string', 'max' => 16],
            [['order_number', 'realname'], 'string', 'max' => 32],
            [['order_number'], 'unique'],
            [['outer_order_number', 'account', 'remark'], 'string', 'max' => 128],
        ];
    }
    /**
     * 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '用户',
            'order_number' => '订单号',
            'platform' => '充值方式',
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
     * 获取用户信息
     * @return object
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    
    /**
     * 获取用户账户信息
     * @return object
     */
    public function getUserAccount()
    {
        return $this->hasOne(UserAccount::className(), ['user_id' => 'user_id']);
    }
    
    /**
     * 充值是否完成
     * @return boolean
     */
    public function complete()
    {
        return in_array($this->status, [static::StatusSuccess, static::StatusFailed]);
    }
    
    /**
     * 设置充值成功
     * @param string 备注
     * @return boolean
     */
    public function success($remark = '')
    {
        if($this->deleted()) {
            return false;
        }
        if( ! in_array($this->status, [static::StatusInit])) {
            return false;
        }
        $this->remark = $remark;
        $this->status = static::StatusSuccess;
        $this->success_at = time();
        $this->updated_at = time();
        return $this->transaction(function($db) {
            if( ! $this->save()) {
                throw new \Exception('update recharge status error');
            }
            // 记录日志
            if( ! $this->logger(RechargeLog::HandlerAdmin, '已完成充值操作')) {
                throw new \Exception('create recharge log error');
            }
            // 更新用户账户表
            if( ! $this->userAccount->recharge($this->amount)) {
                throw new Exception('change balance failed');
            }
            return true;
        });
    }
    
    /**
     * 拒绝充值申请
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
        return $this->transaction(function($db) {
            if( ! $this->save()) {
                throw new \Exception('update recharge status error');
            }
            // 记录日志
            if( ! $this->logger(RechargeLog::HandlerAdmin, '拒绝充值申请')) {
                throw new \Exception('create recharge log error');
            }
            return true;
        });
    }
    
    /**
     * 添加日志
     * @param $type int 操作类型
     * @param $remark string 备注
     * @return boolean
     */
    public function logger($type, $remark = '')
    {
        $param = [
            'handler' => $type,
            'remark' => $remark,
            'recharge_id' => $this->id,
        ];
        return RechargeLog::logger($param);
    }
    
    /**
     * 创建充值订单
     * @param $params array 订单参数
     * @return mixed
     */
    public static function initialize($params)
    {
        if(empty($params)) {
            return false;
        }
        $params['user_id'] = Yii::$app->user->id;
        $params['order_number'] = static::generateOrderNumber();
        $params['status'] = static::StatusInit;
        return static::creator($params);
    }
    
    /**
     * 创建充值订单
     * @param $params array 订单参数
     * @return mixed
     */
    public static function creator($params)
    {
        if(empty($params)) {
            return false;
        }
        $recharge = new static();
        $recharge->loads($params);
        if( ! ($recharge->validate() && $recharge->save())) {
            Yii::info('创建充值订单异常：'.json_encode($recharge->getErrors()));
            return false;
        }
        $recharge->logger(RechargeLog::HandlerUser, '创建充值订单');
        return $recharge;
    }
    
    /**
     * 生成订单号
     * @return string
     */
    public static function generateOrderNumber()
    {
        return strtoupper('C'.base_convert((microtime(true) * 10000).rand(10000, 99999).rand(10000, 99999), 10, 36));
    }
}