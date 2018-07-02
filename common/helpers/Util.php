<?php

/**
 * @name 工具方法
 * -----------------------------------
 * Created by Komodo.
 * User: flydany
 * Date: 2017/6/18
 * Time: 09:07
 */

namespace common\helpers;

use Yii;
use yii\helpers\Html;

class Util {

    // Session锁定前缀Key
    const LockSessionSuffix = 'lock_';
    const LockTimeout = 10;
    
    /**
     * @name 删除上传的文件
     * @param $path string 路径
     * @return boolean
     */
    public static function trashUpload($path)
    {
        // @rule path为空或者为默认图片时忽略
        if(empty($path) || (strpos($path, 'static') >= 0)) {
            return true;
        }
        // 删除图片
        @unlink(Yii::getAlias('@upload/'.$path));
        return true;
    }
    /**
     * @name 锁定
     * @param $key 锁定Key
     * @param $time int 锁定时间
     * @return bool
     */
    public static function lock($key, $time = self::LockTimeout)
    {
        $status = Yii::$app->redis->setnx(static::LockSessionSuffix.$key, '1');
        if( ! $status) {
            return false;
        }
        return Yii::$app->redis->expire(static::LockSessionSuffix.$key, $time);
    }
    
    /**
     * @name 解除锁定
     * @param $key string 锁定Key
     * @return mixed
     */
    public static function unLock($key)
    {
        return Yii::$app->redis->del(static::LockSessionSuffix.$key);
    }
    
    /**
     * @name 获取客户端IP地址
     * @return array|false|string
     */
    public static function clientIp()
    {

        foreach(['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED'] as $key) {
            $ip = getenv($key);
            if($ip) {
                return $ip;
            }
        }
        return $_SERVER['REMOTE_ADDR'] ?? '::1';
    }

    /**
     * @name 批量添加数据
     * @param $tbName string 表名
     * @param $columns array 字段数组
     * @param $data array 数据数组
     * @param $db resource 数据库
     * @return bool|int
     */
    public static function batchInsert($tbName, $columns, $data, $db = null)
    {
        $db = ($db === null) ? Yii::$app->db : $db;
        $rows = [];
        foreach($data as $v) {
            $row = [];
            foreach($columns as $col) {
                $row[] = $v[$col];
            }
            $rows[] = $row;
        }
        try {
            $insertSql = Yii::$app->db->queryBuilder->batchInsert($tbName, $columns, $rows);
            return $db->createCommand($insertSql)->execute();
        }
        catch(\Exception $e) {
            return false;
        }
    }
}
