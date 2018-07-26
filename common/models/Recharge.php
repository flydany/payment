<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Recharge".
 */
class Recharge extends ActiveRecord {

    // status defined
    const StatusInit = '0';
    const StatusAuthing = '21';
    const StatusAuthSuccess = '20';
    const StatusAuthFailed = '22';
    const StatusPaying = '91';
    const StatusSuccess = '90';
    const StatusFailed = '92';
    public static $statusSelector = [
        self::StatusInit => 'wait',
        self::StatusAuthing => 'apply auth',
        self::StatusAuthSuccess => 'auth success',
        self::StatusAuthFailed => 'auth failed',
        self::StatusPaying => 'paying',
        self::StatusSuccess => 'success',
        self::StatusFailed => 'failed',
    ];

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['project_id', 'platform_id', 'project_merchant_id', 'user_id', 'amount', 'fee', 'bind_card_id', 'bank_id', 'order_number', 'source_order_number', 'status'], 'required'],
            [['project_id', 'platform_id', 'project_merchant_id', 'amount', 'fee', 'bind_card_id', 'bank_id', 'success_date', 'success_at', 'deleted_at'], 'integer'],
            [['order_number', 'user_id'], 'string', 'max' => 32],
            [['outer_order_number', 'source_order_number', 'postscript', 'error_code'], 'string', 'max' => 64],
            [['remark'], 'string', 'max' => 255],
            [['order_number'], 'unique'],
            [['project_id', 'source_order_number'], 'unique', 'targetAttribute' => ['project_id', 'source_order_number']],
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
            'project_merchant_id' => 'project platform',
            'order_number' => 'order number',
            'amount' => 'amount',
            'fee' => 'fee',
            'user_id' => 'user number',
            'bind_card_id' => 'bind card number',
            'bank_id' => 'bank number',
            'status' => 'status',
            'success_date' => 'success date',
            'success_at' => 'success at',
            'error_code' => 'error code',
            'source_order_number' => 'source order number',
            'outer_order_number' => 'outer order number',
            'pay_summary' => 'pay summary',
            'remark' => 'remark',
            'deleted_at' => 'deleted at',
        ];
    }
    /**
     * update & insert data check config for html
     * @param $type string 页面操作类型
     * @param $encodeJson boolean 是否转成JSON字符串
     * @return string | array
     */
    public static function flyer($type = 'update')
    {
        // jsut search
        $rule = [
            'param' => [
                'project_id' => ['project', ['int', 'required']],
                'platform_id' => ['platform', ['int', 'required']],
                'project_merchant_id' => ['project merchant number', ['int', 'required']],
                'order_number' => ['order number', ['maxlength' => 32, 'required']],
                'amount' => ['recharge amount', ['int', 'required']],
                'fee' => ['recharge fee', ['int', 'required']],
                'user_id' => ['user_number', ['maxlength' => 32, 'required']],
                'bind_card_id' => ['bind card id', ['int', 'required']],
                'bank_id' => ['bank number', ['in' => array_keys(Platform::$bankSelector), 'required']],
                'success_date' => ['success date', ['date' => 'Ymd']],
                'success_at' => ['success at', ['date' => 'Y-m-d H:i:s']],
                'error_code' => ['error code', ['maxlength' => 64]],
                'source_order_number' => ['source order number', ['maxlength' => 64, 'required']],
                'outer_order_number' => ['outer order number', ['maxlength' => 64]],
                'postscript' => ['postscript', ['maxlength' => 64]],
                'remark' => ['remark', ['maxlength' => 255]],
                'status' => ['recharge status', ['in' => array_keys(static::$statusSelector)]],
            ],
        ];
        return $rule;
    }

    /**
     * 更新数据钩子
     * @param boolean $insert
     * @return boolean
     */
    public function afterSave($insert, $changedAttributes)
    {
        $changed = [];
        foreach($changedAttributes as $key => $value) {
            if($this->getOldAttribute($key) != $value) {
                $changed[$key] = ['before' => $this->getOldAttribute($key), 'after' => $value];
            }
        }
        $this->logger($insert ? 'creator' : 'editor', 'change attributes: '.json_encode($changed));
        return parent::afterSave($insert, $changedAttributes);
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
     * 判断是否有权限操作
     * @return boolean
     */
    public function getHasPermission()
    {
        return $this->project->hasPermission;
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
            if( ! $this->cSave('status')) {
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
            if( ! $this->cSave('status')) {
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
     * @param $event int 操作类型
     * @param $operation string 备注
     * @return boolean
     */
    public function logger($event, $operation = '')
    {
        $param = [
            'event' => $event,
            'operation' => $operation,
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