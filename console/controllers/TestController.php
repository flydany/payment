<?php

namespace console\controllers;

use Yii;
use common\helpers\Checker;
use common\models\Admin;

class TestController extends Controller {
    
    public function actionIndex()
    {
        $path = '.DS_Store';
        for($i = 0; $i < 10; ++$i) {
            $dss = glob($path);
            if(empty($dss)) {
                break;
            }
            foreach($dss as $ds) {
                @unlink($ds);
                echo '[deleted]'.$ds, "\n";
            }
            $path = '*/'.$path;
        }
    }
}
