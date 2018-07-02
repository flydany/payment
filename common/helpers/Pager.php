<?php

namespace common\helpers;

/**
 * ------------------------------------------------------------------
 * @name 分页类
 * ------------------------------------------------------------------
 * Created by Komodo.
 * User: flydany
 * Date: 201515/7/22
 * Time: 14:28
 * ------------------------------------------------------------------
 */

class Pager {
    
    // @name 单例运行数据检查操作
    public static $instance;
    public static function getInstance()
    {
        if( ! is_object(self::$instance)) {
            self::$instance = new Pager();
        }
        return self::$instance;
    }

    // @param $total_count int 数据总条数
    private $total_count = 0;

    // @param $total_page int 数据总页数
    private $total_page = 1;

    // @param $page int 当前页数
    private $page = 1;

    // @param $page_count int 每页展示的条数
    private $page_count = 20;

    // @param $url string 请求的页面地址
    private $url = '';
    
    // @name 分页HTML类型
    const PageTypeNormal = 1;
    const PageTypeH5 = 2;
    public $pageType = self::PageTypeNormal;

    // @name 设置是否需要返回分页HTML
    public function setPageType($pageType = self::PageTypeNormal)
    {
        $this->pageType = $pageType;
        return $this;
    }
    
    /**
     * ------------------------------------------------------------------
     * @name css 样式设置
     * ------------------------------------------------------------------
     * @param $normal_class string 默认当前页样式
     * @param $disabled_class string 不可用时样式
     * @param $current_class string 不可用时样式
     * @param $prev_class string 上一页样式
     * @param $next_class string 下一页样式
     * @param $prev_string string 上一页HTML填充内容
     * @param $next_string string 下一页HTML填充内容
     * ------------------------------------------------------------------
     */
    private $normal_class = '';
    private $disabled_class = 'active';
    private $current_class = 'active';
    private $prev_class = 'prev';
    private $next_class = 'next';
    private $prev_string = "<i class='icon-double-angle-left'></i> Prev";
    private $next_string = "Next <i class='icon-double-angle-right'></i>";

    // 设置参数
    public function setParam($param)
    {
        $this->param = $param;
        return $this;
    }
    
    /* ------------------------------------------------------------------
     * @name 主调函数
     * array(
     *     'url' => '',  组合的网址
     *     'page_count' => 10,  每页条数
     *     'total_count' => 0,   总条数
     *     'page' => 1,    当前页
     *     'param' => array('key' => 'val', 'k' => 'v')))   url 组合参数
     * ------------------------------------------------------------------ */
    public static function page($array = array())
    {
        $page = self::getInstance();
        $page->setParam($array)->initUrl()->initPage();

        $return['total_count'] = $page->total_count;
        $return['total_page'] = $page->total_page;
        $return['page'] = $page->page;
        $return['page_count'] = $page->page_count;
        $return['current_count'] = $page->current_count;
        if(isset($array['page_type'])) {
            $page->setPageType($array['page_type']);
        }
        $return['string'] = $page->escapePage();

        return $return;
    }
    public static function offset()
    {
        $page = self::getInstance();

        return ($page->page - 1) * $page->page_count;
    }
    public static function limit()
    {
        $page = self::getInstance();

        return $page->page_count;
    }

    // 初始化 跳转地址
    private function initUrl()
    {
        $this->url = '';
        return $this;
        if( ! isset($this->param['url']) || empty($this->param['url'])) {
            $this->url = '';
        }
        else {
            $this->url = $this->param['url'];
        }

        if(empty($this->param['param']) || ! is_array($this->param['param'])) {
            return $this;
        }

        $uri = array();
        foreach($this->param['param'] as $key => $val) {
            if(null === $val || is_array($val)) {
                continue;
            }
            $uri[] = $key .'='. $val;
        }
        if(count($uri) > 0) {
            $this->url .= (strpos($this->url, '?') === false ? '?' : '&') . implode('&', $uri);
        }

        return $this;
    }

