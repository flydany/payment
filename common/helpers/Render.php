<?php

/**
 * 工具方法
 * -----------------------------------
 * Created by Komodo.
 * User: flydany
 * Date: 2017/6/18
 * Time: 09:07
 */

namespace common\helpers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

class Render {

    /**
     * 组织静态资源请求网络地址
     * @param $url string 静态资源路径
     * @return string
     */
    public static function static($url)
    {
        if(empty($url)) {
            return '';
        }
        return Url::to('@web/static/'.$url);
    }

    /**
     * 获取源数组中的数据
     * @param $data array | object 源数据
     * @param $key string 提取key
     * @param $default string | array 默认值
     * @return string
     */
    public static function value($data, $key, $default = '')
    {
        if(empty($data)) {
            return $default;
        }
        $keyArray = explode('.', $key);
        if( ! is_array($keyArray)) {
            $keyArray = [$keyArray];
        }
        $length = count($keyArray);
        for($i = 0; $i < $length; ++$i) {
            if(isset($data[$keyArray[$i]])) {
                if($i == $length - 1) {
                    $value = $data[$keyArray[$i]];
                }
                else {
                    $value = static::value($data[$keyArray[$i]], $keyArray[$i + 1], $default);
                }
                if($value || strlen($value) > 0) {
                    return $value;
                }
            }
            else {
                break;
            }
        }
        return $default;
    }

    /**
     * 显示分类的名称
     * @param $selectors array 分类数组
     * @param $key string 数据编号
     * @param $thin string 追加身材
     * @return string
     */
    public static function show($selectors, $key, $thin = 'thin')
    {
        $keys = explode(',', $key);
        if(empty($keys)) {
            return '--';
        }
        $_html = [];
        foreach($keys as $key) {
            if(isset($selectors[$key])) {
                if(is_array($selectors[$key])) {
                    $status = $selectors[$key]['status'];
                    $title = $selectors[$key]['title'];
                }
                else {
                    $title = $selectors[$key];
                    $status = 'blue';
                }
            }
            else {
                $status = 'red';
                $title = 'UNKNOW';
            }
            $_html[] = "<span class=\"flyer-status {$status} {$thin} mr-5px\">{$title}</span>";
        }
        return implode('', $_html);
    }

    /**
     * 显示分类的名称
     * @param $selectors array 分类数组
     * @param $key string 数据编号
     * @param $thin string 高度
     * @param $color string 颜色
     * @return string
     */
    public static function categories($type, $key, $thin = 'thin', $color = 'red')
    {
        $keys = array_filter(explode(',', $key));
        if(empty($keys)) {
            return '--';
        }
        $categories = static::initCategories($type);
        $_html = [];
        foreach($keys as $key) {
            $title = static::categoriesLoop($categories['selectors'], $categories['relates'], $key);
            
            $_html[] = "<span class=\"flyer-status {$color} {$thin} mr-5px\">{$title}</span>";
        }
        return implode('', $_html);
    }
    // 从JS文件中获取数据
    public static function initCategories($type)
    {
        switch($type) {
            case 'designCategories': {
                $path = 'category-design.data.js';
                $key = 'DesignCategories';
            } break;
            default: {
                return '--';
            }
        }
        $fileContent = file_get_contents(Yii::getAlias('@static/system/'.$path));
        $start = strlen("var {$key} = ");
        $categories['selectors'] = json_decode(substr($fileContent, $start, strpos($fileContent, ';var '.$key.'Relation') - $start), true);
        $relateKey = ';var '.$key.'Relation = ';
        $categories['relates'] = json_decode(substr($fileContent, strpos($fileContent, $relateKey) + strlen($relateKey), -1), true);
        return $categories;
    }
    // 递归路径关系
    public static function categoriesLoop($selectors, $relates, $id)
    {
        if( ! isset($relates[$id])) {
            return '--';
        }
        if(isset($relates[$relates[$id]])) {
            return static::categoriesLoop($selectors, $relates, $relates[$id]) + ' <i class="icon-double-angle-right"></i> ' + $selectors[$relates[$id]][$id];
        }
        else {
            return $selectors[$relates[$id]][$id];
        }
    }

