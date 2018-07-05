<?php

namespace common\models;

use yii\db\Query;

class AdminPermission extends ActiveRecord
{
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
            return explode('/', $this->controller)[0];
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
        if(count($path) == 2) {
            return $path[1];
        }
        else {
            return '';
        }
    }
    
    /**
     * check power For
     * 'user': 属于用户
     * 'role': 属于权组
     *
     * return 'user' || 'role' object
     */
    public function getOwner()
    {
        if(is_numeric($this->identity)) {
            return $this->hasOne(Admin::className(), ['id' => 'identity']);
        }
        else {
            return $this->hasOne(AdminRole::className(), ['id' => 'identity']);
        }
    }
    
    /**
     * 批量插入导航权限
     * @param array $permissions 导航权限列表
     * @return boolean
     */
    public static function batchInsert($identity, $permissions)
    {
        if(empty($permissions)) {
            return false;
        }
        $time = time();
        foreach($permissions as $controller) {
            $param[] = [$identity, $controller, $time, $time];
        }
        return Yii::$app->db->createCommand()->batchInsert('admin_permission', ['identity', 'controller', 'created_at', 'updated_at'], $param)->execute();
    }
    
    /**
     * 获取权限组、用户的所有权限 JOIN TABLE navigator FIND navigator id
     * 顶级栏目：【子栏目：true】 仅有某个子栏目的权限
     * 顶级栏目：super 有当前顶级栏目下的所有权限
     * super 有当前系统的所有权限
     * return {"1":{"11":true},"4":{"super":true}}
     */
    public static function permissionSelector($identity)
    {
        $permissions = (new Query)->select(['admin_permission.id', 'admin_permission.controller', 'admin_permission.identity', 'controller.id controller_id', 'method.id method_id', 'method.parent_id _controller_id'])
            ->from('admin_permission')
            ->leftJoin('navigator controller', 'controller.parent_id = 0 and controller.controller = substring_index(admin_permission.controller, \'~\', 1)')
            ->leftJoin('navigator method', 'method.controller = replace(admin_permission.controller, \'~\', \'/\') or (method.parent_id = controller.id and method.controller = substring_index(admin_permission.controller, \'~\', -1))')
            ->where(['admin_permission.identity' => $identity])->all();
        
        $data = [];
        if(count($permissions)) {
            foreach($permissions as $permission) {
                $controller_id = $permission['controller_id'];
                if( ! $controller_id) {
                    $controller_id = $permission['_controller_id'];
                }
                $method_id = $permission['method_id'];
                // 超级管理员
                if($permission['controller'] == 'super') {
                    $data = ['super' => true];
                    break;
                }
                else {
                    // 子系统 超级管理员
                    if( ! $method_id) {
                        $data[$controller_id] = ['super' => true];
                    }
                    else {
                        if(isset($data[$controller_id]['super'])) {
                            continue;
                        }
                        // 普通管理员
                        else {
                            $data[$controller_id][$method_id] = true;
                        }
                    }
                }
            }
        }
        return $data;
    }
}