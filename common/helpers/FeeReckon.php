<?php

/**
 * this helper class was create for fee reckon
 *
 */

namespace common\helpers;

use Yii;
use common\helper\Checker;
use common\models\Platform;
use common\models\Merchant;

class FeeReckon {
    
    // 单例运行
    public static $instance;
    public static function getInstance()
    {
        if( ! is_object(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    // @name 金额计算结果
    public $fee;
    // 计算规则
    public $amount;
    // amount => $this->amount，day => date('Ymd')，time => date('His')
    // @param $rate like [['0.001', [['amount', 'let', 50000]]], ['0.0008', [['amount', 'gt', 50000]]]
    // @describe $amount <= 50000 use rate 0.001, $amount > 50000 use rate 0.0008
    public $rate;
    // @param $max like [['200', [['day', 'eq', ':holiday']]], ['100', [['day', 'eq', ':weekday']]]
    // @describe 节假日 use max 200, 工作日 use max 300
    public $max;
    // @param $min like [['200', [['time', 'let', '12:00:00']]], ['100', [['time', 'gt', '12:00:00']]]
    // @describe 12点之前 use min 200, 12点之后 use min 100
    public $min;
    // @name 基础费率，在计算结果基础上相加
    public $baseFee = 0;
    // @name set Attribute
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }
    /**
     * @name 设置费率计算规则
     * @param $rateRule array | string  费率计算规则
     * @return $this
     */
    public function setRate($rateRule)
    {
        $rate = $this->choiseUse($rateRule);
        if(Checker::checker_float($rate)) {
            $this->rate = $rate;
        }
        return $this;
    }

    /**
     * @name 使用规则选取适用值
     * @param $checkerRules array | string 计算规则
     * @return string
     */
    public function choiseUse($checkerRules)
    {
        if( ! is_array($checkerRules)) {
            return $checkerRules;
        }
        $choise = '';
        foreach($checkerRules as $rules) {
            $required = true;
            foreach($rules[1] as $rule) {
                // $this->v($rule, 'start');
                // 解析$rule数组
                $value = $this->nameValue($rule[0]);
                $func = "checker_{$rule[1]}";
                // 获取数据校验format值
                $format = $this->judgeValue(isset($rule[2]) ? $rule[2] : '');
                // $this->v(['name' => $rule[0], 'type' => $rule[1], 'value' => $value, 'function' => $func, 'format' => $format], 'build');
                if( ! Checker::$func($value, $format)) {
                    $required = false;
                    break;
                }
                // $this->v($required ? 'true' : 'false', 'start');
            }
            // 如果通过，返回当前的应用值
            if($required) {
                $choise = $rules[0];
                break;
            }
        }
        // $this->v($choise, 'choised');
        return $choise;
    }

    /**
     * @name 获取参与判断的属性的值
     * @param $name string 属性
     * @return false|string
     */
    public function nameValue($name)
    {
        switch($name) {
            case 'amount':
                return $this->amount;
            case 'day':
                return date('Ymd');
            case 'time':
                return date('His');
        }
        return '';
    }
    // @name ||||||||此处需修改|||||||||

    /**
     * @name 根据值类型获取判断值
     * @param $format string 值类型
     * @return false|string
     */
    public function judgeValue($format)
    {
        if(substr($format, 0, 1) == ':') {
            switch($format) {
                case ':holiday':
                    return 'is holiday?' ? date('Ymd') : '';
                case ':weekday':
                    return 'is weekday?' ? date('Ymd') : '';
            }
        }
        return $format;
    }
    /**
     * @name 设置最高值计算规则
     * @param $max array | string  最大值计算规则
     * @return $this
     */
    public function setMax($max)
    {
        $max = $this->choiseUse($max);
        if(Checker::checker_int($max)) {
            $this->max = $max;
        }
        return $this;
    }
    /**
     * @name 设置最低值计算规则
     * @param $min array | string  最低值计算规则
     * @return $this
     */
    public function setMin($min)
    {
        $min = $this->choiseUse($min);
        if(Checker::checker_int($min)) {
            $this->min = $min;
        }
        return $this;
    }
    /**
     * @name 设置基础费率计算规则
     * @param $baseFee array | string 基础费率计算规则
     * @return $this
     */
    public function setBaseFee($baseFee = 0)
    {
        $baseFee = $this->choiseUse($baseFee);
        if(Checker::checker_int($baseFee)) {
            $this->baseFee = $baseFee;
        }
        return $this;
    }

    /**
     * @name 充值系统参数
     * @return $this
     */
    public function resetSystem()
    {
        $this->amount = null;
        $this->rate = null;
        $this->max = null;
        $this->min = null;
        $this->baseFee = 0;
        $this->fee = 0;
        return $this;
    }

    /**
     * @name 设置费率计算配置
     * @param $config 费率配置
     * @return $this
     */
    public function initSystem($config)
    {
        foreach(['rate', 'max', 'min', 'baseFee'] as $key) {
            if(isset($config[$key])) {
                call_user_func([$this, 'set'.ucfirst($key)], $config[$key]);
            }
        }
        return $this;
    }

    // @name Use route id and partner id to find config
    // @param $routeId int 通道id
    // @param $partnerId string 商户id
    // @return array
    public static function findConfig($routeId, $partnerId = '', $type = Platform::PaytypeDebit)
    {
        $config = Merchant::finder($routeId, $partnerId, $type);
        if(isset($config['feeRule'])) {
            return $config['feeRule'];
        }
        $config = Yii::$app->params['debitCfg'];
        if(isset($config[$routeId])) {
            if(isset($config[$routeId][$partnerId]['feeRule'])) {
                return $config[$routeId][$partnerId]['feeRule'];
            }
            else if(isset($config[$routeId]['feeRule'])) {
                return $config[$routeId]['feeRule'];
            }
        }
        return [];
    }

    // @name reckon the amount fee
    // @param $amount int 计算金额
    // @param $routeId int 通道id
    // @param $partnerId string 商户id
    // @return int
    public static function reckon($amount, $routeId, $partnerId = '', $type = Platform::PaytypeDebit)
    {
        if( ! $amount) {
            return 0;
        }
        $config = static::findConfig($routeId, $partnerId, $type);
        /** @var $feeReckon FeeReckon */
        $feeReckon = static::getInstance();
        // @name 初始配置
        $feeReckon->resetSystem();
        $feeReckon->setAmount($amount);
        $feeReckon->initSystem($config);
        // @开始计算
        $feeReckon->reckonRate();
        $feeReckon->checkMaxer()->checkMiner();
        $feeReckon->checkBaseFee();
        return $feeReckon->fee;
    }

    // @name Use rule string to reckon fee
    // @return object
    public function reckonRate()
    {
        if( ! $this->rate) {
            return $this;
        }
        $this->fee = floatval(bcmul($this->amount, $this->rate, 0));
        return $this;
    }
    // @name Check wether fee bigger then max
    // @return $this
    public function checkMaxer()
    {
        if( ! $this->max) {
            return $this;
        }
        if($this->max && $this->fee > $this->max) {
            $this->fee = $this->max;
        }
        return $this;
    }
    // @name Check wether fee littler then min
    // @return $this
    public function checkMiner()
    {
        if( ! $this->min) {
            return $this;
        }
        if($this->fee < $this->min) {
            $this->fee = $this->min;
        }
        return $this;
    }
    // @name Add baseFee to fee result
    // @return $this
    public function checkBaseFee()
    {
        if( ! $this->baseFee) {
            return $this;
        }
        $this->fee = bcadd($this->fee, $this->baseFee);
        return $this;
    }

    public function v($data, $title = '')
    {
        echo $title ? $title.':' : '';
        print_r($data);
        echo "\n";
        return $this;
    }
}
