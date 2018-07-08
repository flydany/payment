<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "BindCard".
 */
class BindCard extends ActiveRecord {
    
    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['project_id', 'platform_id', 'merchant_id', 'paytype', 'bank_id', 'card_no', 'user_id', 'realname', 'id_card', 'mobile'], 'required'],
            [['project_id', 'platform_id', 'paytype', 'bank_id', 'card_no', 'user_id', 'deleted_at'], 'integer'],
            [['mobile'], 'string', 'max' => 32],
            [['realname', 'bind_number', 'protocol_number'], 'string', 'max' => 64],
            [['card_no', 'mobile'], 'unique', 'targetAttribute' => ['card_no', 'mobile']],
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
            'merchant_id' => 'merchant number',
            'paytype' => 'pay type',
            'bank_id' => 'bank id',
            'card_no' => 'card number',
            'realname' => 'real name',
            'id_card' => 'id card',
            'mobile' => 'mobile',
            'user_id' => 'user number',
            'bind_number' => 'bind order number',
            'protocol_number' => 'protocol number',
            'deleted_at' => 'deleted at',
        ];
    }
    
    /**
     * 获取项目
     * @return Project
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['project_id' => 'id']);
    }
    
    /**
     * 获取商户号配置
     * @return Merchant
     */
    public function getMerchant()
    {
        return $this->hasOne(Merchant::className(), ['merchant_id' => 'id']);
    }
}