    // 初始化 每页 数量/总数量
    private function initPage()
    {
        // 每页数量
        if(isset($this->param['page_count']) && $this->param['page_count']) {
            $this->page_count = $this->param['page_count'];
        }
        else if( ! $this->page_count) {
            $this->page_count = 10;
        }

        // 总数量
        if(isset($this->param['total_count'])) {
            $this->total_count = $this->param['total_count'];
        }
        else {
            $this->total_count = 0;
        }

        // 计算总页数
        if(empty($this->total_count) || empty($this->page_count)) {
            $this->total_page = 1;
        }
        $this->total_page = ceil($this->total_count / $this->page_count);
        if($this->total_page < 1) {
            $this->total_page = 1;
        }

        // 当前页
        if(isset($this->param['page']) && $this->param['page']) {
            $this->page = $this->param['page'];
        }
        else {
            $this->page = \Yii::$app->request->get('page') ? \Yii::$app->request->get('page') : \Yii::$app->request->post('page');
        }
        if($this->page < 1) {
            $this->page = 1;
        }
        $this->current_count = $this->page_count;
        if($this->page >= $this->total_page) {
            $this->page = $this->total_page;
            $this->current_count =$this->total_count - ($this->page - 1) * $this->page_count;
        }
    }

    // 组合 分页 HTML
    private function escapePage()
    {
        // 添加分页参数
        $this->url .= (strpos($this->url, '?') === false ? '?' : '&') .'page=';

        // 开始组合
        $string[] = '<ul>';

        switch($this->pageType) {
            case static::PageTypeH5: {
                $string = array_merge($string, $this->escapeH5Page());
            } break;
            default: {
                $string = array_merge($string, $this->escapeNormalPage());
            }
        }

        $string[] = '</ul>';

        // 组合 返回字符串
        return implode('', $string);
    }

    // @name 组织适合H5页面的HTML
    private function escapeH5Page()
    {
        $string = [];
        // 定义 上一页 第一页
        if($this->page <= 1) {
            $string[] = $this->replacePage(array($this->prev_class .' '. $this->disabled_class, $this->prev_string));
        }
        else {
            $string[] = $this->replacePage(array($this->prev_class .' '. $this->normal_class, $this->page - 1, $this->prev_string));
        }
        // 定义 最后一页 下一页
        if($this->page >= $this->total_page) {
            $string[] = $this->replacePage(array($this->next_class .' '. $this->disabled_class, $this->next_string));
        }
        else {
            $string[] = $this->replacePage(array($this->next_class .' '. $this->normal_class, $this->page + 1, $this->next_string));
        }
        return $string;
    }

    // @name 正常方式组织分页HTML
    private function escapeNormalPage()
    {
        $string = [];
        // 定义 上一页 第一页
        if($this->page <= 1) {
            $string[] = $this->replacePage(array($this->prev_class .' '. $this->disabled_class, $this->prev_string));
            $string[] = $this->replacePage(array($this->current_class, 1));
        }
        else {
            $string[] = $this->replacePage(array($this->prev_class .' '. $this->normal_class, $this->page - 1, $this->prev_string));
            $string[] = $this->replacePage(array($this->normal_class, 1, 1));
        }

        if($this->page - 5 > 2) {
            $i = $this->page - 5;
            // 定义 前部省略部分分页
            $string[] = $this->replacePage(array($this->disabled_class, '..'));
        }
        else {
            $i = 2;
        }
        // 定义 中间部分分页
        for($i; ($i <= $this->page + 5) && ($i < $this->total_page); ++$i) {
            if((($i <= $this->page) && (($i + 5) >= $this->page)) || (($i >= $this->page) && (($i - 5) <= $this->page))) {
                if($i == $this->page) {
                    $string[] = $this->replacePage(array($this->current_class, $i));
                }
                else {
                    $string[] = $this->replacePage(array($this->normal_class, $i, $i));
                }
                continue;
            }
        }
        // 定义 后部省略部分分页
        if($i < $this->total_page) {
            $string[] = $this->replacePage(array($this->disabled_class, '..'));
        }
        // 定义 最后一页 下一页
        if($this->page >= $this->total_page) {
            if($this->total_page != 1) {
                $string[] = $this->replacePage(array($this->current_class, $this->total_page));
            }
            $string[] = $this->replacePage(array($this->next_class .' '. $this->disabled_class, $this->next_string));
        }
        else {
            $string[] = $this->replacePage(array($this->normal_class, $this->total_page, $this->total_page));
            $string[] = $this->replacePage(array($this->next_class .' '. $this->normal_class, $this->page + 1, $this->next_string));
        }
        return $string;
    }

    // 转义 组合一个分页数字
    public function replacePage($array)
    {
        // 定义分页小项的字符串类型
        $unlink_string = "<li class='{class}'><a>{show}</a></li>";
        $link_string = "<li class='{class}'><a class='pager' data-page='{page}' href='javascript:;' data-href='{$this->url}{page}'>{show}</a></li>";

        if(count($array) == 2) {
            return str_replace(array('{class}', '{show}'), $array, $unlink_string);
        }
        else {
            return str_replace(array('{class}', '{page}', '{show}'), $array, $link_string);
        }
    }
}
