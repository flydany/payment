<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

class AdminResource extends ActiveRecord {
    
    // 数据源常量定义
    const TypeProject = '1';
    const TypePlatform = '2';
    public static $typeSelector = [
        self::TypeProject => 'project',
        self::TypePlatform => 'merchant',
    ];
    
    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['type', 'identity', 'power'], 'required'],
            [['type', 'power'], 'integer'],
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
            'type' => 'resource type',
            'identity' => 'identity',
            'power' => 'resource number',
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
                'power' => ['resource number', ['int', 'required']],
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
     * 判断当前用户是否有此项目的权限
     * @return boolean
     */
    public static function hasPermission($power, $type)
    {
        if(Yii::$app->admin->isSupper) {
            return true;
        }
        return AdminResource::find()->where(['power' => static::slicePower($power), 'identity' => Yii::$app->admin->identity, 'type' => $type])->exists();
    }
    /**
     * 获取项目已存在的负责人
     * @return array
     */
    public static function identities($power, $type)
    {
        return array_unique(AdminResource::find()->select('identity')->where(['power' => static::slicePower($power), 'type' => $type])->column());
    }

    /**
     * 获取负责人的资源
     * @return array
     */
    public static function powers($type)
    {
        return array_unique(AdminResource::find()->select('power')->where(['identity' => Yii::$app->admin->identity, 'type' => $type])->column());
    }

    /**
     * 分解权标查询条件
     * @param string $power 权限标识
     * @return array
     */
    public static function powerCondition($powers)
    {
        $conditions = [];
        $powers = array_unique($powers);
        foreach($powers as $power) {
            $powers = explode('.', $power);
            if(isset($powers[1])) {
                $conditions[$powers[0]]['platform_id'] = $powers[0];
                $conditions[$powers[0]]['merchant_number'][] = $powers[1];
            }
            else {
                $conditions[$powers[0]] = ['platform_id' => $powers[0]];
            }
        }
        return $conditions;
    }

    /**
     * 分解权限
     * @param string $power 权标
     * @return array
     */
    public static function slicePower($power)
    {
        $powers = explode('.', $power);
        $response = ['0'];
        $per = [];
        foreach($powers as $p) {
            $per[] = $p;
            $response[] = implode('.', $per);
        }
        return $response;
    }
}