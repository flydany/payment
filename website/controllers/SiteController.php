<?php

namespace website\controllers;

use Yii;

class SiteController extends Controller {
    
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // $this->layout = 'simple.php';

        return $this->render('index');
    }
}
