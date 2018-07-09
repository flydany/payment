<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Project".
 */
class Project extends ActiveRecord {

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['title', 'project_name', 'public_key', 'status'], 'required'],
            [['status', 'deleted_at'], 'integer'],
            [['project_name'], 'string', 'max' => 64],
            [['project_name'], 'unique'],
            [['title', 'remark'], 'string', 'max' => 255],
            [['public_key', 'effect_time'], 'string', 'max' => 1024],
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
            'project_name' => 'project name',
            'public_key' => 'rsa public',
            'effect_time' => 'effect times',
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
                'project_name' => ['project name', ['maxlength' => 64, 'required']],
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
     * 获取项目联系人
     * @return object
     */
    public function getContacts()
    {
        return $this->hasOne(ProjectContact::className(), ['project_id' => 'id']);
    }
}