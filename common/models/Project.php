<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\components\ActiveQuery;
use common\models\interfaces\ResourceInterface;

/**
 * This is the model class for table "Project".
 */
class Project extends ActiveRecord implements ResourceInterface {

    const StatusNormal = '0';
    const StatusForbidden = '1';
    public static $statusSelector = [
        self::StatusNormal =>  'normal',
        self::StatusForbidden => 'forbidden',
    ];

    // only define rules for those attributes that
    // will receive user inputs.
    public function rules()
    {
        return [
            [['title', 'effect_date', 'public_key', 'status'], 'required'],
            [['status', 'deleted_at'], 'integer'],
            [['title'], 'string', 'max' => 64],
            [['effect_date'], 'string', 'max' => 16],
            [['remark'], 'string', 'max' => 255],
            [['public_key'], 'string', 'max' => 1024],
        ];
    }
    /**
     * 字段名称
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'title' => 'title',
            'effect_date' => 'effect date',
            'public_key' => 'rsa public',
            'status' => 'status',
            'remark' => 'remark',
            'deleted_at' => 'deleted at',
        ];
    }
    /**
     * update & insert data check config for html
     * @param $type string 页面操作类型
     * @param $encodeJson boolean 是否转成json串
     * @return string / array
     */
    public static function flyer($type = 'update')
    {
        $rule = [
            'param' => [
                'title' => ['title', ['maxlength' => 64, 'required']],
                'effect_date' => ['effect date', ['date' => 'Y-m-d', 'required']],
                'public_key' => ['rsa public', ['maxlength' => 1024, 'required']],
                'remark' => ['remark', ['maxlength' => 255, 'required']],
                'status' => ['status', ['in' => array_keys(static::$statusSelector), 'required']],
            ],
        ];
        return $rule;
    }

    /**
     * 新建项目，赋予创建人权限
     * @param boolean $insert 是否创建
     * @param array $changedAttributes 改变的属性
     * @return boolean|void
     */
    public function afterSave($insert, $changedAttributes)
    {
        if( ! parent::afterSave($insert, $changedAttributes)) {
            return false;
        }
        if($insert && ! Yii::$app->admin->isSupper) {
            AdminResource::creator(Yii::$app->admin->id, $this->id, static::resourceType());
        }
        return true;
    }

    /**
     * 获取项目的商户号配置
     * @return ActiveQuery
     */
    public function getProjectMerchants()
    {
        return $this->hasMany(ProjectMerchant::className(), ['project_id' => 'id']);
    }
    public function getRechargeMerchants()
    {
        return $this->getProjectMerchants()->where(['paytype' => Platform::PaytypeRecharge]);
    }
    public function getWithdrawMerchants()
    {
        return $this->getProjectMerchants()->where(['paytype' => Platform::PaytypeWithdraw]);
    }
    public function getAgreementMerchants()
    {
        return $this->getProjectMerchants()->where(['paytype' => Platform::PaytypeAgreement]);
    }

    /**
     * 返回权限类型
     * @return mixed
     */
    public static function resourceType()
    {
        return AdminResource::TypeProject;
    }

    /**
     * 返回权限标识
     * @return mixed
     */
    public function getPower()
    {
        return $this->id;
    }

    /**
     * 判断当前用户是否有此项目的权限
     * @return boolean
     */
    public function getHasPermission()
    {
        return AdminResource::hasPermission($this->power, static::resourceType());
    }
    /**
     * 获取项目已存在的负责人
     * @return array
     */
    public function getIdentities()
    {
        return AdminResource::identities($this->power, static::resourceType());
    }
    
    /**
     * 获取项目联系人
     * @return object
     */
    public function getContacts()
    {
        return $this->hasOne(ProjectContacts::className(), ['project_id' => 'id']);
    }

    /**
     * 获取项目列表
     * @return array
     */
    public static function selector()
    {
        return ArrayHelper::map(
            array_map(
                function($value) {
                    return ['id' => $value['id'], 'title' => $value['id'].'.'.$value['title']];
                },
                static::find()->select('id, title')->where(['status' => static::StatusNormal])->filterResource(static::resourceType())->orderBy('id desc')->asArray()->all()),
            'id', 'title'
        );
    }
}