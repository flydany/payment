<?php

namespace admin\controllers;

use yii\helpers\ArrayHelper;
use common\models\Category;
use common\helpers\Checker;
use common\helpers\Pager;

/**
 * ShellController implements the CRUD actions for Shell model.
 */
class ShellController extends Controller {
    
    public function actionIndex()
    {
        return $this->render('shell-list');
    }
}