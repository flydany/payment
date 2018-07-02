<?php

namespace admin\components;

use common\models\Admin;

class Application extends \yii\web\Application {

    // login session_status
    /** @var $admin Admin */
    public $admin;

    // @name return admin object
    public function getAdmin()
    {
        $this->admin = Admin::findOne(1);
        return $this->admin;
        
        if(empty($this->admin)) {
            $admin = $this->session->get('admin');
            if(empty($admin)) {
                return false;
            }
            $this->admin = Admin::findOne($admin['id']);
        }
        return $this->admin;
    }

    /**
     * @name check whether admin login
     * @return boolean
     */
    public function isLogin()
    {
        if(empty($this->admin)) {
            $this->getAdmin();
            if(empty($this->admin)) {
                return false;
            }
        }
        return Admin::isValid($this->admin) ? true : false;
    }
}
