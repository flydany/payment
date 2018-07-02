<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "ProjectContact".
 */
class ProjectContact extends ActiveRecord {

    // 联系人常量
    const IdentityCommerce = 0;
    const IdentityProduct = 1;
    const IdentityDeveloper = 2;
    const IdentityFinance = 3;
    const IdentityCustomerService = 4;
    public static $identitySelector = [
        self::IdentityCommerce => '商务',
        self::IdentityProduct => '产品',
        self::IdentityDeveloper => '开发',
        self::IdentityFinance => '财务',
        self::IdentityCustomerService => '客服',
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
            'project_id' => '项目',
            'identity' => '身份标识',
            'name' => '联系人',
            'mobile' => '手机号',
            'email' => '邮箱',
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
                'project_id' => ['项目', ['maxlength' => 255, 'required']],
                'identity' => ['身份标识', ['inkey' => static::$identitySelector, 'required']],
                'name' => ['联系人', ['maxlength' => 64, 'required']],
                'mobile' => ['手机号', ['maxlength' => 32, 'required']],
                'email' => ['邮箱', ['maxlength' => 255, 'required']],
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
        return $this->hasOne(Project::className(), ['project_id' => 'id']);
    }
}