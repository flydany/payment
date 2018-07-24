<?php

namespace common\components;

use Yii;
use common\models\AdminResource;

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
                $id = 'project_id';
                if($this->modelClass == 'common\models\Project') {
                    $id = 'id';
                }
            } break;
            case AdminResource::TypeMerchant: {
                $id = 'merchant_id';
                if($this->modelClass == 'common\models\Merchant') {
                    $id = 'id';
                }
            } break;
        }
        $this->andWhere([$this->modelClass::tableName().'.'.$id => Yii::$app->admin->getResourceNumbers($type)]);
        return $this;
    }
}
