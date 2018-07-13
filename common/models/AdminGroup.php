<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

class AdminGroup extends ActiveRecord {
    
    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['admin_id', 'identity'], 'required'],
            [['admin_id'], 'integer'],
        ];
    }
    /**
     * @name 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'admin_id' => 'administrator number',
            'identity' => 'permission group identity',
        ];
    }
    
    /**
     * @name find permissions group
     * @return object list
     */
    public function getPermissionGroup()
    {
        return $this->hasOne(AdminRole::className(), ['identity' => 'identity']);
    }
    /**
     * @name find permissions list
     * @return object list
     */
    public function getPermissions()
    {
        return $this->hasMany(AdminPermission::className(), ['identity' => 'identity']);
    }
    public function permissionSelector()
    {
        return ArrayHelper::map($this->rolePermissions, 'id', 'controller');
    }
    
    // @name 修改admin_role.identity时
    // @describe 需要更新admin_permission的identity字段
    // @param $old_identity string 老的权限标识
    // @rturn boolean
    public function onChange($old_identity)
    {
        if($this->identity && ($this->identity != $old_identity)) {
            return AdminPermission::updateAll(['identity' => $this->identity], ['identity' => $old_identity]);
        }
        return true;
    }
    
    /**
     * @param integer $adminId 管理员编号
     * @param array $identities 需要设置的权限组列表
     * @param array $currentIdentitis 当前存在的权限组列表
     * @return boolean|integet
     */
    public static function setPermissionGroups($adminId, $identities, $currentIdentitis)
    {
        if(empty($adminId)) {
            return false;
        }
        if(empty($identities)) {
            static::deleteAll(['admin_id' => $adminId]);
            return true;
        }
        // 查询当前用户拥有的权限，进行去重、删除以剔除权限
        $newIdentities = array_diff($identities, $currentIdentitis);
        $deleteIdentities = array_diff($currentIdentitis, $identities);
        if(count($deleteIdentities) > 0) {
            static::deleteAll(['admin_id' => $adminId, 'identity' => $deleteIdentities]);
        }
        if(count($newIdentities) > 0) {
            static::batchInsert($adminId, $newIdentities);
        }
        return true;
    }
    
    /**
     * 批量插入权限组
     * @param  integer $adminId 管理员编号
     * @param array $identities 标识数组
     * @return boolean
     */
    public static function batchInsert($adminId, $identities)
    {
        if(empty($adminId) || empty($identities)) {
            return false;
        }
        $time = time();
        foreach($identities as $identity) {
            $param[] = [$adminId, $identity, $time, $time];
        }
        return Yii::$app->db->createCommand()->batchInsert(static::tableName(), [
            'admin_id', 'identity', 'created_at', 'updated_at'
        ], $param)->execute();
    }
}