<?php

/**
 * 工具方法
 * -----------------------------------
 * Created by Komodo.
 * User: flydany
 * Date: 2017/6/18
 * Time: 09:07
 */

namespace admin\helpers;

use Yii;
use yii\helpers\Url;

class Render extends \common\helpers\Render {

    /**
     * 用户上传资料
     * @param $url string 资源路径
     * @return string
     */
    public static function upload($url)
    {
        if(empty($url)) {
            return '';
        }
        return Url::to('@web/upload/'.$url);
    }
}
