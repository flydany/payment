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
        self::StatusInit => 'wait',
        self::StatusPaying => 'paying',
        self::StatusSuccess => 'success',
        self::StatusFailed => 'failed',
    ];

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['project_id', 'platform_id', 'project_merchant_id', 'amount', 'fee', 'order_number', 'source_order_number', 'status'], 'required'],
            [['project_id', 'platform_id', 'project_merchant_id', 'amount', 'fee', 'success_at', 'deleted_at'], 'integer'],
            [['order_number'], 'string', 'max' => 32],
            [['order_number'], 'unique'],
            [['project_id', 'source_order_number'], 'unique', 'targetAttribute' => ['project_id', 'source_order_number']],
            [['outer_order_number', 'source_order_number'], 'string', 'max' => 64],
            [['pay_summary', 'remark'], 'string', 'max' => 255],
        ];
    }
    /**
     * 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'project_id' => 'project number',
            'platform_id' => 'platform number',
            'project_merchant_id' => 'project merchant',
            'order_number' => 'order number',
            'amount' => 'amount',
            'fee' => 'fee',
            'status' => 'status',
            'success_date' => 'success date',
            'success_at' => 'success at',
            'source_order_number' => 'source order number',
            'outer_order_number' => 'outer order number',
            'pay_summary' => 'pay summary',
            'remark' => 'remark',
            'deleted_at' => 'deleted at',
        ];
    }

    /**
     * 获取项目
     * @return Project
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    /**
     * 获取项目商户配置
     * @return ProjectMerchant
     */
    public function getProjectMerchant()
    {
        return $this->hasOne(ProjectMerchant::className(), ['id' => 'project_merchant_id']);
    }

    /**
     * 获取商户配置
     * @return Merchant
     */
    public function getMerchant()
    {
        return Merchant::find()->where(['id' => $this->projectMerchant->merchant_id])->one();
    }

    /**
     * 获取银行卡信息
     * @return BindCard
     */
    public function getBindCard()
    {
        return $this->hasOne(BindCard::className(), ['id' => 'bind_card_id']);
    }
    
    /**
     * 设置充值成功
     * @param string $remark 备注
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
        return $this->transaction(function($db) use ($remark) {
            if( ! $this->cSave(['status' => $this->status])) {
                throw new \Exception('update recharge status error');
            }
            // 记录日志
            if( ! $this->logger(RechargeLog::HandlerAdmin, '充值成功: '.$remark)) {
                throw new \Exception('create recharge log error');
            }
            return true;
        });
    }
    
    /**
     * 充值失败
     * @param string $remark 备注
     * @return boolean
     */
    public function failed($remark = '')
    {
        if($this->deleted()) {
            return false;
        }
        $this->remark = $remark;
        $this->status = static::StatusFailed;
        $this->updated_at = time();
        return $this->transaction(function($db) use ($remark) {
            if( ! $this->cSave(['status' => $this->status])) {
                throw new \Exception('update recharge status error');
            }
            // 记录日志
            if( ! $this->logger(RechargeLog::HandlerAdmin, '充值失败: '.$remark)) {
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
        return strtoupper('R'.base_convert((microtime(true) * 10000).rand(10000, 99999).rand(10000, 99999), 10, 36));
    }
}