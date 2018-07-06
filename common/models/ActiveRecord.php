<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use common\components\ActiveQuery;

/**
 * This is the base model class.
 */
class ActiveRecord extends \yii\db\ActiveRecord {

    // 求交集对比常量
    const UionSumCompare = 255;

    // 获取主数据库
    public function db()
    {
        return static::getDb();
    }

    // 获取只读数据库
    public function dbRead(){
        return Yii::$app->get('dbrd');
    }

    /**
     * 执行事务
     * @param callable $callback a valid PHP callback that performs the job. Accepts connection instance as parameter.
     * @param string|null $isolationLevel The isolation level to use for this transaction.
     * @return mixed result of callback function
     */
    public function transaction(callable $callable, $isolationLevel = null)
    {
        try {
            return $this->db()->transaction($callable, $isolationLevel);
        }
        catch(\Exception $exception) {
            # 此处临时修改为抛异常，上线之后需要修改回来
            // return false;
            throw $exception;
        }
    }

    /**
     * 执行原生SQL语句
     * @param $sql string SQL语句
     * @return boolean
     */
    public function executeQuery($sql)
    {
        return $this->db()->createCommand($sql)->execute();
    }

    /**
     * 加上下面这行，数据库中的created_at和updated_at会自动在创建和修改时设置为当时时间戳
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => function() {
                    return time();
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     * @return ActiveQuery the newly created [[ActiveQuery]] instance.
     */
    public static function find()
    {
        return Yii::createObject(ActiveQuery::className(), [get_called_class()]);
    }

    /**
     * 返回数据校验规则
     * @param $type string[insert|update] 校验规则
     * @param $encode boolean
     */
    public static function checker($type = 'insert', $encode = true)
    {
        $rule = static::flyer($type);
        return $encode ? json_encode($rule) : $rule;
    }

    /**
     * error __toString()
     * 组装validate错误
     */
    public function errors()
    {
        return implode('. ', ArrayHelper::getColumn($this->getErrors(), '0'));
    }
    
    /**
     * set Attributes by front -> database key
     * @param $param array 填充的数组
     * @param $tbKey array 需要转换键名称的对应关系
     * @param $checkValid boolean 是否需要检测值有效性
     * @return boolean
     */
    public function loadAttributes($param, $tbKey = null, $checkValid = false)
    {
        if($tbKey) {
            foreach($tbKey as $key => $dbKey) {
                if(isset($param[$key])) {
                    $param[$dbKey] = $param[$key];
                    unset($param[$key]);
                }
            }
        }
        // $setParams = [];
        // // 提取 页面展示key => 数据库存储key
        // $tbKey && $tbKey = array_flip($tbKey);
        // // 循环获取model类的各个字段值
        // foreach($this->fields() as $key) {
        //     if(isset($tbKey[$key])) {
        //         isset($param[$tbKey[$key]]) && $setParams[$key] = $param[$tbKey[$key]];
        //     }
        //     else {
        //         isset($param[$key]) && $setParams[$key] = $param[$key];
        //     }
        // }
        // echo '<pre>'; print_r($param); die;
        parent::setAttributes($param, $checkValid);
        return $this;
    }

    // set Attributes by front -> database key
    public function loads($param, $tbKey = null)
    {
        if($tbKey) {
            foreach($tbKey as $key => $dbKey) {
                if(isset($param[$key])) {
                    $param[$dbKey] = $param[$key];
                    unset($param[$key]);
                }
            }
        }
        return parent::load([static::className() => $param], static::className());
    }
    
    /**
     * 校验数据是否存在/允许编辑
     * @param $id int 需要校验的数据编号
     * @param $condition array 附加条件
     * @return bool|static
     */
    public static function finder($id, $condition = [])
    {
        // id 为必填项，判断数据存在状态
        if(empty($id)) {
            // 参数异常，渲染错误页面
            return false;
        }
        $object = static::find()->where(['id' => $id])->andFilterWhere($condition)->one();
        // 未得到，渲染错误页面
        if(empty($object)) {
            return false;
        }
        return $object;
    }

    /**
     * 更新数据追加前置条件
     * @param array $conditions 前置条件数组
     * @return integer
     */
    public function cSave($conditions = [])
    {
        $conditions['id'] = $this->id;
        $conditions['created_at'] = $this->created_at;
        return static::updateAll($this->attributes, $conditions);
    }

