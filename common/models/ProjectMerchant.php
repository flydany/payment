<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "ProjectMerchant".
 */
class ProjectMerchant extends ActiveRecord {
    
    const StatusNormal = '0';
    const StatusForbidden = '1';
    public static $statusSelector = [
        self::StatusNormal => 'normal',
        self::StatusForbidden => 'forbidden',
    ];

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['project_id', 'merchant_id', 'status'], 'required'],
            [['project_id', 'platform_id', 'merchant_id', 'paytype', 'status'], 'integer'],
            [['remark'], 'string', 'max' => 255],
        ];
    }
    /**
     * 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'project_id' => 'project id',
            'platform_id' => 'platform id',
            'merchant_id' => 'merchant id',
            'paytype' => 'payment type',
            'status' => 'status',
            'remark' => 'remark',
        ];
    }
    /**
     * update & insert data check config for html
     * @param string $type 页面操作类型
     * @return string|array
     */
    public static function flyer($type = 'update')
    {
        $rule = [
            'param' => [
                'project_id' => ['project id', ['int', 'required']],
                // 'platform_id' => ['platform id', ['inkey' => array_keys(Platform::$platformSelector), 'required']],
                'merchant_id' => ['merchant id', ['int', 'required']],
                // 'paytype' => ['payment type', ['in' => array_keys(Platform::$paytype), 'required']],
                'status' => ['status', ['in' => array_keys(static::$statusSelector), 'required']],
                'remark' => ['remark', ['maxlength' => 255, 'required']],
            ],
        ];
        return $rule;
    }

    /**
     * 更新前初始化商户配置的信息
     * @return boolean
     */
    public function beforeSave($insert)
    {
        $this->platform_id = $this->merchant->platform_id;
        // $this->paytype = $this->merchant->paytype;
        return parent::beforeSave($insert);
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
     * 获取商户号配置
     * @return object
     */
    public function getMerchant()
    {
        return $this->hasOne(Merchant::className(), ['id' => 'merchant_id']);
    }

    /**
     * 判断当前用户是否有此项目的权限
     * @return boolean
     */
    public function getHasPermission()
    {
        if(Yii::$app->admin->isSupper) {
            return true;
        }
        return $this->project->hasPermission && $this->merchant->hasPermission;
    }

    /**
     * 获取银行限额
     * @return object
     */
    public function getMerchantBanks()
    {
        return $this->hasMany(MerchantBank::className(), ['and', ['platform_id' => 'platform_id', 'paytype' => 'paytype'], ['or', ['merchant_id' => 'merchant_id'], ['merchant_id' => '']]]);

        return MerchantBank::find()->where(['platform_id' => $this->platform_id, 'paytype' => $this->paytype, 'status' => static::StatusUsable])
            ->andWhere(['or', ['merchant_id' => $this->merchant_id], ['merchant_id' => '']])
            ->asArray()->all();
    }

    public static function selector($projectId = '')
    {
        return ArrayHelper::map(
            ProjectMerchant::find()->select('id, title')->andFilterWhere(['project_id' => $projectId])->filterResource(AdminResource::TypeProject)->asArray()->all(),
            'id', 'title'
        );
    }
}