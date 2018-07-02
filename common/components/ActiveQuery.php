<?php

namespace common\components;

use Yii;
use common\models\Project;

class ActiveQuery extends \yii\db\ActiveQuery {
    
    /**
     * 资源权限过滤
     * @param string $id 编号
     * @return $this
     */
    public function filter($type)
    {
        if(Yii::$app->admin->role->identity != 'super') {
            if($this instanceof Project) {
                $id = 'id';
            }
            $this->andWhere([$id => Yii::$app->admin->power()]);
        }
        return $this;
    }
}
