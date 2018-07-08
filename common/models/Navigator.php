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
    public static $typeSelector = [
        self::TypeFunction => '功能',
        self::TypeNavigator => '菜单',
    ];
    
    // @name 地址跳转方式
    const TargetStatic = 0;
    const TargetSelf = 1;
    const TargetBlank = 2;
    public static $targetSelector = [
        self::TargetStatic => '_static',
        self::TargetSelf => '_self',
        self::TargetBlank => '_blank',
    ];
    
    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['title', 'parent_id', 'top_id', 'controller'], 'required'],
            [['parent_id', 'top_id', 'type', 'target', 'sort', 'status', 'deleted_at'], 'integer'],
            [['path', 'icon_class'], 'string', 'max' => 64],
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
            'title' => 'title',
            'parent_id' => 'parent navigator number',
            'top_id' => 'top navigator number',
            'path' => 'navigator path',
            'type' => 'navigator type',
            'controller' => 'controller',
            'target' => 'target',
            'sort' => 'sort',
            'icon_class' => 'icon class',
            'remark' => 'remark',
            'status' => 'status',
            'deleted_at' => 'deleted at',
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
                'title' => ['title', ['maxlength' => 128, 'required']],
                'controller' => ['controller', ['controller', 'required']],
                'icon_class' => ['icon class', ['maxlength' => 64]],
                'type' => ['navigator type', ['int', 'default' => '0']],
                'target' => ['target', ['in' => array_keys(static::$targetSelector), 'default' => '_static']],
                'sort' => ['sort', ['int', 'required']],
                'remark' => ['remark', ['maxlength' => 255]],
            ]
        ];
        if($type == 'insert') {
            $rule['param']['parent_id'] = ['parent navigator number', ['int', 'required']];
            $rule['param']['top_id'] = ['top navigator number', ['int', 'required']];
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
            $this->path = $this->initPath();
        }
        return true;
    }
    
    /**
     * 保存导航时，调用导航的onChange事件
     * @param boolean $insert 更新的数据
     * @param array $changedAttributes 改变的属性
     * @return boolean|integer|void
     */
    public function afterSave($insert, $changedAttributes)
    {
        // echo $changedAttributes['controller'];
        if(parent::afterSave($insert, $changedAttributes)) {
            return $this->onChange($changedAttributes['controller'] ?? '');
        }
        return true;
    }
    
    /**
     * 控制器改变时，修改关联表
     * @param string $oldController 原控制器名称
     * @return boolean|integer
     */
    public function onChange($oldController)
    {
        if($this->isNewRecord || ($oldController == $this->controller)) {
            return true;
        }
        return Permission::updateAll(['controller' => $this->controller], ['controller' => $oldController]);
    }

    /**
     * find top navigator
     * @return static
     */
    public function getTop()
    {
        return $this->hasOne(Navigator::className(), ['id' => 'top_id']);
    }
    
    /**
     * find parent navigator
     * @return static
     */
    public function getParent()
    {
        return $this->hasOne(Navigator::className(), ['id' => 'parent_id']);
    }
    
    /**
     * build controller path
     * @return string
     */
    public function initPath()
    {
        if($this->parent) {
            return $this->parent->path.$this->parent_id.',';
        }
        else {
            return ',0,';
        }
    }
    
    /**
     * 导航列表结构
     * @return array
     */
    public static function controllers()
    {
        return ArrayHelper::index(Navigator::find()->orderBy('sort asc')->asArray()->all(), 'id', 'parent_id');
    }
    
    /**
     * 导航列表结构简洁版
     * @return array
     */
    public static function controllerSelector()
    {
        return ArrayHelper::map(Navigator::find()->select('id, title, parent_id')->orderBy('sort asc')->asArray()->all(), 'id', 'title', 'parent_id');
    }
}