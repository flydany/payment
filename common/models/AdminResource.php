<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

class AdminResource extends ActiveRecord {
    
    // 数据源常量定义
    const TypeProject = '1';
    const TypeMerchant = '2';
    public static $typeSelector = [
        self::TypeProject => 'project',
        self::TypeMerchant => 'platform',
    ];
    
    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['type', 'identity', 'power'], 'required'],
            [['type'], 'integer'],
            [['identity', 'power'], 'string', 'max' => 64],
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
            'type' => 'resource type',
            'identity' => 'identity',
            'power' => 'power describe',
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
                'type' => ['resource type', ['int', 'required']],
                'identity' => ['identity list', ['required']],
                'power' => ['power describe', ['maxlength' => 64, 'required']],
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
    public static function setResources($itemId, $identities, $type = self::TypeProject)
    {
        // 如果不存在数据，删除所有已存在的数据源
        if(empty($itemId)) {
            return static::deleteAll(['power' => $itemId, 'type' => $type]);
        }
        // 获取已存在的权限
        $powers = static::find()->select('id, identity')->where(['power' => $itemId, 'type' => $type])->asArray()->all();
        $has = ArrayHelper::map($powers, 'id', 'identity');
        $insert = array_diff($identities, $has);
        $delete = array_diff($has, $identities);
        if($delete) {
            if( ! static::deleteAll(['power' => $itemId, 'type' => $type, 'identity' => $delete])) {
                return false;
            }
        }
        if($insert) {
            $insert = array_filter($insert);
            $time = time();
            foreach($insert as $identity) {
                $params[] = [$type, $identity, $itemId, $time, $time];
            }
            $result = Yii::$app->db->createCommand()->batchInsert(static::tableName(), ['type', 'identity', 'power', 'updated_at', 'created_at'], $params)->execute();
            if(empty($result)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * 保存角色的数据源权限
     * @param string $identity 标识
     * @param array $items 数据源编号数组
     * @param int $type 数据源类型
     * @return boolean|integer
     * @throws \yii\db\Exception
     */
    public static function setPermissions($identity, $items, $type = self::TypeProject)
    {
        // 如果不存在数据，删除所有已存在的数据源
        if(empty($items)) {
           return static::deleteAll(['identity' => $identity, 'type' => $type]);
        }
        // 获取已存在的权限
        $powers = static::find()->select('id, power')->where(['identity' => $identity, 'type' => $type])->asArray()->all();
        $has = ArrayHelper::map($powers, 'id', 'power');
        $insert = array_diff($items, $has);
        $delete = array_diff($has, $items);
        if($delete) {
            if( ! static::deleteAll(['identity' => $identity, 'type' => $type, 'power' => $delete])) {
                return false;
            }
        }
        if($insert) {
            $insert = array_filter($insert);
            $time = time();
            foreach($insert as $itemId) {
                $params[] = [$type, $identity, $itemId, $time, $time];
            }
            $result = Yii::$app->db->createCommand()->batchInsert(static::tableName(), ['type', 'identity', 'power', 'updated_at', 'created_at'], $params)->execute();
            if(empty($result)) {
                return false;
            }
        }
        return true;
    }

    /**
     * 添加权限
     * @param string $identity 身份标识
     * @param integer $power 数据源编号
     * @param string $type 数据源类型
     * @return boolean
     */
    public static function creator($identity, $power, $type = self::TypeProject)
    {
        $resource = new static();
        $resource->loads(['type' => $type, 'identity' => (string)$identity, 'power' => $power]);
        if($resource->validate() && $resource->save()) {
            return true;
        }
        return false;
    }

    /**
     * 分割权限
     * @param string $power 权标
     * @return array
     */
    public static function slicePower($power)
    {

        $powers = explode('.', $power);
        $search = [0];
        $c = '';
        foreach($powers as $power) {
            $c[] = $power;
            $search[] = implode('.', $c);
        }
        return $search;
    }
}