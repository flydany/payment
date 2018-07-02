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
            [['bank_id', 'card_no', 'realname', 'id_card', 'mobile'], 'required'],
            [['bank_id', 'card_no', 'mobile', 'deleted_at'], 'integer'],
            [['realname'], 'string', 'max' => 64],
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
            'bank_id' => '银行',
            'card_no' => '卡号',
            'realname' => '姓名',
            'id_card' => '身份证',
            'mobile' => '手机号',
            'deleted_at' => '删除时间',
        ];
    }
}