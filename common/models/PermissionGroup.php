<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

class PermissionGroup extends ActiveRecord {
    
    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['title', 'identity'], 'required'],
            [['deleted_at'], 'integer'],
            [['title', 'identity'], 'string', 'max' => 128],
            [['remark'], 'string', 'max' => 255]
        ];
    }
    /**
     * 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'title' => 'administrator group title',
            'identity' => 'identity',
            'remark' => 'remark',
            'deleted_at' => 'deleted at',
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
                'title' => ['administrator group title', ['maxlength' => 128, 'required']],
                'identity' => ['identity', ['status', 'required']],
                'remark' => ['remark', ['maxlength' => 255]],
            ]
        ];
        return $rule;
    }

    /**
     * find permissions list
     * @return object list
     */
    public function getPermissions()
    {
        return $this->hasMany(Permission::className(), ['identity' => 'identity']);
    }
    public function getPermissionSelector()
    {
        return array_column($this->permissions, 'controller');
    }
    
    // 修改admin_role.identity时
    // @describe 需要更新admin_permission的identity字段
    // @param $old_identity string 老的权限标识
    // @rturn boolean
    public function onChange($old_identity)
    {
        if($this->identity && ($this->identity != $old_identity)) {
            Permission::updateAll(['identity' => $this->identity], ['identity' => $old_identity]);
            AdminPermissionGroup::updateAll(['identity' => $this->identity], ['identity' => $old_identity]);
        }
        return true;
    }
    
    /**
     * change admin's permission
     * @param array $permissions permission details (navigator's id)
     * @return boolean
     */
    public function setPermissions($permissions)
    {
        return Permission::setPermissions($this->identity, $permissions, $this->permissionSelector);
    }
    
    // for select use
    // @return array relation of[identity => title]
    public static function identitySelector()
    {
        return ArrayHelper::map(static::find()->select('identity, title')->orderBy('id ASC')->asArray()->all(), 'identity', 'title');
    }
    
    /**
     * 校验数据是否存在/允许编辑
     * @param $id int admin's id 需要校验的数据编号
     * @return bool|static
     */
    public static function finder($id, $condition = [])
    {
        // id 为必填项，判断数据存在状态
        if($id == 1) {
            // 参数异常，渲染错误页面
            return false;
        }
        return parent::finder($id, $condition);
    }
}