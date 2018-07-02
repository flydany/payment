<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

class AdminItem extends ActiveRecord {
    
    // 数据源常量定义
    const TypeProject = 1;
    public $typeSelector = [
        self::TypeProject => ['title' => '项目', 'status' => 'blue'],
    ];
    
    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['type', 'identity', 'item_id'], 'required'],
            [['type', 'item_id'], 'integer'],
            [['identity'], 'string', 'max' => 64],
            [['identity'], 'match', 'pattern' => "/^[\w\-\_\.]+$/"],
        ];
    }
    /**
     * 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'type' => '数据源',
            'identity' => '标识',
            'item_id' => '数据源编号',
        ];
    }
    
    /**
     * update & insert data check config for html
     * @param $type string 页面操作类型
     * @param $encodeJson boolean 是否转成json串
     * @return string | array
     */
    public static function flyer($type = 'update')
    {
        $rule = [
            'param' => [
                'type' => ['数据源', ['int', 'required']],
                'identity' => ['标识', ['preg' => '/^[\w\-_\.]{1,}$/', 'required']],
                'item_id' => ['数据源编号', ['int', 'required']],
            ]
        ];
        return $rule;
    }
    
    /**
     * 保存数据源权限
     * @param string $identity 标识
     * @param array $items 数据源编号数组
     * @param int $type 数据源类型
     * @return boolean|integer
     * @throws \yii\db\Exception
     */
    public static function setItems($identity, $items, $type = self::TypeProject)
    {
        // 如果不存在数据，删除所有已存在的数据源
        if(empty($items)) {
           return static::deleteAll(['identity' => $identity, 'type' => $type]);
        }
        // 获取已存在的权限
        $powers = static::find()->where(['identity' => $identity, 'type' => $type])->all();
        $has = ArrayHelper::map($powers, 'id', 'item_id');
        $insert = array_diff($items, $has);
        $delete = array_diff($has, $insert);
        if($delete) {
            if( ! static::deleteAll(['identity' => $identity, 'type' => $type, 'item_id' => $delete])) {
                return false;
            }
        }
        if($insert) {
            if( ! static::batchInsert($identity, $insert, $type)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * @param string $identity 标识
     * @param array $items 数据源编号数组
     * @param int $type 数据源类型
     * @return boolean|integer
     * @throws \yii\db\Exception
     */
    public static function batchInsert($identity, $items, $type = self::TypeProject)
    {
        if(empty($items)) {
            return false;
        }
        $items = array_filter($items);
        $time = time();
        foreach($items as $item_id) {
            $params[] = [$type, $identity, $item_id, $time, $time];
        }
        return Yii::$app->db->createCommand()->batchInsert(static::tableName(), ['type', 'identity', 'item_id', 'updated_at', 'created_at'], $params)->execute();
    }
}