    /**
     * 显示TAGS
     * @param $tags array 数据串
     * @param $thin string 高度
     * @param $color string 颜色
     * @return string
     */
    public static function tag($tag, $thin = 'thin', $color = 'green')
    {
        $tags = array_filter(explode(';', $tag));
        if(empty($tags)) {
            return '--';
        }
        $_html = [];
        foreach($tags as $tag) {
            $_html[] = "<span class=\"flyer-status {$color} {$thin} mr-5px\">{$tag}</span>";
        }
        return implode('', $_html);
    }

    /**
     * 显示分类的名称
     * @param $selectors array 分类数组
     * @param $key string 数据编号
     * @param $color boolean 是否追加css
     * @return string
     */
    public static function text($selectors, $keys, $color = true)
    {
        if( ! is_array($keys)) {
            $keys = explode(',', $keys);
        }
        if(empty($keys)) {
            return '---';
        }
        $_html = [];
        foreach($keys as $key) {
            if(isset($selectors[$key])) {
                if(is_array($selectors[$key])) {
                    $status = $selectors[$key]['status'];
                    $title = $selectors[$key]['title'];
                }
                else {
                    $title = $selectors[$key];
                    $status = 'blue';
                }
            }
            else {
                $status = 'red';
                $title = 'UNKNOW';
            }
            $_html[] = $color ? "<span class=\"cl-{$status} mr-5px\">{$title}</span>" : "<span class=\"mr-5px\">{$title}</span>";
        }
        return implode('，', $_html);
    }

    /**
     * 裁剪html标签内的字符串
     * @param $string string 字符串
     * @length int 截取的字符串长度
     * @return string
     */
    public static function interceptHtml($string, $length = 160, $tail = '...')
    {
        $string = preg_replace("/<\/?[^>]+>/", '', $string);
        return mb_substr($string, 0, $length).$tail;
    }
    
    /**
     * 组装select元素
     * @param $name string 名称
     * @param $list array option数组
     * @param $select string 选中元素
     * @param $option array 属性
     * @param $configurate array 配置信息
     * @return string
     */
    public static function select($name, $lists, $select = '', $options = [], $configurate = [])
    {
        $_status = 'status';
        $_title = 'title';
        $_value = 'value';
        if( ! empty($configurate)) {
            foreach(['status', 'title', 'value'] as $k) {
                if( ! isset($configurate[$k])) {
                    continue;
                }
                $sk = '_'.$k;
                $$sk = $configurate[$k];
            }
        }
        $_html = '';
        $_html .= "<select class='form-control' name=\"{$name}\":option>";
        $_option = '';
        if($options) {
            foreach($options as $attr => $option) {
                if(in_array($attr, ['prompt'])) {
                    continue;
                }
                $_option .= " {$attr}=\"{$option}\"";
            }
        }
        $_html = str_replace(':option', $_option, $_html);
        if(isset($options['prompt'])) {
            $_html .= "<option value=\"\">{$options['prompt']}</option>";
        }
        if($lists) {
            foreach($lists as $value => $list) {
                $status = '';
                $selected = '';
                if(is_array($list)) {
                    isset($list[$_status]) && ($status = " data-status=\"{$list[$_status]}\"");
                    $title = $list[$_title];
                    $value = isset($list[$_value]) ? $list[$_value] : $value;
                }
                else {
                    $title = $list;
                }
                if(strlen($select) && ($select == $value)) {
                    $selected = ' selected';
                }
                $_html .= "<option value=\"{$value}\"{$status}{$selected}>{$title}</option>";
            }
        }
        $_html .= '</select>';
        return $_html;
    }

