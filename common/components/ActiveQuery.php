<?php

namespace common\components;

use Yii;
use common\models\AdminResource;
use common\models\Merchant;

class ActiveQuery extends \yii\db\ActiveQuery {
    
    /**
     * 资源权限过滤
     * @param string $id 编号
     * @return $this
     */
    public function filterResource($type = AdminResource::TypeProject)
    {
        if(Yii::$app->admin->isSupper) {
            return $this;
        }
        switch($type) {
            case AdminResource::TypeProject: {
                $key = 'project_id';
                if($this->modelClass == 'common\models\Project') {
                    $key = 'id';
                }
                $powers = Yii::$app->admin->getResourcePowers($type);
                // 项目超级权限
                if(in_array('0', $powers)) {
                    return $this;
                }
            } break;
            case AdminResource::TypeMerchant: {
                $powers = Yii::$app->admin->getResourcePowers($type);
                // 商户号超级权限
                if(in_array('0', $powers)) {
                    return $this;
                }
                $condition = AdminResource::powerCondition($powers);
                array_unshift($condition, 'or');
                if(in_array($this->modelClass, ['common\models\ProjectMerchant'])) {
                    $key = 'merchant_id';
                    $powers = Merchant::find()->select('id')->where($condition)->column();
                }
                else {
                    $this->andWhere($condition);
                    return $this;
                }
            } break;
        }
        $this->andWhere([$this->modelClass::tableName().'.'.$key => $powers]);
        return $this;
    }
}
