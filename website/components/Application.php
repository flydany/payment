<?php

namespace website\components;

use Yii;
use common\models\User;

class Application extends \yii\web\Application {

    // login session_status
    public $user;

    // @name return user object
    public function getUser()
    {
        if(empty($this->user)) {
            $user = $this->session->get('user');
            if(empty($user)) {
                return false;
            }
            $this->user = User::findOne($user['id']);
        }
        return $this->user;
    }

    /**
     * @name check whether user login
     * @return boolean
     */
    public function isLogin()
    {
        if(empty($this->user)) {
            $this->getUser();
            if(empty($this->user)) {
                return false;
            }
        }
        return User::isValid($this->user) ? true : false;
    }
}
