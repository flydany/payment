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
                $id = 'project_id';
                if($this->modelClass == 'common\models\Project') {
                    $id = 'id';
                }
                $ids = Yii::$app->admin->getResourcePowers($type);
                // 项目超级权限
                if(in_array(0, $ids)) {
                    return $this;
                }
            } break;
            case AdminResource::TypeMerchant: {
                $id = 'merchant_id';
                if($this->modelClass == 'common\models\Merchant') {
                    $id = 'id';
                }
                $ids = Yii::$app->admin->getResourcePowers($type);
                // 商户号超级权限
                if(in_array(0, $ids)) {
                    return $this;
                }
                if(in_array($this->modelClass, ['common\models\MerchantBank', 'common\models\MerchantBankMaintain'])) {
                    $id = 'merchant_number';
                    $ids = Merchant::find()->select('merchant_number')->where(['id' => $ids])->column();
                }
            } break;
        }
        $this->andWhere([$this->modelClass::tableName().'.'.$id => $ids]);
        return $this;
    }
}
