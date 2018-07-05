<?php
/**
 * Created by PhpStorm.
 * User: flydany
 * Date: 2018/2/3
 * Time: 13:14
 */
namespace common\helpers;

use Yii;

class Signature {
    
    protected $publicKey;
    protected $privateKey;
    protected $privatePassword;
    
    public function __construct($privateKey, $privatePassword, $publicKey)
    {
        $this->privateKey = $privateKey;
        $this->privatePassword = $privatePassword;
        $this->publicKey = $publicKey;
    }
    
    
    public function convertParams($params)
    {
        ksort($params);
        return http_build_query($params);
    }
    public function encrypt($params)
    {
        $string = $this->convertParams($params);
        return $this->rsaEncrypt($string);
    }
    
    public function rsaEncrypt($string)
    {
        return $string;
    }
    public function rsaDecrypt($string)
    {
        return $string;
    }
}