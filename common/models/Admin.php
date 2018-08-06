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
     * 字段名称
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
     * update & insert data check config for html
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
     * @function getAdminPermissionGroups 获取权组数组
     * @function getRoleIdentity 获取权组标识数组
     * @function getPermissionGroups 获取权组数组
     * @function getAdminPermissions 获取用户权限数组
     * @function getGroupPermissions 获取权组权限数组
     * @function getPermissions 获取用户所有权限数组
     * @function permissionSelector 获取用户所有权限数组
     */
    public function getAdminPermissionGroups()
    {
        return $this->hasMany(AdminGroup::className(), ['admin_id' => 'id']);
    }
    public function getIdentities()
    {
        return array_column($this->adminPermissionGroups, 'identity');
    }
    public function getIdentity()
    {
        return array_merge($this->identities, [$this->id]);
    }
    public function getPermissionGroups()
    {
        return AdminRole::find()->where(['identity' => $this->identities])->all();
    }
    public function getAdminPermissions()
    {
        return $this->hasMany(AdminPermission::className(), ['identity' => 'id']);
    }
    public function getGroupPermissions()
    {
        return AdminPermission::find()->where(['identity' => $this->identities])->all();
    }
    public function getPermissions()
    {
        return array_merge($this->adminPermissions, $this->groupPermissions);
    }
    public function getPermissionSelector()
    {
        return array_filter(array_column($this->permissions, 'controller'));
    }
    // 判断是否超级
    public function getIsSupper()
    {
        return in_array('super', $this->identities);
    }
    // 判断是否有权限
    public function hasPermission($permission)
    {
        return $this->isSupper || in_array($permission, $this->permissionSelector);
    }
    public function isGroupPermission($permission)
    {
        return $this->isSupper || in_array($permission, array_column($this->groupPermissions, 'controller'));
    }

    /**
     * 资源权限相关
     *
     */
    public function getResources($type = AdminResource::TypeProject)
    {
        return AdminResource::find()->where(['identity' => array_merge($this->identities, [$this->id])])->andFilterWhere(['type' => $type])->all();
    }
    public function getResourcePowers($type = '')
    {
        return array_unique(
            array_map(
                function($resource) {
                    return $resource->power;
                },
                $this->getResources($type)
            )
        );
    }

    /**
     * 查询是否有资源权限
     * @param string $power 权标
     * @param string $type 分类
     * @return boolean
     */
    public function hasResourcePower($power, $type = AdminResource::TypeProject)
    {
        if($this->isSupper) {
            return true;
        }
        return AdminResource::find()->where(['identity' => array_merge($this->identities, [$this->id]), 'power' => AdminResource::slicePower($power)])->andFilterWhere(['type' => $type])->exists();
    }

    /**
     * do some thing before save this admin object
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
    // create md5 password and set it to password_digest
    public function hashPassword()
    {
        if($this->password_digest == $this->getOldAttribute('password_digest')) {
            return true;
        }
        $this->password_digest = static::passwordDigest($this->password_digest);
        return true;
    }
    /**
     * create md5 password
     * @param $password string 密码
     * @return string md5串
     */
    public static function passwordDigest($password)
    {
        return password_hash($password.Yii::$app->params['passwordDigest'], PASSWORD_DEFAULT, ['cost' => 13]);
    }

    /**
     * check wether this admin password right
     * @return boolean
     */
    public function validatePassword($password)
    {
        return password_verify($password.Yii::$app->params['passwordDigest'], $this->password_digest);
    }
    
    /**
     * check wether this admin was out of time
     * @return boolean
     */
    public function valid()
    {
        return ($this->effect_date >= date('Y-m-d') || in_array($this->effect_date, [date('Y-m-d'), '0000-00-00'])) ? true : false;
    }

    /**
     * check wether this admin was out of time
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
     * 设置登陆态
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
     * change admin's permission
     * @param array $identities permission groups
     * @param array $permissions permission details (navigator's id)
     * @return boolean
     */
    public function setPermissions($identities, $permissions)
    {
        // 如果用户所属组改变了，则此处更新所属组
        if( ! AdminGroup::setPermissionGroups((string)$this->id, $identities, $this->identities)) {
            return false;
        }
        if( ! AdminPermission::setPermissions((string)$this->id, $permissions, $this->permissionSelector)) {
            return false;
        }
        return true;
    }
    
    /**
     * 重写父类通过key设置属性方法，如果原始密码为空，剔除原始密码字段
     * @param array $param array params set to this object
     * @param array $tableKey key which need transfered
     * @param bool $checkValid boolean wether check param valid
     * @return bool
     */
    public function loadAttributes($param, $tableKey = null, $checkValid = true)
    {
        // 如果密码不存在重置密码
        if(isset($tableKey['password']) && empty($param['password'])) {
            // $tbKey['password'] = null;
            unset($tableKey['password']);
        }

        // call parent function
        return parent::loadAttributes($param, $tableKey, $checkValid);
    }
    
    /**
     * 校验数据是否存在/允许编辑
     * @param $id int admin's id 需要校验的数据编号
     * @return bool|static
     */
    public static function finder($id, $condition = [])
    {
        // id 为必填项，判断数据存在状态
        if(in_array($id, [0, 1])) {
            // 参数异常，渲染错误页面
            return false;
        }
        return parent::finder($id, $condition);
    }
    /**
     * check admin's permission
     * @describe permission access by
     *   1、super
     *   2、controller
     *   3、controller/action
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
        // echo '<pre>'; print_r(AdminPermission::find()->where(['controller' => $controllers, 'identity' => $identity])->exists()); die;
        // 返回是否存在权限
        return AdminPermission::find()->where(['controller' => $controllers, 'identity' => $identity])->exists();
    }
}