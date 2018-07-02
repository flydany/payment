<?php

/**
 * @name 工具方法
 * ---------------------------------
 * Created by Komodo.
 * User: flydany
 * Date: 2017/6/18
 * Time: 09:07
 */

namespace website\helpers;

use Yii;
use yii\helpers\Url;
use common\models\UserEvaluate;

class Render extends \common\helpers\Render {
    
    /**
     * 截取字符串
     * @param $string string 需要截取的字符串
     * @param $type string 截取类型
     * @return string
     */
    public static function substring($string, $type = 'mobile')
    {
        if(empty($string)) {
            return '';
        }
        switch ($type) {
            case 'mobile': {
                return substr($string, 0, 3).'****'.substr($string, -4);
            } break;
            case 'realname': {
                return mb_substr($string, 0, 1).str_pad('*', mb_strlen($string) - 1);
            } break;
        }
        return $string;
    }
    
    /**
     * 渲染用户星级
     * @param $star float 星级
     * @return string
     */
    public static function star($star = UserEvaluate::StartLimit)
    {
        $_html = '';
        for($i = 1; $i <= $star; ++$i) {
            $_html .= '<i class="icon-star"></i> ';
        }
        if((float)$star > (int)$star) {
            $_html .= '<i class="icon-star-half"></i>';
        }
        return $_html;
    }
    
    /**
     * @name 组织首页模型渲染HTML
     * @param $patternList array 模型列表
     * @return string
     */
    public static function renderIndexPattern($patternList)
    {
        $_html = '';
        foreach($patternList as $key => $pattern) {
            $image = static::upload($pattern['image']);
            $href = Url::to('@web/pattern/pattern-detail?id='.$pattern['id']);
            $_html .= <<<INDEXPATTERN
    <div class="pattern">
        <div class="imager"><a href="{$href}"><img src="{$image}"></a></div>
        <div class="describer">
            <p class="pattern-title"><a href="{$href}">{$pattern['title']}</a></p>
            <p class="pattern-detail">
                <span class="pattern-download"><i class="icon-download"></i> {$pattern['download']}</span>
                <span class="pattern-view"><i class="icon-eye-open"></i> {$pattern['view']}</span>
                <span class="pattern-favorite"><i class="icon-star"></i> {$pattern['view']}</span>
            </p>
        </div>
    </div>
INDEXPATTERN;
        }
        return $_html;
    }

    /**
     * @name 组织首页模型渲染HTML
     * @param $patternList array 模型列表
     * @return string
     */
    public static function renderIndexArticle($articleList)
    {
        $_html = '';
        foreach ($articleList as $key => $article) {
            $image = static::upload($article['image']);
            $href = Url::to('@web/article/article-detail?id=' . $article['id']);
            $content = static::interceptHtml($article['content']);
            $isFirst = $key == 0 ? 'first' : '';
            $view = static::number($article['view']);
            $createAt = date('Y-m-d H:i', $article['created_at']);
            $_html .= <<<INDEXPATTERN
    <div class="article {$isFirst}">
        <a href="{$href}"><img class="imager" src="{$image}"></a>
        <p class="article-title"><a href="$href">{$article['title']}</a></p>
        <p class="article-information"><i class="icon-time"></i> {$createAt} <i class="icon-eye-open ml-20px"></i> {$view}</p>
        <p class="article-detail">{$content}</p>
        <p class="article-more"><a href="{$href}">阅读详细&gt;&gt;</a></p>
    </div>
INDEXPATTERN;
        }
        return $_html;
    }
    
    /**
     * 根据生日计算年龄
     * @return integer
     */
    public static function age($birthday)
    {
        $age = strtotime($birthday);
        if($age === false) {
            return '--';
        }
        list($y1, $m1, $d1) = explode("-", date("Y-m-d", $age));
        $now = strtotime("now");
        list($y2, $m2, $d2) = explode("-", date("Y-m-d", $now));
        $age = $y2 - $y1;
        if((int)($m2.$d2) < (int)($m1.$d1)) {
            $age -= 1;
        }
        return $age;
    }
}
