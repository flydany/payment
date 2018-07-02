<?php

/**
 * this helper class was create for check post / get data's format
 *
 */

namespace common\helpers;

use Yii;

class Checker {

    const SuccessCode = 200;

    /**
     * is _echo == true
     * echo the complate rule & data
     * make you seen the data complate process
     */
    public $_echo = false;

    // 单例运行数据检查操作
    public static $instance;
    public static function getInstance()
    {
        if( ! is_object(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }
    // 验证规则变量
    public $rules;
    public $params;
    // 设置验证数据
    public function setParams($params)
    {
        $this->params = $params;
    }
    // 设置验证数据
    public function setRules($rules)
    {
        $this->rules = $rules;
    }

    // 验证结果变量
    public $status;
    public $message = [];
    public $recycle = false;
    // @name 设置处理状态
    public function setStatus($status)
    {
        $this->status = $status;
    }
    // @name 获取处理状态
    public function getStatus()
    {
        return $this->status;
    }
    // @name 设置异常之后是否继续校验状态
    public function setRecycle($recycle)
    {
        $this->recycle = $recycle;
    }
    // @name 获取异常之后是否继续校验状态
    public function getRecycle()
    {
        return $this->recycle;
    }
    // @name 是否继续校验
    public function unRecycle()
    {
        return $this->recycle ? false : true;
    }
    // @name 设置错误描述
    public function setMessage($message)
    {
        $this->message = $message;
    }
    // @name 追加错误描述
    public function addMessage($message)
    {
        $this->message[] = $message;
    }
    // @name 获取错误描述
    public function getMessage()
    {
        return implode('。', $this->message);
    }
    // @name 获取规则中预设的异常提示
    public function getNotice($key, $type, $default = '')
    {
        if(isset($this->rules['param'][$key][2][$type])) {
            return $this->rules['param'][$key][2][$type];
        }
        return $default;
    }

    // @name 重置配置参数
    public function reset()
    {
        $this->setRules([]);
        $this->setParams([]);
        $this->setRecycle(false);
        $this->setStatus(static::SuccessCode);
        $this->setMessage([]);
    }

    /**
     * @name 校验参数格式是否异常，参数处理流程
     * @param $params array 参数数组
     * @param $rules array 规则数组
     * @param $recycle boolean 遇错是否退出校验
     * @return array
     */
    public static function authentication($rules, $params, $recycle = false)
    {
        /* @var $checker Checker */
        $checker = static::getInstance();
        $checker->reset();
        $checker->setRecycle($recycle);
        $checker->setParams($params);
        $checker->setRules($rules);
        try {
            // 多选一 规则重置
            $checker->initOrRelateRule();
            // 关系数据 规则重置
            $checker->initRelateRule();
            // 开始校验每条数据规则
            return $checker->verifyData();
        }
        catch(\Exception $exp) {
            return ['code' => 'System.Error', 'message' => $exp->getMessage()];
        }
        // 数据逐条验证
    }
    // @name 判断是否验证通过
    public static function isValidate()
    {
        /* @var $checker Checker */
        $checker = static::getInstance();
        return $checker->status == static::SuccessCode;
    }
    // 对应关系 验证
    public function initRelateRule()
    {
        if( ! (isset($this->rules['relate']) && $this->rules['relate'])) {
            return true;
        }
        foreach($this->rules['relate'] as $key => $relate) {
            if(static::checker_int($key) === true) {
                $this->reBuliderRelateRule($relate);
            }
            // 目前只需要验证 type = value 类型的数据，后续如果需要可以在此处扩展
            else {
                throw new \Exception("未知的关联数据验证模式：{$key}", 101004);
            }
        }
    }
    // 多选一 验证
    public function initOrRelateRule() { }
    // 值相关的对应关系验证 【'rules(array)', 'params required(array)'】
    // 如：[[['type', 'eq', 1], ['option', 'in', [1, 2]]], ['reqTxDate', 'reqTxTime', 'reqSeqNo']],
    public function reBuliderRelateRule($relate)
    {
        $required = true;
        foreach($relate[0] as $rule) {
            if( ! is_array($rule)) {
                throw new \Exception('未知的数据值关联验证规则', 101005);
            }
            // 解析$rule数组
            $name = $rule[0];
            $type = $rule[1];
            // 获取关联键的值
            $value = isset($this->params[$name]) ? $this->params[$name] : null;
            switch($type) {
                // 通过校验验证
                case 'pass': {
                    // 验证当前数据
                    $checker = $this->singleCheck($this->rules['param'][$name], $name, $value);
                    if($checker['code'] != 'pass') {
                        $required = false;
                    }
                } break;
                // 其他校验验证
                default: {
                    $func = "checker_{$type}";
                    // 未定义的验证规则，直接退出返回错误
                    if( ! method_exists($this, $func)) {
                        throw new \Exception("未定义数据值关联验证方法：{$func}", 101006);
                    }
                    // 获取数据校验format值
                    $format = isset($rule[2]) ? $rule[2] : '';
                    if( ! call_user_func_array([$this, $func], [$value, $format]) === true) {
                        $required = false;
                    }
                }
            }
        }
        if($required) {
            /**
             * 实现功能 -> 当idType = static::$IdTypeIDCard时，idNo的格式必须是身份证号格式，且必填
             * [[['idType', 'eq', static::$IdTypeIDCard]], ['idNo' => ['rule' => ['idcard', 'required']]]
             * [[['idType', 'eq', static::$IdTypeIDCard]], ['idNo' => ['rule' => ['idcard'], 'required']]],
             * [[['idType', 'eq', static::$IdTypeIDCard]], ['idNo' => ['rule' => 'idcard']], 'idNo'],
             * 以上三种执行效果是一致的
             */
            if( ! is_array($relate[1])) {
                $relate[1] = [$relate[1]];
            }
            foreach($relate[1] as $name => $rule) {
                if(static::checker_int($name) === true) {
                    if( ! isset($this->rules['param'][$rule])) {
                        throw new \Exception("未知关联数据：{$rule}", 101007);
                    }
                    $this->rules['param'][$name][1]['required'] = true;
                }
                else {
                    if( ! isset($this->rules['param'][$name])) {
                        throw new \Exception("未知关联数据：{$name}", 101007);
                    }
                    foreach($rule as $type => $value) {
                        if(static::checker_int($type) === true) {
                            if($value == 'required') {
                                $this->rules['param'][$name][1]['required'] = true;
                                continue;
                            }
                        }
                        switch($type) {
                            case 'rule': {
                                $this->rules['param'][$name][1] = $value;
                            } break;
                            case 'required': {
                                $this->rules['param'][$name][1]['required'] = true;
                            } break;
                            case 'message': {
                                $this->rules['param'][$name][2] = $value;
                            } break;
                            default : {
                                throw new \Exception("未知关联数据规则改变：{$type}", 101008);
                            }
                        }
                    }
                }
            }
        }
    }
    // 根据rule对数据进行验证 【'title(string)', 'rules(array)', 'required(required|true|false)', 'message(array)'】
    // 如：['电子账号', ['accountid', 'length' => 19, 'required'], ['accountid' => '格式错误', 'length' => '长度错误']]
    public function verifyData()
    {
        // 预设处理状态为校验通过
        $this->setStatus(static::SuccessCode);
        foreach($this->rules['param'] as $name => $rule) {
            // 获取值
            $value = isset($this->params[$name]) ? $this->params[$name] : null;
            // 检测是否验证通过
            $checker = $this->singleCheck($rule, $name, $value);
            if($checker['code'] != 'pass') {
                $this->setStatus($checker['code']);
                $this->addMessage($checker['message']);
                if($this->unRecycle()) {
                    break;
                }
            }
        }
        return ['code' => $this->getStatus(), 'message' => $this->getMessage()];
    }
    // @name  验证单条数据
    // @param $rule array 验证规则
    // @param $name string 验证字段名称
    // @param $value string / array 字段数据
    // @return array['code', 'message']
    public function singleCheck($rule, $name, $value)
    {
        if($this->_echo) {
            echo 'check: name->', $name, ', value->', print_r($value), '<br>';
            print_r($rule);
            echo '<br>';
        }
        $oneStatus = 'pass'; $oneMsg = [];
        if( ! isset($rule[1])) {
            return ['code' => $oneStatus, 'message' => $oneMsg];
        }
        // 获取 参数值
        if($value === '' || $value === null) {
            if($rule[1] == 'required' || isset($rule[1]['required']) || (is_array($rule[1]) && in_array('required', $rule[1]))) {
                $oneStatus = 'null';
                $oneMsg[] = $this->getNotice($name, 'required', '不能为空');
            }
        }
        else {
            if( ! is_array($rule[1])) {
                $rule[1] = [$rule[1]];
            }
            // 对 check 数组中的验证类型进行验证
            foreach($rule[1] as $type => $format) {
                if($this->unRecycle() && $oneStatus != 'pass') {
                    break;
                }
                // 忽略单数据必填验证
                if( ! is_array($value) && ($type === 'required' || $format === 'required')) {
                    continue;
                }
                if(static::checker_int($type) === true) {
                    $type = $format;
                }
                else {
                    // if $rule 's value format is :*** 则使用$this->params[$format] 的值替代
                    if(static::checker_string($format) && substr($format, 0, 1) === ':') {
                        $format = str_replace(':', '', $format);
                        if( ! isset($this->params[$format])) {
                            throw new \Exception('未知的关联数据键');
                        }
                        $format = $this->params[$format];
                    }
                }
                $func = 'checker_'.$type;
                if( ! method_exists($this, $func)) {
                    throw new \Exception("未定义数据值关联验证方法：{$func}", 101006);
                }
                if( ! is_array($value)) {
                    if(call_user_func_array([$this, $func], [$value, $format]) !== true) {
                        $oneStatus = 'error';
                        $oneMsg[] = $this->getNotice($name, $type, static::getWarn($func));
                    }
                }
                else {
                    foreach($value as $k => $v) {
                        if(($msg = call_user_func_array([$this, $func], [$v, $format])) !== true) {
                            $oneStatus = 'error';
                            $oneMsg[] = '第'.($k + 1).'条'.$this->getNotice($name, $type, $msg);
                            if($this->unRecycle()) {
                                break 2;
                            }
                        }
                    }
                }
            }
        }
        // echo str_pad($name, 30, ' '), '：', str_pad($oneStatus, 10, ' '), ' - ', $oneMsg, "\n";
        if($oneStatus != 'pass') {
            $oneMsg = $rule[0] .'：'. implode(', ', $oneMsg);
        }
        if($this->_echo) {
            echo 'verify: status->', $oneStatus, ', message->', print_r($oneMsg), '<br>';
        }
        return ['code' => $oneStatus, 'message' => $oneMsg];
    }
    // 数据验证函数
    // 定长 类型
    public static function checker_length($value, $format)
    {
        return (mb_strlen($value, 'UTF-8') == $format) ? true : false;
    }
    // 最大长度 类型
    public static function checker_maxlength($value, $format)
    {
        return (mb_strlen($value, 'UTF-8') <= $format) ? true : false;
    }
    // 最大长度 类型
    public static function checker_minlength($value, $format)
    {
        return (mb_strlen($value, 'UTF-8') >= $format) ? true : false;
    }
    // 相等 类型
    public static function checker_eq($value, $format)
    {
        $checker = static::getInstance();
        $eqValue = $format; $title = '';
        if(isset($checker->rules[$format])) {
            $eqValue = isset($checker->params[$format]) ? $checker->params[$format] : '';
            $title = $checker->rules[$format]['title'];
        }
        if($value == '') {
            return ($value === $eqValue) ? true : false;
        }
        else {
            return ($value == $eqValue) ? true : false;
        }
    }
    // @name 检测必填
    public static function checker_required($value, $format = '')
    {
        return ! static::checker_empty($value, $format);
    }
    // 空串 类型
    public static function checker_empty($value, $format = '')
    {
        return ($value === null || $value === '' || $value === []) ? true : false;
    }
    // in数组 类型
    public static function checker_in($value, $format)
    {
        return in_array($value, $format) ? true : false;
    }
    // in数组key 类型
    public static function checker_inkey($value, $format)
    {
        return array_key_exists($value, $format) ? true : false;
    }
    // 小于 类型
    public static function checker_lt($value, $format)
    {
        return ($value < $format) ? true : false;
    }
    // 小于等于 类型
    public static function checker_let($value, $format)
    {
        return ($value <= $format) ? true : false;
    }
    // 大于 类型
    public static function checker_gt($value, $format)
    {
        return ($value > $format) ? true : false;
    }
    // 大于等于 类型
    public static function checker_get($value, $format)
    {
        return ($value >= $format) ? true : false;
    }
    // number 类型
    public static function checker_number($value, $format = '')
    {
        return preg_match("/^\d+$/", $value) ? true : false;
    }
    // int 类型
    public static function checker_int($value, $format = '')
    {
        return preg_match("/^[+\-]?\d+$/", $value) ? true : false;
    }
    // string 类型
    public static function checker_string($value, $format = '')
    {
        return (is_string($value) && preg_match("/^.+$/", $value)) ? true : false;
    }
    // float 类型
    public static function checker_float($value, $format = '')
    {
        return preg_match("/^\d+(\.\d{0,5})?$/", $value) ? true : false;
    }
    // @name 标签组校验
    public static function checker_tags($value, $format = '')
    {
        return preg_match("/^([^;]{1,6};){0,4}([^;]{1,6})?$/", $value) ? true : false;
    }
    // 电子账号 类型
    public static function checker_accountid($value, $format = '')
    {
        return (strlen($value) == 19) ? true : false;
    }
    // 银行卡号 类型
    public static function checker_bankcard($value, $format = '')
    {
        $arr_no = str_split($value);
        $last_n = $arr_no[count($arr_no) - 1];
        krsort($arr_no);
        $i = 1;
        $total = 0;
        foreach ($arr_no as $n) {
            if($i % 2 == 0) {
                $ix = $n * 2;
                if($ix >= 10) {
                    $nx = 1 + ($ix % 10);
                    $total += $nx;
                } else {
                    $total += $ix;
                }
            } else {
                $total += $n;
            }
            ++$i;
        }
        return ($last_n == (($total - $last_n) * 9) % 10) ? true : false;
    }
    // 手机号 类型
    public static function checker_mobile($value, $format = '')
    {
        return preg_match("/1\d{10}$/", $value) ? true : false;
    }
    // 邮箱 类型
    public static function checker_email($value, $format = '')
    {
        return preg_match("/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/", $value) ? true : false;
    }
    // 正则匹配
    public static function checker_preg($value, $format)
    {
        return preg_match($format, $value) ? true : false;
    }
    // check string is Format Date ?
    public static function checker_date($value, $format = 'Ymd')
    {
        $unixTime_1 = strtotime($value);
        if ( ! is_numeric($unixTime_1)) {
            return '格式错误';
        }
        $checkDate = date($format, $unixTime_1);
        $unixTime_2 = strtotime($checkDate);
        // echo $unixTime_1, '-', $unixTime_2; die;
        return (($unixTime_1 == $unixTime_2) && ($value == $checkDate)) ? true : false;
    }
    // idcard 类型
    public static function checker_idcard($value, $format = '')
    {
        $preg = preg_match("/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/", $value);
        $preg || $preg = preg_match("/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/", $value);
        return $preg ? true : false;
    }
    // url 类型
    public static function checker_url($value, $format = '')
    {
        // if(YII_ENV != 'prod') {
        //     return preg_match("/(http(s)?)?(:\/\/)?([\da-z0-9-\.]+)([\/\w \.-?:&%-=]*)*\/?/", $value) ? true : false;
        // }
        return preg_match("/(http(s)?)?(:\/\/)?([\da-z0-9-\.]+)\.([a-z0-9]{2,6})([\/\w \.-?:&%-=]*)*\/?/", $value) ? true : false;
    }
    // IP 类型
    public static function checker_ip($value, $format = '')
    {
        return preg_match("/^(([01]?\d?\d|2[0-4]\d|25[0-5])\.){3}([01]?\d?\d|2[0-4]\d|25[0-5])$/", $value) ? true : false;
    }
    // check string is json ?
    public static function checker_json($value, $format = '')
    {
        json_decode($value);
        return (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
    // check value is null OR '' ?
    public static function checker_null($value, $format = '')
    {
        return strlen($value) > 0 ? true : false;
    }
    // check number is QQ ?
    public static function checker_qq($value, $format = '')
    {
        return preg_match("/^[1-9][0-9]{4,11}$/", $value) ? true : false;
    }
    // check number is telephone ?
    public static function checker_phone($value, $format = '')
    {
        return preg_match("/^[0-9-+\s]{4,16}$/", $value) ? true : false;
    }
    // check string is chinese ?
    public static function checker_chinese($value, $format = '')
    {
        return preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $value) ? true : false;
    }
    // check string is english ?
    public static function checker_english($value, $format = '')
    {
        return preg_match("/^[a-zA-Z]+$/", $value) ? true : false;
    }
    // check string is valid for username ?
    public static function checker_username($value, $format = '')
    {
        return preg_match("/^[\w-_@\.]{4,}$/i", $value) ? true : false;
    }
    // check string is valid for password ?
    public static function checker_password($value, $format = '')
    {
        return preg_match("/^.{4,}$/i", $value) ? true : false;
    }
    // check string is valid for status ?
    public static function checker_status($value, $format = '')
    {
        return preg_match("/^[\w-_@\.]+$/i", $value) ? true : false;
    }
    // return check type's warn message
    public static function getWarn($type)
    {
        static $message = [
            'length' => '长度错误', 'maxlength' => '过长', 'minlength' => '过短', 'tags' => '格式错误',
            'eq' => '值错误', 'empty' => '值不为空', 'in' => '值错误', 'inkey' => '值错误', 'null' => '值不为空',
            'lt' => '值过小', 'gt' => '值过大', 'let' => '值过小', 'get' => '值过大',
            'number' => '格式错误', 'int' => '格式错误', 'string' => '格式错误', 'float' => '格式错误',
            'bankcard' => '格式错误', 'idcard' => '格式错误', 'mobile' => '格式错误', 'email' => '格式错误',
            'date' => '格式错误', 'url' => '格式错误', 'ip' => '格式错误', 'qq' => '格式错误', 'phone' => '格式错误',
            'chinese' => '格式错误', 'english' => '格式错误',
            'accountid' => '格式错误', 'username' => '格式错误', 'password' => '格式错误', 'status' => '格式错误', 'json' => '格式错误',
            'preg' => '格式错误',
        ];
        if( ! isset($message[$type])) {
            return '格式错误';
        }
        return $message[$type];
    }
}
