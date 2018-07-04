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
            [['role_id'], 'integer'],
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
            'role_id' => 'power group',
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
                'username' => ['登录名', ['username', 'required']],
                'role_id' => ['权组', ['int']],
                'password_digest' => ['密码', ['password']],
                'realname' => ['姓名', ['chinese', 'minlength' => 2, 'maxlength' => 4, 'required']],
                'mobile' => ['电话', ['mobile', 'required']],
                'email' => ['邮箱', ['email', 'required']],
                'effect_date' => ['过期时间', ['date' => 'Y-m-d', 'required']],
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
     * @name find admin's power role
     * @param $this->id int 当前对象编号
     * @return object admin role query
     */
    public function getAdminRole()
    {
        return $this->hasOne(AdminRole::className(), ['id' => 'role_id']);
    }
    
    /**
     * @name find admin's power detail
     * @param $this->id int 当前对象编号
     * @return object admin permission query
     */
    public function getAdminPermissions()
    {
        return $this->hasMany(AdminPermission::className(), ['identity' => 'id']);
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
     * @param $role_id int  - role id
     * @param $permissions array  - permission details (navigator's id)
     * @return array [code, message]
     */
    public function setPermissions($role_id, $permissions)
    {
        // 如果用户所属组改变了，则此处更新所属组
        if($role_id != $this->role_id) {
            $this->role_id = $role_id;
            $this->updated_at = time();
            if( ! $this->save()) {
                return false;
            }
        }
        if(empty($permissions)) {
            return AdminPermission::deleteAll(['identity' => $this->id]);
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
        // 去除 当前选中权限 数组中的 所属组已有的 权限
        if($this->role_id && $this->adminRole) {
            // 搜索当前所属组的详细信息
            $newPermissions = array_diff($newPermissions, ArrayHelper::getColumn($this->adminRole->rolePermissions, 'navigator_path'));
        }
        // 查询当前用户拥有的权限，进行去重、删除以剔除权限
        $currPermissions = ArrayHelper::getColumn($this->adminPermissions, 'navigator_path');
        $newPermissions = array_diff($newPermissions, $currPermissions);
        $deletePermission = array_diff($currPermissions, $newPermissions);
        if(count($deletePermission) > 0) {
            if( ! AdminPermission::deleteAll(['identity' => $this->id, 'navigator_path' => $deletePermission])) {
                return false;
            }
        }
        if(count($newPermissions) > 0) {
            if( ! AdminPermission::batchInsert($this->id, $newPermissions)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * @name 剔除选中权限中的特定权限
     * @param $permissions array navigator's controller name
     * @param $permission string controller name which need remove
     * @return array
     */
    private function removePermission($permissions, $permission)
    {
        // 超级权限
        if($permission == 'super') {
            $permissions = [];
        }
        // 剔除子菜单（不包括自身）
        if(preg_match("/~$/", $permission)) {
            foreach($permissions as $navigator_path => $t) {
                if(strpos($navigator_path, $permission) !== false && ($navigator_path != $permission)) {
                    unset($permissions[$navigator_path]);
                }
            }
        }
        // 剔除自身 从选中权限中剔除 此权限
        if(isset($permissions[$permission])) {
            unset($permissions[$permission]);
        }
        return $permissions;
    }
    
    /**
     * @name 重写父类通过key设置属性方法，如果原始密码为空，剔除原始密码字段
     * @param array $param array params set to this object
     * @param null $tbKey array key which need transfered
     * @param bool $checkValid boolean wether check param valid
     * @return bool
     */
    public function setAttributesByKey($param, $tbKey = null, $checkValid = true)
    {
        // 如果密码不存在重置密码
        if(isset($tbKey['password']) && empty($param['password'])) {
            // $tbKey['password'] = null;
            unset($tbKey['password']);
        }

        // call parent function
        return parent::setAttributesByKey($param, $tbKey, $checkValid);
    }
    
    /**
     * @name 校验数据是否存在/允许编辑
     * @param $id int admin's id 需要校验的数据编号
     * @return bool | object
     */
    public static function finder($id, $condition = [])
    {
        // id 为必填项，判断数据存在状态
        if($id == 1) {
            // 参数异常，渲染错误页面
            // return false;
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
        $navigator_path = ['super', $controller, $controller .'/'. $action];
        if( ! $identity && ! Yii::$app->isLogin()) {
            return false;
        }
        // 组织需要校验的身份信息
        if( ! $identity) {
            $identity = [Yii::$app->admin['id']];
            Yii::$app->admin['adminRole'] && $identity[] = Yii::$app->admin['adminRole']['identity'];
        }
        // echo '<pre>'; print_r(Yii::$app->admin['adminRole']); die;
        // 返回是否存在权限
        return AdminPermission::find()->where(['controller' => $navigator_path, 'identity' => $identity])->exists();
    }
}