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
            [['title', 'identity', 'public_key', 'status'], 'required'],
            [['status', 'deleted_at'], 'integer'],
            [['identity'], 'string', 'max' => 64],
            [['identity'], 'unique'],
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
            'title' => '标题',
            'identity' => '标识',
            'public_key' => '公钥串',
            'status' => '状态',
            'remark' => '备注',
            'deleted_at' => '删除时间',
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
                'title' => ['标题', ['maxlength' => 255, 'required']],
                'identity' => ['标识', ['maxlength' => 64, 'required']],
                'public_key' => ['公钥串', ['maxlength' => 1024, 'required']],
                'remark' => ['备注', ['maxlength' => 255, 'required']],
                'status' => ['状态', ['inkey' => static::$statusSelector, 'required']],
            ],
        ];
        return $rule;
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