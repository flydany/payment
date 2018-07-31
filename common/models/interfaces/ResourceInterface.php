<?php
/**
 * Created by PhpStorm.
 * User: sunbiao
 * Date: 2018/7/31
 * Time: 下午2:46
 */

namespace common\models\interfaces;

interface ResourceInterface {

    /**
     * 权限标记
     * @return mixed
     */
    public static function resourceType();

    /**
     * 获取权限标识
     * @return mixed
     */
    public function getPower();

    /**
     * 判断管理员是否有权限
     * @return mixed
     */
    public function getHasPermission();

    /**
     * 获取管理员的所有相关权限
     * @return mixed
     */
    public function getIdentities();
}