<?php

namespace common\models;

use Yii;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "Navigator".
 */
class Navigator extends \common\models\ActiveRecord {
    
    // @name 设置导航功能类型
    const TypeFunction = 0;
    const TypeNavigator = 1;
    const TypeSublime = 2;
    
    // @name 地址跳转方式
    const TargetBlank = '_blank';
    const TargetStatic = '_static';
    const TargetSelf = '_self';
    public static $targetSelector = [
        self::TargetBlank => ['title' => '新窗口', 'status' => 'red'],
        self::TargetStatic => ['title' => '默认', 'status' => 'blue'],
        self::TargetSelf => ['title' => '当前窗口', 'status' => 'green'],
    ];
    
    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['title', 'parent_id', 'top_id', 'controller'], 'required'],
            [['parent_id', 'top_id', 'type', 'sort', 'flag', 'type', 'deleted_at'], 'integer'],
            [['path', 'target', 'icon_class'], 'string', 'max' => 64],
            [['type'], 'default', 'value' => static::TypeFunction],
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
            'title' => '标题',
            'parent_id' => '父栏目',
            'top_id' => '顶级父栏',
            'path' => '路径',
            'type' => '栏目类型',
            'controller' => '控制器',
            'target' => '打开方式',
            'sort' => '排序',
            'flag' => '是否显示',
            'icon_class' => '图标',
            'deleted_at' => '删除时间',
            'remark' => '备注',
        ];
    }
    /**
     * @name update & insert data check config for html
     * @param $type string 页面操作类型
     * @param $encodeJson boolean 是否转成json串
     * @return string / array
     */
    public static function flyer($type = 'update')
    {
        $rule = [
            'param' => [
                'title' => ['菜单标题', ['maxlength' => 128, 'required']],
                'controller' => ['控制器', ['preg' => '/^[\w\-\_\/]+$/', 'required']],
                'icon_class' => ['小图标', ['preg' => '/^[\w\-\_]+$/']],
                'type' => ['菜单类型', ['int', 'default' => '0']],
                'target' => ['是否打开新窗口', ['preg' => '/^\_(static|blank)$/', 'default' => '_static']],
                'sort' => ['排序', ['int', 'required']],
                'remark' => ['备注', ['maxlength' => 255]],
            ]
        ];
        if($type == 'insert') {
            $rule['param']['parent_id'] = ['所属菜单', ['int', 'required']];
            $rule['param']['top_id'] = ['顶级父栏目', ['int', 'required']];
        }
        return $rule;
    }

    /**
     * before save
     */
    public function beforeSave($insert)
    {
        // 插入前 对 path 赋值
        if(parent::beforeSave($insert) && $this->isNewRecord) {
            // 初始化路径
            $this->path = $this->getPath();
        }
        return true;
    }
    
    // 修改navigator.controller时，需要更新admin_navigator的navigator_path字段
    public function afterSave($insert, $changedAttributes)
    {
        // echo $changedAttributes['controller'];
        if(parent::afterSave($insert, $changedAttributes)) {
            return $this->onChange($changedAttributes['controller'] ?? '');
        }
        return true;
    }
    
    // @name 修改navigator.controller时
    // @describe 需要更新admin_permission的navigator_path字段
    // @param array $changedAttributes 修改的数据
    // @rturn boolean
    public function onChange($oldController)
    {
        
        if($this->isNewRecord || ($oldController == $this->controller)) {
            return true;
        }
        $sql = "update admin_permission set navigator_path = replace(navigator_path, '{$oldController}~', '{$this->controller}~') where navigator_path like '{$oldController}~%'";
        return Yii::$app->db->createCommand($sql)->execute();
    }

    /**
     * find init path
     */
    public function getTop()
    {
        return $this->hasOne(Navigator::className(), ['id' => 'top_id']);
    }
    
    /**
     * find init path
     */
    public function getParent()
    {
        return $this->hasOne(Navigator::className(), ['id' => 'parent_id']);
    }
    
    /**
     * find init path
     */
    public function getPath()
    {
        if($navigator = $this->getParent()->one()) {
            return $navigator->path.$this->parent_id.',';
        }
        else {
            return ',0,';
        }
    }
    
    /**
     * find all navigators
     * show as relation style
     * 
     * parent_id => [detail]
     */
    public static function getAllRelation()
    {
        $navigators = Navigator::find()->where(['flag' => 1])->orderBy('sort ASC')->asArray()->all();
        return ArrayHelper::index($navigators, 'id', 'parent_id');
    }
    /**
     * for html show nav
     * header nav
     */
    public static function getHeader()
    {
        // -1 = welcome
        $navigator = [['id' => '-1', 'title' => '个人中心', 'icon_class' => 'icon-flag', 'controller' => 'welcome']];
        return array_merge($navigator, Navigator::find()->select('id, title, icon_class, controller')->where(['parent_id' => '0', 'flag' => 1, 'type' => 1])->orderBy('sort ASC')->asArray()->all());
    }
    /**
     * for html show nav
     * lefter nav
     */
    public static function getLefter($parent_id)
    {
        if($parent_id == '-1') {
            return [
                ['id' => '-101', 'title' => '个人资料', 'icon_class' => 'icon-asterisk', 'controller' => 'personal-data'],
                ['id' => '-102', 'title' => '修改密码', 'icon_class' => 'icon-key', 'controller' => 'change-password'],
            ];
        }
        return Navigator::find()->select('id, title, icon_class, controller')->where(['parent_id' => $parent_id, 'flag' => 1, 'type' => 1])->orderBy('sort ASC')->asArray()->all();
    }
}