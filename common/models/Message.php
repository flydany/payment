<?php

namespace common\models;

use Yii;
use yii\db\Exception;

class Message extends \common\models\ActiveRecord {

    // 消息类型
    const TypeMessage = 0;
    const TypeRecharge = 1;
    const TypeRechargeRefuse = 11;
    const TypeWithdraw = 2;
    const TypeWithdrawApply = 21;
    const TypeWithdrawRefuse = 22;
    const TypeEmploymentApplicant = 3;
    const TypeEmploymentRecruit = 31;
    const TypeEmploymentPay = 32;
    const TypeEarn = 4;
    const TypeBondPay = 51;
    const TypeReturnBondPay = 51;
    public static $typeSelector = [
        self::TypeMessage => ['title' => '消息', 'status' => 'blue'],
        self::TypeRecharge => ['title' => '充值', 'status' => 'green'],
        self::TypeRechargeRefuse => ['title' => '拒绝充值', 'status' => 'red'],
        self::TypeWithdraw => ['title' => '提现', 'status' => 'green'],
        self::TypeWithdrawApply => ['title' => '提现申请', 'status' => 'orange'],
        self::TypeWithdrawRefuse => ['title' => '拒绝提现', 'status' => 'red'],
        self::TypeEmploymentApplicant => ['title' => '简历申请', 'status' => 'purple'],
        self::TypeEmploymentRecruit => ['title' => '招聘', 'status' => 'purple'],
        self::TypeEmploymentPay => ['title' => '佣金支付', 'status' => 'purple'],
        self::TypeEarn => ['title' => '到账薪酬', 'status' => 'green'],
        self::TypeBondPay => ['title' => '雇佣金缴纳', 'status' => 'purple'],
        self::TypeReturnBondPay => ['title' => '雇佣金退还', 'status' => 'green'],
    ];

    // 状态
    const StatusUnread = 0;
    const StatusRead = 1;
    public static $statusSelector = [
        self::StatusUnread => ['title' => '未读', 'status' => 'blue'],
        self::StatusRead => ['title' => '已读', 'status' => 'green'],
    ];

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['type', 'receiver_id', 'title', 'content'], 'required'],
            [['type', 'sender_id', 'receiver_id', 'status'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['content'], 'string', 'max' => 1024],
        ];
    }
    /**
     * 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'type' => '消息类型',
            'sender_id' => '发件人',
            'receiver_id' => '收件人',
            'title' => '标题',
            'content' => '内容',
            'status' => '状态',
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
        // jsut search
        $rule = [
            'param' => [
                'type' => ['消息类型', ['inkey' => static::$typeSelector, 'required']],
                'receiver_id' => ['收件人', ['int', 'required']],
                'sender_id' => ['发件人', ['int']],
                'title' => ['标题', ['maxlength' => 255, 'required']],
                'content' => ['消息体', ['maxlength' => 1024, 'required']],
                'status' => ['状态', ['inkey' => static::$statusSelector]],
            ],
        ];
        return $rule;
    }
    
    /**
     * 获取接收人信息
     * @return object
     */
    public function getReceiver()
    {
        return $this->hasOne(User::className(), ['id' => 'receiver_id']);
    }
    
    /**
     * 获取发件人信息
     * @return object
     */
    public function getSender()
    {
        return $this->hasOne(User::className(), ['id' => 'sender_id']);
    }
    
    /**
     * 设置消息已读
     * @return boolean
     */
    public function reador()
    {
        $this->status = static::StatusRead;
        $this->updated_at = time();
        return $this->save();
    }
    
    /**
     * 发送站内信
     * @param $params array 数据数组
     * @param $type int 站内信类型
     * @param $renderParams array 消息体渲染参数
     * @return boolean
     */
    public static function messager($params, $type = '', $renderParams = [])
    {
        if($type) {
            $params['type'] = $type;
            $body = static::template($type, $renderParams);
            $params['title'] = $body['title'];
            $params['content'] = $body['content'];
        }
        $messager = new static();
        $messager->load([static::className() => $params], static::className());
        return $messager->save();
    }
    
    /**
     * 渲染消息体
     * @param $type int 消息类型
     * @param $params array 消息参数
     * @return array [title, content]
     */
    public static function template($type, $params)
    {
        $bodys = [
            static::TypeRecharge => [
                'title' => '充值到账',
                'content' => '您于:time充值的:amount元已经到账，请您查收！',
                'params' => ['time', 'amount'],
            ],
            static::TypeRechargeRefuse => [
                'title' => '拒绝充值申请',
                'content' => '您于:time申请充值的:amount元已经被拒绝，请您知悉！',
                'params' => ['time', 'amount'],
            ],
            static::TypeWithdraw => [
                'title' => '提现到账',
                'content' => '您于:time申请的:amount元提现已经打款成功，请您知悉！',
                'params' => ['time', 'amount'],
            ],
            static::TypeWithdrawApply => [
                'title' => '提现申请',
                'content' => '您于:time提交了一笔:amount元的提现申请，我们正以最快的速度进行处理，请您知悉！',
                'params' => ['time', 'amount'],
            ],
            static::TypeWithdrawRefuse => [
                'title' => '拒绝提现申请',
                'content' => '您于:time提交的:amount元提现申请已经被拒绝，请您知悉！',
                'params' => ['time', 'amount'],
            ],
            static::TypeEmploymentApplicant => [
                'title' => '有新的简历',
                'content' => '您于:time提交的招聘信息有新的简历投递，请您知悉！',
                'params' => ['time'],
            ],
            static::TypeEmploymentRecruit => [
                'title' => '有新的招聘响应',
                'content' => '您于:time申请的招聘:title已经被雇主采纳，正在等待雇主打款，请您知悉！',
                'params' => ['time', 'title'],
            ],
            static::TypeEmploymentPay => [
                'title' => '佣金消费',
                'content' => '您于:time支付了:amount的雇佣佣金，请您知悉！',
                'params' => ['time', 'amount'],
            ],
            static::TypeEarn => [
                'title' => '薪酬到账',
                'content' => '您于:time到账一笔:amount元的薪酬，请您知悉！',
                'params' => ['time', 'amount'],
            ],
            static::TypeBondPay => [
                'title' => '保证金缴纳',
                'content' => '您于:time缴纳了:amount元的保证金，请您知悉！',
                'params' => ['time', 'amount'],
            ],
            static::TypeReturnBondPay => [
                'title' => '保证金退还',
                'content' => '您于:time到账一笔:amount元的保证金退款，请您知悉！',
                'params' => ['time', 'amount'],
            ],
        ];
        $title = $bodys[$type]['title'];
        $content = $bodys[$type]['content'];
        foreach($bodys[$type]['params'] as $key) {
            if(in_array($key, ['time', 'created_at', 'updated_at', 'success_at'])) {
                if(is_numeric($params[$key])) {
                    $params[$key] = date('Y-m-d H:i:s', $params[$key]);
                }
            }
            if(in_array($key, ['amount', 'money'])) {
                $params[$key] = number_format($params[$key] / 100, 2);
            }
            $title = str_replace(":{$key}", $params[$key], $title);
            $content = str_replace(":{$key}", $params[$key], $content);
        }
        return ['title' => $title, 'content' => $content];
    }
}