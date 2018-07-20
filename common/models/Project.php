<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Project".
 */
class Project extends ActiveRecord {

    const StatusNormal = '1';
    const StatusForbidden = '2';
    public static $statusSelector = [
        self::StatusNormal =>  '正常',
        self::StatusForbidden => '禁用',
    ];

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['title', 'effect_date', 'public_key', 'status'], 'required'],
            [['status', 'deleted_at'], 'integer'],
            [['effect_date'], 'string', 'max' => 16],
            [['title', 'remark'], 'string', 'max' => 255],
            [['public_key'], 'string', 'max' => 1024],
        ];
    }
    /**
     * 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'title' => 'title',
            'effect_date' => 'effect date',
            'public_key' => 'rsa public',
            'status' => 'status',
            'remark' => 'remark',
            'deleted_at' => 'deleted at',
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
                'title' => ['title', ['maxlength' => 255, 'required']],
                'effect_date' => ['effect date', ['date' => 'Y-m-d', 'required']],
                'public_key' => ['rsa public', ['maxlength' => 1024, 'required']],
                'remark' => ['remark', ['maxlength' => 255, 'required']],
                'status' => ['status', ['in' => array_keys(static::$statusSelector), 'required']],
            ],
        ];
        return $rule;
    }

    /**
     * 获取项目的商户号配置
     * @return array
     */
    public function getProjectMerchants()
    {
        return $this->hasMany(ProjectMerchant::className(), ['project_id' => 'id']);
    }
    public function getRechargeMerchants()
    {
        return $this->getProjectMerchants()->where(['paytype' => Platform::PaytypeRecharge]);
    }
    public function getWithdrawMerchants()
    {
        return $this->getProjectMerchants()->where(['paytype' => Platform::PaytypeWithdraw]);
    }
    public function getAgreementMerchants()
    {
        return $this->getProjectMerchants()->where(['paytype' => Platform::PaytypeAgreement]);
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
        return AdminResource::find()->where(['item_id' => $this->id, 'identity' => array_merge(Yii::$app->admin->identities, [Yii::$app->admin->id]), 'type' => AdminResource::TypeProject])->exists();
    }
    
    /**
     * 获取项目联系人
     * @return object
     */
    public function getContacts()
    {
        return $this->hasOne(ProjectContacts::className(), ['project_id' => 'id']);
    }
    public function saveContacts($contacts)
    {

    }
}