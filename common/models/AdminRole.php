<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

class AdminRole extends ActiveRecord {
    
    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['title', 'identity'], 'required'],
            [['sort', 'deleted_at'], 'integer'],
            [['title', 'identity'], 'string', 'max' => 128],
            [['identity'], 'match', 'pattern' => "/^[\w\-\_\.]+$/"],
            [['remark'], 'string', 'max' => 255]
        ];
    }
    /**
     * @name 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'title' => 'administrator group title',
            'identity' => 'identity',
            'sort' => 'sort',
            'remark' => 'remark',
            'deleted_at' => 'deleted at',
        ];
    }
    
    /**
     * @name update & insert data check config for html
     * @param $type string 页面操作类型
     * @param $encodeJson boolean 是否转成json串
     * @return string | array
     */
    public static function flyer($type = 'update')
    {
        $rule = [
            'param' => [
                'title' => ['administrator group title', ['maxlength' => 128, 'required']],
                'identity' => ['identity', ['preg' => '/^[\w\-_\.]{1,}$/', 'required']],
                'remark' => ['remark', ['maxlength' => 255]],
            ]
        ];
        return $rule;
    }

    /**
     * @name find permissions list
     * @return object list
     */
    public function getRolePermissions()
    {
        return $this->hasMany(AdminPermission::className(), ['identity' => 'identity']);
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
     * @name change admin's permission
     * @param $permissions array  - permission details (navigator's id)
     * @return array [code, message]
     */
    public function setPermissions($permissions)
    {
        if(empty($permissions)) {
            return AdminPermission::deleteAll(['identity' => $this->identity]);
        }
        // 搜索当前选中的权限
        $newPermissions = [];
        $navigators = Navigator::find()->where(['id' => $permissions])->with('parent')->orderBy('id ASC')->all();
        if(empty($navigators)) {
            return false;
        }
        foreach($navigators as $navigator) {
            if($navigator->parent_id == 0) {
                $newPermissions[] = $navigator->controller .'~';
            }
            else if( ! in_array($navigator->parent->controller .'~', $newPermissions)) {
                $newPermissions[] = $navigator->parent->controller .'~'. $navigator->controller;
            }
        }
        // 查询当前权组拥有的权限，进行去重、删除以剔除权限
        $currPermissions = ArrayHelper::getColumn($this->rolePermissions, 'controller');
        $deletePermissions = array_diff($currPermissions, $newPermissions);
        $newPermissions = array_diff($newPermissions, $currPermissions);
        if(count($deletePermissions) > 0) {
            if( ! AdminPermission::deleteAll(['identity' => $this->identity, 'controller' => $deletePermissions])) {
                return false;
            }
        }
        if(count($newPermissions) > 0) {
            if( ! AdminPermission::batchInsert($newPermissions)) {
                return false;
            }
        }
        return true;
    }
    
    // @name for select use
    // @return array relation of[id => title]
    public static function identitySelector()
    {
        return ArrayHelper::map(static::find()->select('id, title')->orderBy('id ASC')->asArray()->all(), 'id', 'title');
    }
}