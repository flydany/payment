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
        return $this->hasOne(PermissionGroup::className(), ['identity' => 'identity']);
    }
    /**
     * @name find permissions list
     * @return object list
     */
    public function getPermissions()
    {
        return $this->hasMany(Permission::className(), ['identity' => 'identity']);
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
            return Permission::updateAll(['identity' => $this->identity], ['identity' => $old_identity]);
        }
        return true;
    }
}