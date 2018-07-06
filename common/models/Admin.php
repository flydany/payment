<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class Admin extends ActiveRecord {
    
    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['username', 'realname', 'mobile', 'email', 'effect_date'], 'required'],
            [['username', 'password_digest'], 'string', 'length' => [4, 128]],
            [['mobile'], 'match', 'pattern' => "/^1\d{10}$/"],
            [['username', 'mobile'], 'unique'],
        ];
    }
    /**
     * @name 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => 'username',
            'password_digest' => 'password',
            'realname' => 'realname',
            'mobile' => 'mobile',
            'email' => 'email',
            'effect_date' => 'effect date',
        ];
    }
    /**
     * @name update & insert data check config for html
     * @param $type string 页面操作类型
     * @param $encodeJson boolean 是否转成JSON字符串
     * @return string | array
     */
    public static function flyer($type = 'update')
    {
        // jsut search
        $rule = [
            'param' => [
                'username' => ['username', ['username', 'required']],
                'password_digest' => ['password', ['password']],
                'realname' => ['realname', ['maxlength' => 64, 'required']],
                'mobile' => ['mobile', ['mobile', 'required']],
                'email' => ['email', ['email', 'required']],
                'effect_date' => ['effect date', ['date' => 'Y-m-d', 'required']],
            ],
        ];
        // type eq update
        if($type == 'update') {
            $rule['param']['password_digest'][1]['required'] = false;
            unset($rule['param']['username']);
        }
        return $rule;
    }
    
    /**
     * admin's power detail
     * @function getAdminGroups 获取权组数组
     * @function getRoleIdentity 获取权组标识数组
     * @function getPermissionGroups 获取权组数组
     * @function getAdminPermissions 获取用户权限数组
     * @function getGroupPermissions 获取权组权限数组
     * @function getPermissions 获取用户所有权限数组
     * @function permissionSelector 获取用户所有权限数组
     */
    public function getAdminGroups()
    {
        return $this->hasMany(AdminGroup::className(), ['admin_id' => 'id']);
    }
    public function getIdentities()
    {
        return array_column($this->adminGroups, 'identity');
    }
    public function getPermissionGroups()
    {
        return PermissionGroup::find()->where(['identity' => $this->identities])->all();
    }
    public function getAdminPermissions()
    {
        return $this->hasMany(Permission::className(), ['identity' => 'id']);
    }
    public function getGroupPermissions()
    {
        return Permission::find()->where(['identity' => $this->identities])->all();
    }
    public function getPermissions()
    {
        return array_merge($this->adminPermissions, $this->groupPermissions);
    }
    public function permissionSelector()
    {
        return array_filter(array_column($this->permissions, 'controller'));
    }
    /**
     * @name do some thing before save this admin object
     * @param $insert boolean update params
     * @return boolean
     */
    public function beforeSave($insert)
    {
        // 插入前
        if(parent::beforeSave($insert)) {
            // 初始化密码
            $this->hashPassword();
        }
        return true;
    }
    // @name create md5 password and set it to password_digest
    public function hashPassword()
    {
        if($this->password_digest == $this->getOldAttribute('password_digest')) {
            return true;
        }
        $this->password_digest = static::passwordDigest($this->password_digest);
        return true;
    }
    /**
     * @name create md5 password
     * @param $password string 密码
     * @return string md5串
     */
    public static function passwordDigest($password)
    {
        return password_hash($password.Yii::$app->params['passwordDigest'], PASSWORD_DEFAULT, ['cost' => 13]);
    }

    /**
     * @name check wether this admin password right
     * @return boolean
     */
    public function validatePassword($password)
    {
        return password_verify($password.Yii::$app->params['passwordDigest'], $this->password_digest);
    }
    
    /**
     * @name check wether this admin was out of time
     * @return boolean
     */
    public function valid()
    {
        return ($this->effect_date >= date('Y-m-d') || in_array($this->effect_date, [date('Y-m-d'), '0000-00-00'])) ? true : false;
    }

    /**
     * @name check wether this admin was out of time
     * @return boolean
     */
    public static function isValid($admin)
    {
        if(empty($admin)) {
            return false;
        }
        return ($admin['effect_date'] >= date('Y-m-d') || in_array($admin['effect_date'], [date('Y-m-d'), '0000-00-00'])) ? true : false;
    }

    /**
     * @name 设置登陆态
     * @return boolean
     */
    public function login()
    {
        return Yii::$app->session->set('admin', $this);
        $cache = $this->attributes;
        $cache['adminRole'] = $this->adminRole->attributes;
        $cache['expire_time'] = time() + 3600 * 24;
        return Yii::$app->session->set('admin', $cache);
    }
    
    /**
     * @name change admin's permission
     * @param $permissions array  - permission details (navigator's id)
     * @return array [code, message]
     */
    public function setPermissions($roles, $permissions)
    {
        // 如果用户所属组改变了，则此处更新所属组
        if(implode(', ', $roles) != $this->roles) {
            $this->adminGroups = implode(', ', $roles);
            $this->updated_at = time();
            if( ! $this->save()) {
                return false;
            }
        }
        if(empty($permissions)) {
            return Permission::deleteAll(['identity' => $this->id]);
        }
        // 查询当前用户拥有的权限，进行去重、删除以剔除权限
        $newPermissions = array_diff($permissions, $this->permissionSelector);
        $deletePermission = array_diff($this->permissionSelector, $newPermissions);
        if(count($deletePermission) > 0) {
            if( ! Permission::deleteAll(['identity' => $this->id, 'controller' => $deletePermission])) {
                return false;
            }
        }
        if(count($newPermissions) > 0) {
            if( ! Permission::batchInsert($this->id, $newPermissions)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * @name 重写父类通过key设置属性方法，如果原始密码为空，剔除原始密码字段
     * @param array $param array params set to this object
     * @param null $tbKey array key which need transfered
     * @param bool $checkValid boolean wether check param valid
     * @return bool
     */
    public function loadAttributes($param, $tbKey = null, $checkValid = true)
    {
        // 如果密码不存在重置密码
        if(isset($tbKey['password']) && empty($param['password'])) {
            // $tbKey['password'] = null;
            unset($tbKey['password']);
        }

        // call parent function
        return parent::loadAttributes($param, $tbKey, $checkValid);
    }
    
    /**
     * @name 校验数据是否存在/允许编辑
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
    /**
     * @name check admin's permission
     * @describe permission access by
     *   1、super
     *   2、controller~
     *   3、controller~action
     * @param $controller string 控制请名称
     * @param $action string action方法名称
     * @param $identity string 身份信息
     * @return boolean
     */
    public static function checkPermission($controller, $action, $identity = null)
    {
        // 组织权限验证规则
        $controllers = ['super', $controller, $controller .'/'. $action];
        if(empty($identity) && ! Yii::$app->isLogin()) {
            return false;
        }
        // 组织需要校验的身份信息
        if(empty($identity)) {
            $identity = array_merge([Yii::$app->admin['id']], Yii::$app->admin->identities);
        }
        // echo '<pre>'; print_r(Yii::$app->admin->identities); die;
        // 返回是否存在权限
        return Permission::find()->where(['controller' => $controllers, 'identity' => $identity])->exists();
    }
}