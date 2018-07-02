<?php
/**
 * Created by PhpStorm.
 * User: flydany
 * Date: 2018/2/3
 * Time: 12:58
 */
namespace common\server\recharge;

/**
 * 充值接口函数定义
 */
interface RechargeInterface {
    
    /**
     * 初始接口基础类
     * @return mixed
     */
    public function init();
    
    /**
     * 充值申请入口
     * @param $params array 申请参数
     * @return mixed
     */
    public function recharge($params);
    
    /**
     * 订单状态查询入口
     * @param $params array 查询参数
     * @return mixed
     */
    public function rechargeQuery($params);
    
    /**
     * 订单回调
     * @param $params array 回调参数
     * @return mixed
     */
    public function notify($params);
    
    /**
     * 是否成功
     * @return mixed
     */
    public function success();
    
    /**
     * 是否失败
     * @return mixed
     */
    public function failed();
    
    /**
     * 是否处理中
     * @return mixed
     */
    public function dealing();
}