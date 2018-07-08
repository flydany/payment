<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class Permission extends ActiveRecord {
    
    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['identity', 'controller'], 'required'],
        ];
    }
    /**
     * @name 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'identity' => 'identity',
            'controller' => 'controller',
        ];
    }
    
    /**
     * return this power's controller for
     */
    public function getControllerTitle()
    {
        if(strpos($this->controller, '/') >= 0) {
            return preg_replace("/\/[\w-_]+$/", '', $this->controller);
        }
        else {
            return '';
        }
    }
    
    /**
     * return this power's action's for
     */
    public function getActionTitle()
    {
        $path = explode('/', $this->controller);
        if(count($path) >= 2) {
            return array_pop($path);
        }
        else {
            return '';
        }
    }
    
    /**
     * check power For
     * 'user': 属于用户
     * 'role': 属于权组
     * return 'user' || 'role' object
     */
    public function getOwner()
    {
        if(is_numeric($this->identity)) {
            return $this->hasOne(Admin::className(), ['id' => 'identity']);
        }
        else {
            return $this->hasOne(PermissionGroup::className(), ['id' => 'identity']);
        }
    }
    
    /**
     * 设置权限
     * @param string $identity 标识
     * @param array $permissions 权限列表
     * @param array $currentPermissions 当前已有的权限列表
     * @return boolean|integer
     */
    public static function setPermissions($identity, $permissions, $currentPermissions)
    {
        if(empty($identity)) {
            return false;
        }
        if(empty($permissions)) {
            static::deleteAll(['identity' => $identity]);
            return true;
        }
        // 查询当前用户拥有的权限，进行去重、删除以剔除权限
        $newPermissions = array_diff($permissions, $currentPermissions);
        $deletePermission = array_diff($currentPermissions, $permissions);
        if(count($deletePermission) > 0) {
            static::deleteAll(['identity' => $identity, 'controller' => $deletePermission]);
        }
        if(count($newPermissions) > 0) {
            static::batchInsert($identity, $newPermissions);
        }
        return true;
    }
    
    /**
     * 批量插入导航权限
     * @param  string $identity 标识
     * @param array $permissions 导航权限列表
     * @return boolean
     */
    public static function batchInsert($identity, $permissions)
    {
        if(empty($identity) || empty($permissions)) {
            return false;
        }
        $time = time();
        foreach($permissions as $controller) {
            $param[] = [$identity, $controller, $time, $time];
        }
        return Yii::$app->db->createCommand()->batchInsert(static::tableName(), [
            'identity', 'controller', 'created_at', 'updated_at'
        ], $param)->execute();
    }
    
    /**
     * 根据标识获取权限
     * @param string|array $identity 标识
     * @return array
     */
    public static function identityPermissions($identity)
    {
        return array_map(function($data) {
            return array_values($data);
        }, ArrayHelper::map(
            Permission::find()->select('id, identity, controller')->where(['identity' => $identity])->asArray()->all(),
            'id', 'controller', 'identity'
        ));
    }
}