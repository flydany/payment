<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ProjectApi".
 */
class ProjectApi extends ActiveRecord {

    // 状态敞亮
    const StatusNormal = '0';
    const StatusForbidden = '1';
    public static $statusSelector = [
        self::StatusNormal =>  'normal',
        self::StatusForbidden => 'forbidden',
    ];

    // 接口常量
    const ApiRecharge = '1';
    const ApiWithdraw = '2';
    const ApiAgreement = '3';
    public static $apiSelector = [
        self::ApiRecharge => 'recharge',
        self::ApiWithdraw => 'withdraw',
        self::ApiAgreement => 'agreement',
    ];

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['project_id', 'api'], 'required'],
            [['project_id', 'api', 'admin_id', 'deleted_at', 'status'], 'integer'],
            [['remark', 'parameters'], 'string', 'max' => 255],
            [['parameters'], 'string', 'max' => 65535],
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
            'api' => 'api',
            'parameters' => 'parameter config',
            'deleted_at' => 'deleted at',
            'remark' => 'remark',
            'status' => 'status',
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
                'project_id' => ['project number', ['int', 'required']],
                'api' => ['identity', ['in' => array_keys(static::$apiSelector), 'required']],
                'parameter_name' => ['parameter name', ['maxlength' => 64]],
                'parameter_value' => ['parameter value', ['maxlength' => 1024]],
                'remark' => ['email', ['maxlength' => 255, 'required']],
            ],
        ];
        return $rule;
    }

    /**
     * 获取项目
     * @return object
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }

    /**
     * 获取操作人
     * @return \yii\db\ActiveQuery
     */
    public function getOperator()
    {
        return $this->hasOne(Admin::className(), ['id' => 'admin_id']);
    }
}