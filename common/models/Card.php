<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "Card".
 */
class Card extends ActiveRecord {

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['project_id', 'bank_id', 'card_no', 'realname', 'id_card', 'mobile'], 'required'],
            [['project_id', 'bank_id', 'card_no', 'mobile', 'deleted_at'], 'integer'],
            [['realname'], 'string', 'max' => 64],
            [['project_id', 'mobile', 'card_no'], 'unique', 'targetAttribute' => ['project_id', 'mobile', 'card_no']],
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
            'bank_id' => 'bank number',
            'card_no' => 'card number',
            'realname' => 'real name',
            'id_card' => 'id card',
            'mobile' => 'mobile',
            'deleted_at' => 'deleted at',
        ];
    }
}