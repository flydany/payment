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
use yii\data\Pagination;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\LinkPager;

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
     * 生成分页类
     * @param integer $totalCount 数据总数
     * @param array $options 配置选项
     * @return Pagination
     */
    public static function pagination($totalCount, $options = [])
    {
        $options = array_merge([
            'totalCount' => $totalCount,
            'pageSizeParam' => false,
            'pageSize' => Yii::$app->request->getQueryParam('pageSize') ?? 20,
        ], $options);
        return new Pagination($options);
    }
    /**
     * 创建分页标签
     * @param Pagination $pagination
     * @param array $options
     * @return string
     * @throws \Exception
     */
    public static function pager($pagination, $options = [])
    {
        $options = array_merge([
            'pagination' => $pagination,
            'hideOnSinglePage' => false,
            'firstPageLabel' => '<i class="fa fa-angle-double-left fa-fw"></i>',
            'lastPageLabel' => '<i class="fa fa-angle-double-right fa-fw"></i>',
            'prevPageLabel' => '<i class="fa fa-angle-left fa-fw"></i>',
            'nextPageLabel' => '<i class="fa fa-angle-right fa-fw"></i>',
        ], $options);
        return LinkPager::widget($options);
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
    public static function select($name, $lists, $select = '', $options = [])
    {
        $options['class'] = 'form-control'.(isset($options['class']) ? " {$options['class']}" : '');
        return Html::dropDownList($name, $select, $lists, $options);
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
