<?php

namespace admin\controllers;

use Yii;
use common\helpers\Uploader;

class CommonController extends Controller {

    /***
     * @name 上传文件操作
     * @param $upload array files
     * @return string file path json
     */
    function actionUploadFile()
    {
        $params = require Yii::getAlias('@admin/config/uploader.php');
        // 上传操作
        $uploader = (new Uploader($params['patternFileLoader']))->upload('upload');
        $this->setStatus($uploader['code'], $uploader['message']);
        return $this->json(['url' => $uploader['url']]);
    }
}