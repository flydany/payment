<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Debit".
 */
class Debit extends ActiveRecord {

    // Debit's status defined
    const StatusInit = '0';
    const StatusPaying = '91';
    const StatusSuccess = '90';
    const StatusFailed = '92';
    public static $statusSelector = [
        self::StatusInit => ['title' => '待扣款', 'status' => 'blue'],
        self::StatusPaying => ['title' => '扣款中', 'status' => 'purple'],
        self::StatusSuccess => ['title' => '扣款成功', 'status' => 'green'],
        self::StatusFailed => ['title' => '扣款失败', 'status' => 'red'],
    ];

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['project_id', 'merchant_id', 'order_number', 'source_order_number', 'user_id', 'amount', 'status'], 'required'],
            [['project_id', 'merchant_id', 'user_id', 'amount', 'fee', 'success_at', 'deleted_at'], 'integer'],
            [['order_number'], 'string', 'max' => 32],
            [['order_number', 'source_order_number'], 'unique'],
            [['outer_order_number', 'source_order_number'], 'string', 'max' => 64],
            [['summary', 'remark'], 'string', 'max' => 255],
        ];
    }
    /**
     * 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'project_id' => '项目编号',
            'merchant_id' => '商户编号',
            'order_number' => '订单号',
            'source_order_number' => '来源方订单号',
            'card_id' => '关联卡编号',
            'user_id' => '用户',
            'amount' => '金额',
            'fee' => '手续费',
            'success_at' => '成功时间',
            'outer_order_number' => '第三方订单号',
            'summary' => '付款信息',
            'remark' => '备注',
            'status' => '状态',
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
                throw new \Exception('update Debit status error');
            }
            // 记录日志
            if( ! $this->logger(DebitLog::HandlerAdmin, '已完成充值操作')) {
                throw new \Exception('create Debit log error');
            }
            // 更新用户账户表
            if( ! $this->userAccount->Debit($this->amount)) {
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
                throw new \Exception('update Debit status error');
            }
            // 记录日志
            if( ! $this->logger(DebitLog::HandlerAdmin, '拒绝充值申请')) {
                throw new \Exception('create Debit log error');
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
            'Debit_id' => $this->id,
        ];
        return DebitLog::logger($param);
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
        $Debit = new static();
        $Debit->loads($params);
        if( ! ($Debit->validate() && $Debit->save())) {
            Yii::info('创建充值订单异常：'.json_encode($Debit->getErrors()));
            return false;
        }
        $Debit->logger(DebitLog::HandlerUser, '创建充值订单');
        return $Debit;
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