<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ProjectContacts".
 */
class ProjectContacts extends ActiveRecord {

    // 联系人常量
    const IdentityCommerce = '0';
    const IdentityProduct = '1';
    const IdentityDeveloper = '2';
    const IdentityFinance = '3';
    const IdentityCustomerService = '4';
    public static $identitySelector = [
        self::IdentityCommerce => 'commerce',
        self::IdentityProduct => 'product',
        self::IdentityDeveloper => 'developer',
        self::IdentityFinance => 'finance',
        self::IdentityCustomerService => 'customer service',
    ];

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['project_id', 'identity', 'name', 'mobile'], 'required'],
            [['project_id', 'identity'], 'integer'],
            [['name'], 'string', 'max' => 64],
            [['mobile'], 'string', 'max' => 32],
            [['email'], 'string', 'max' => 255],
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
            'identity' => 'identity',
            'name' => 'name',
            'mobile' => 'mobile',
            'email' => 'email',
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
                'identity' => ['identity', ['in' => array_keys(static::$identitySelector), 'required']],
                'name' => ['name', ['maxlength' => 64, 'required']],
                'mobile' => ['mobile', ['maxlength' => 32, 'required']],
                'email' => ['email', ['maxlength' => 255, 'required']],
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
}