    /**
     * 组装radio元素
     * @param $name string 名称
     * @param $list array option数组
     * @param $select string 选中元素
     * @param $options array 属性信息
     * @param $configurate array 配置信息
     * @return string
     */
    public static function radio($name, $lists, $select = '', $options = [], $configurate = [])
    {
        $_html = '';
        $_status = 'status';
        $_title = 'title';
        $_value = 'value';
        if( ! empty($configurate)) {
            foreach(['status', 'title', 'value'] as $k) {
                if( ! isset($configurate[$k])) {
                    continue;
                }
                $sk = '_'.$k;
                $$sk = $configurate[$k];
            }
        }
        $_option = '';
        if($options) {
            foreach($options as $attr => $option) {
                if(in_array($attr, ['prompt'])) {
                    continue;
                }
                $_option .= " {$attr}=\"{$option}\"";
            }
        }
        if(isset($options['prompt'])) {
            $_html .= "<input name=\"{$name}\" value=\"\" checked type=\"radio\" flyer=\"radio\" title=\"{$options['prompt']}\"{$_option}>";
        }
        foreach($lists as $value => $list) {
            $status = '';
            $selected = '';
            if(is_array($list)) {
                isset($list[$_status]) && ($status = " data-status=\"{$list[$_status]}\"");
                $title = $list[$_title];
                $value = isset($list[$_value]) ? $list[$_value] : $value;
            }
            else {
                $title = $list;
            }
            if(strlen($select) && ($select == $value)) {
                $selected = ' checked';
            }
            $_html .= "<input name=\"{$name}\" value=\"{$value}\"{$status}{$selected} type=\"radio\" flyer=\"radio\" title=\"{$title}\"{$_option}>";
        }
        return $_html;
    }
    
    /**
     * 组装checkbox元素
     * @param $name string 名称
     * @param $list array option数组
     * @param $select string 选中元素
     * @param $options array 属性信息
     * @param $configurate array 配置信息
     * @return string
     */
    public static function checkbox($name, $lists, $select = '', $options = [], $configurate = [])
    {
        if( ! is_array($select)) {
            $select = array_filter(explode(',', $select));
        }
        $_status = 'status';
        $_title = 'title';
        $_value = 'value';
        if( ! empty($configurate)) {
            foreach(['status', 'title', 'value'] as $k) {
                if( ! isset($configurate[$k])) {
                    continue;
                }
                $sk = '_'.$k;
                $$sk = $configurate[$k];
            }
        }
        $_option = '';
        if($options) {
            foreach($options as $attr => $option) {
                if(in_array($attr, ['prompt'])) {
                    continue;
                }
                $_option .= " {$attr}=\"{$option}\"";
            }
        }
        $_html = '';
        if(isset($options['prompt'])) {
            $_html .= "<input name=\"{$name}\" value=\"\" checked type=\"checkbox\" flyer=\"radio\" title=\"{$options['prompt']}\"{$_option}>";
        }
        foreach($lists as $value => $list) {
            $status = '';
            $selected = '';
            if(is_array($list)) {
                isset($list[$_status]) && ($status = " data-status=\"{$list[$_status]}\"");
                $title = $list[$_title];
                $value = isset($list[$_value]) ? $list[$_value] : $value;
            }
            else {
                $title = $list;
            }
            if($select && in_array($value, $select)) {
                $selected = ' checked';
            }
            $_html .= "<input name=\"{$name}[]\" value=\"{$value}\"{$status}{$selected} type=\"checkbox\" flyer=\"checkbox\" title=\"{$title}\"{$_option}>";
        }
        return $_html;
    }

    /**
     * 金额显示
     * @param $amount int 金额（单位：分）
     * @param $decimal int 小数点位数
     * @return string
     */
    public static function amount($amount, $decimal = 2)
    {
        return number_format((int)$amount / 100, $decimal);
    }

    /**
     * 数量显示
     * @param $amount int 数量
     * @param $decimal int 小数点位数
     * @return string
     */
    public static function number($number, $decimal = 0)
    {
        return number_format((int)$number, $decimal);
    }
}
