<?php

namespace common\helpers;

use yii;
use yii\web\UploadedFile;

class Uploader {

    public $path;
    public $allow;
    public $size;

    // @name 初始化配置
    // @param $config array 配置参数
    public function __construct(array $config = [])
    {
        // 设置上传相关配置参数
        $this->setPath($config['path']);
        $this->setAllow($config['allow']);
        $this->setSize($config['size']);
    }

    /**
     * @name 设置网址路径
     * @param $host string
     * @return $this object
     */
    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }
    /**
     * @name 设置上传路径
     * @param $path string
     * @return $this object
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }
    /**
     * @name 设置上传文件类型限制
     * @param $allow string
     * @return $this object
     */
    public function setAllow($allow)
    {
        $this->allow = $allow;
        return $this;
    }
    /**
     * @name 设置上传文件最大允许大小
     * @param $size string
     * @return $this object
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    // @name 判断当前文件是否图片格式
    // @return boolean
    public function isImage()
    {
        return true;
    }
    // @name 随机生成文件名称
    // @return string
    public function generateFileName()
    {
        return strtoupper(base_convert(time() . rand(100, 999) . rand(1000, 9999), 10, 26));
    }

    // @name 根据伪路径生成真是路径
    // @param $this->path string 伪路径（'designer/{yyyy}{mm}/'）
    // @return string
    public static function buildPath($path)
    {
        $year = date('Y');
        $month = date('m');
        $day = date('d');
        $path = str_replace(
            ['{yyyy}', '{mm}', '{dd}'],
            [$year, $month, $day],
            $path
        );
        return $path;
    }

    /**
     * @name 文件上传操作
     * @param $originName string file文件名
     * @return array [code, message, url]
     */
    public function upload($originName)
    {
        $this->path = static::buildPath($this->path);
        $root = Yii::getAlias('@upload').'/'.$this->path;
        //返回一个实例化对象
        $files = UploadedFile::getInstanceByName($originName);
        if(empty($files)) {
            return ['code' => 'File.Error', 'message' => '图片对象不存在'];
        }
        if($files && in_array($files->type, $this->allow)) {
            $newName = $this->generateFileName().'.'.$files->getExtension();
        }
        else {
            return ['code' => 'File.Error', 'message' => '不支持的文件格式: '.$files->type.'（File Error）'];
        }
        if($files->size > $this->size) {
            return ['code' => 'File.Error', 'message' => '上传的文件太大（File Error）'];
        }        
        if( ! is_dir($root)) {
            if( ! mkdir($root, 0777, true)) {
                return ['code' => 'AdminPermission.Denied', 'message' => '创建目录失败（AdminPermission Denied Of Make Dir）'];
            }
        }
        if($files->saveAs($root.$newName)) {
            return ['code' => SuccessCode, 'message' => 'success', 'url' => Render::upload($this->path.$newName), 'path' => $this->path.$newName];
//            if($this->thumb) {
//                $this->thumbphoto($files, $this->path.$newName, $this->path.'thumb'.$newName);
//                return $this->path.$newName.'#'.$this->path.'thumb'.$newName;
//            }
//            else {
//                return $this->path.$newName;
//            }
        }
        else {
            return ['code' => 'Upload.Error', 'message' => '上传失败（Upload Error）'];
        }
    }
}