    /**
     * 回收数据
     * @return boolean
     */
    public function trash()
    {
        if( ! property_exists($this, 'deleted_at')) {
            return false;
        }
        $this->deleted_at = time();
        $this->updated_at = time();
        return $this->save();
    }

    /**
     * 批量回收数据
     * @param array $condition 条件数组
     * @return integer
     */
    public static function trashAll($condition)
    {
        if( ! key_exists('deleted_at', Yii::$app->db->getSchema()->getTableSchema(static::tableName())->columns)) {
            return false;
        }
        if(empty($condition)) {
            return false;
        }
        return static::updateAll(['deleted_at' => time(), 'updated_at' => time()], $condition);
    }

    /**
     * wether data delete
     * @return boolean
     */
    public function deleted()
    {
        if( ! property_exists($this, 'deleted_at')) {
            return false;
        }
        return $this->deleted_at > 0 ? true : false;
    }

    /**
     * wether data deleted
     * @param $data array|object 数据源
     * @return boolean
     */
    public static function isDeleted($data)
    {
        if( ! isset($data['deleted_at'])) {
            return false;
        }
        return $data['deleted_at'] > 0 ? true : false;
    }
    
    /**
     * 使用get数据创建查询条件
     * @param $tabkey array 生成条件规则
     * @return array
     */
    public static function initGetCondition($tabkey)
    {
        return static::initCondition($tabkey, Yii::$app->getRequest()->get());
    }
    /**
     * 使用post数据创建查询条件
     * @param $tabkey array 生成条件规则
     * @return array
     */
    public static function initPostCondition($tabkey)
    {
        return static::initCondition($tabkey, Yii::$app->getRequest()->post());
    }
    // 初始化查询条件
    // @param $tableKey array 需要组建条件的 key 转换
    // @describe $tableKey like ['search key', 'search key' => ['search key', 'handle'], ['db key', 'handle', 'search key']]
    // @param $param array 查询数据条件值
    // @return array
    public static function initCondition($tabkey, $params)
    {
        $condition = [];
        if($params) {
            foreach($tabkey as $key => $dbkey) {
                if(is_numeric($key)) {
                    $key = is_array($dbkey) ? (isset($dbkey[2]) ? $dbkey[2] : $dbkey[0]) : $dbkey;
                }
                if( ! isset($params[$key]) || ( ! is_array($params[$key]) && $params[$key] == '' && strlen($params[$key]) == 0)) {
                    continue;
                }
                if( ! is_array($dbkey)) {
                    $dbkey = [$dbkey, 'eq'];
                }
                switch($dbkey[1]) {
                    case 'eq': {
                        $condition[$dbkey[0]] = $params[$key];
                    } break;
                    default: {
                        $condition[] = [$dbkey[1], $dbkey[0], $params[$key]];
                    } break;
                }
            }
        }
        return $condition;
    }

    // 创建查询条件
    // @param $condition array 查询条件
    // @describe bulid query by conditions include null value
    // @param $tableKey array 键值转换
    // @describe $tableKey like ['attribute' => 'value', ['operate', 'attribute', 'value']]
    // @describe $tableKey like ['attribute' => 'value', ['attribute' => 'value'], ['operate', 'attribute', 'value']]
    // @return object
    public static function bulidCondition($conditions = [], $tableKey = [])
    {
        $query = static::find();
        if(empty($conditions)) {
            return $query;
        }
        foreach($conditions as $attribute => $condition) {
            if( ! is_array($condition)) {
                $query->andWhere([$attribute => $condition]);
            }
            elseif(is_numeric($attribute)){
                $query->andWhere($condition);
            }
            else {
                $query->andWhere([$attribute => $condition]);
            }
        }
        return $query;
    }
    
    // 创建查询条件
    // $condition array 查询条件
    // bulid query by conditions except null value
    // compatible ['attribute' => 'value', ['operate', 'attribute', 'value']]
    // ['attribute' => 'value', ['attribute' => 'value'], ['operate', 'attribute', 'value']]
    // @return object
    public static function filterConditions($conditions = [], $tableKey = [])
    {
        $query = static::find();
        if(empty($conditions)) {
            return $query;
        }
        foreach($conditions as $attribute => $condition) {
            if( ! is_array($condition)) {
                $query->andFilterWhere([$attribute => $condition]);
            }
            elseif(is_numeric($attribute)){
                $query->andFilterWhere($condition);
            }
            else {
                $query->andFilterWhere([$attribute => $condition]);
            }
        }
        return $query;
    }
}
