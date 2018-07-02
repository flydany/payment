<?php

namespace common\models;

use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "Category".
 */
class Category extends ActiveRecord {
    
    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['title', 'parent_id', 'path', 'top_id', 'flag'], 'required'],
            [['parent_id', 'top_id', 'sort', 'flag', 'deleted_at'], 'integer'],
            [['icon_class', 'path', 'title'], 'string', 'max' => 64],
            [['remark'], 'string', 'max' => 255],
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
                'title' => ['分类标题', ['maxlength' => 16, 'required']],
                'icon_class' => ['小图标', ['preg' => '/^[\w-_]+$/']],
                'sort' => ['排序', ['int', 'required']],
                'remark' => ['备注', ['maxlength' => 256]],
            ]
        ];
        if($type == 'insert') {
            $rule['param']['parent_id'] = ['所属分类', ['int', 'required']];
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
            $this->buildSystemInfo();
        }
        return true;
    }

    /**
     * @name find category's top id
     * @return object Category
     */
    public function getTop()
    {
        return $this->hasOne(Category::className(), ['id' => 'top_id']);
    }
    
    /**
     * @name find category's parent
     * @return object Category
     */
    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id']);
    }
    
    /**
     * @name bulid parent path from parent category
     * @return string
     */
    public function buildSystemInfo()
    {
        if($category = $this->getParent()->one()) {
            $this->top_id = $category->top_id;
            $this->path = $category->path.$this->parent_id.',';
        }
        else {
            $this->top_id = 0;
            $this->path = ',0,';
        }
        return true;
    }
    
    /**
     * @name 获取前端(categorier.class.js)select自动选择需要使用的多级分类关系
     * @param $topId int 所属顶级分类
     * @return mixed
     */
    public static function categorier($topId)
    {
        $categoryList = static::find()->select('id, title, parent_id')->where(['top_id' => $topId])->asArray()->all();
        $categories['categories'] = json_encode(ArrayHelper::map($categoryList, 'id', 'title', 'parent_id'));
        $categories['category_relate'] = json_encode(ArrayHelper::map($categoryList, 'id', 'parent_id'));
        return $categories;
    }
    
    /**
     * @name 获取当前父元素的所有子元素信息
     * @param $parent_id int 所属父元素
     * @return array
     */
    public static function selector($parent_id)
    {
        $categories = static::find()->select('id, title')->where(['parent_id' => $parent_id])->orderBy('sort ASC')->asArray()->all();
        return ArrayHelper::map($categories, 'id', 'title');
    }
}