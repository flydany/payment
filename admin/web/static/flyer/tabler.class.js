/**
 * table-template js
 * TABLE 模版 公用js函数定义
 */
// @created by flydany
// 2017-04-02 20:00:00

if(typeof(template) === 'undefined') {
    jQuery.getScript('/static/art.template/template.js', function() {
        // 引入模板类
        template.helper("dateShow", dateShow);
        template.helper("dayShow", dayShow);
        template.helper("fmoney", fmoney);
    });
}
// 定义时间转换函数
function dateShow(dateString, format)
{
    if ( ! dateString || dateString == 0) {
        return '--';
    }
    // console.log(dateString);
    if( ! isNaN(dateString)) {
        var date = new Date(dateString * 1000);
        dateString = date.getFullYear();
        dateString += '-' + (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1);
        dateString += '-' + date.getDate();
        // console.log(dateString);
        if(format == 'short' || format == 'day') {
            return dateString;
        }
        dateString += ' ' + date.getHours();
        dateString += ':' + date.getMinutes();
        // console.log(dateString);
        if(format == 'minute') {
            return dateString;
        }
        dateString += ':' + date.getSeconds();
        return dateString;
    }
    else {
        if (format == 'short' || format == 'day') {
            return dateString.substr(0, 10);
        } else if (format == 'minute') {
            return dateString.substr(0, 16);
        } else {
            return dateString;
        }
    }
}
// 定义日期字符串填充
function dayShow(dayString, format)
{
    if ( ! dayString || dayString == 0) {
        return '--';
    }
    var year = dayString.substr(0, 4);
    var month = dayString.substr(4, 2);
    var day = dayString.substr(6, 2);
    if(format == 1) {
        return year + '年' + month + '月' + day + '日';
    }
    else {
        return year + '-' + month + '-' + day;
    }
}
// 定义金额转换函数
function fmoney(s, n)
{
    if (!s) {
        s = 0;
    }
    // 金额单位转换
    s /= 100;
    s = parseFloat((s + "").replace(/[^\d\.-]/g, "")).toFixed(n) + "";
    var l = s.split(".")[0].split("").reverse(),
        r = s.split(".")[1];
    t = "";
    for (i = 0; i < l.length; ++i) {
        t += l[i] + ((i + 1) % 3 == 0 && (i + 1) != l.length ? "," : "");
    }
    return t.split("").reverse().join("") + (n > 0 ? "." + r : '');
}

var tabler = function() {
    // ajax 请求地址
    // er 表示容器
    this.url = '';
    this.method = 'POST';
    this.param = {};
    // 请求参数容器
    this.searcher = {};
    this.searchButton = '';
    this.searchClass = '.tabler';
    this.readyCall = true;
    // 请求页数
    this.page = 1;
    this.pageSize = 20;
    // 数据渲染模板
    this.templater = '';
    // 渲染的table
    this.tabler = '';
    this.selectType = 'mult';
    // 全选、反选按钮
    this.selectButton = '';
    this.reverseButton = '';
    this.showLoading = true;
    // 渲染的pager
    this.pager = '';
    // 数据请求之后数据存储
    this.SuccessCode = '200';
    this.data = {};
    // POST请求数据之前调用的函数
    // format [[function name, param]]
    this.beforePost = [];
    // POST请求数据成功完成后调用的函数
    // format [[function name, param]]
    this.afterPost = [];
    /**
     * @name 添加请求前函数调用
     * @param functionName
     * @return this object
     */
    this.addBeforeAjax = function(functionName) {
        this.beforePost.push(functionName);
        return this;
    }
    // @name 添加请求后函数调用
    this.addAfterAjax = function(functionName) {
        this.afterPost.push(functionName);
        return this;
    }
    /**
     * @name 设置系统配置 url
     * @param url 请求的地址
     * @return this object
     */
    this.setUrl = function(url) {
        this.url = url;
        return this;
    }
    this.setParams = function(param) {
        this.param = param;
    }
    this.addParam = function(name, value) {
        if(name.indexOf('[]') > 0) {
            if(this.param[name] === undefined) {
                this.param[name] = [];
            }
            this.param[name].push(value);
        }
        else {
            this.param[name] = value;
        }
        return this;
    }
    // @name 设置系统配置 searcher 搜索条件容器
    this.setSearcher = function(search) {
        this.searcher = search;
        return this;
    }
    // @name 设置查询按钮
    this.setSearchButton = function(button) {
        this.searchButton = button;
        return this;
    }
    // @name 设置系统配置 page 页数
    this.setPage = function(page) {
        this.page = page;
        return this;
    }
    // @name 设置系统配置 pageSize 页面大小
    this.setPageSize = function(size) {
        this.pageSize = size;
        return this;
    }
    // @name 设置系统配置 table
    this.setTabler = function(table) {
        this.tabler = table;
        return this;
    }
    // @name 设置系统配置 table
    this.setShowLoading = function(show) {
        this.showLoading = show;
        return this;
    }
    // @name 设置系统配置 table
    this.setTemplater = function(template) {
        this.templater = template;
        return this;
    }
    // @name 设置系统配置 table
    this.setPager = function(page) {
        this.pager = page;
        return this;
    }
    // @name 设置表格行点击类型
    this.setSelectType = function(selectType) {
        this.selectType = selectType;
        return this;
    }
    // @name 设置表格行全选按钮
    this.setSelectButton = function(selectButton) {
        this.selectButton = selectButton;
        return this;
    }
    // @name 设置表格行反选按钮
    this.setReverseButton = function(reverseButton) {
        this.reverseButton = reverseButton;
        return this;
    }
    this.setReadyCall = function(readyCall) {
        this.readyCall = !!readyCall;
        return this;
    }
    this.isReadyCall = function() {
        return this.readyCall ? true : false;
    }
    // @name 填充表格
    this.renderTabler = function(_html) {
        if($(this.tabler).find('tbody').length) {
            $(this.tabler).find('tbody').html(_html);
        }
        else {
            $(this.tabler).html(_html);
        }
        return this;
    }
    // @name 显示加载提示描述
    this.showMessage = function(message, icon) {
        if($(this.tabler)[0].tagName == 'TABLE') {
            this.renderTabler('<tr><td colspan="' + $(this.tabler).find('thead th').length + '"><i class="' + icon + ' fa-fw"></i> ' + message + '</td></tr>');
        }
        else {
            this.renderTabler('<i class="' + icon + '"></i> ' + message);
        }
    }
    // @name 显示加载异常信息描述
    this.showError = function(message) {
        this.showMessage(message, 'fa fa-times-circle');
    }
    // @name 表格数据加载中动画、隐藏搜索button
    this.loading = function() {
        if(this.showLoading || (this.showLoading == undefined)) {
            // $(this.searchButton).attr('disabled', true);
            $(this.searchButton).button('loading');
            this.showMessage('searching..', 'fa fa-spin fa-spinner');
        }
    }
    // @name 显示搜索button
    this.loaded = function() {
        // $(this.searchButton).attr('disabled', false);
        $(this.searchButton).button('reset');
    }
    // @name 填充page容器
    this.renderPager = function(_html) {
        $(this.pager).find('.render').html(_html);
        return this;
    }

    this.init = function(param) {
        if(this.isEmpty(param.url)) {
            console.log('error configuration');
        }
        // 初始化配置参数
        this.setUrl(param.url);
        // 设置查询参数、按钮
        this.setSearcher(param.search).setSearchButton(param.button);
        // 设置表格、分页容器
        this.setTabler(param.table).setPager(param.page);
        // 设置模板
        this.setTemplater(param.template);
        this.setShowLoading(param.showLoading);
        // 添加请求前数据处理函数
        if( ! this.isEmpty(param.beforePost)) {
            this.addBeforeAjax(param.beforePost);
        }
        // 添加请求后数据处理函数
        if( ! this.isEmpty(param.afterPost)) {
            this.addAfterAjax(param.afterPost);
        }
        // 初始化page全选、反选按钮
        if((param.selectButton !== undefined) || (param.reverseButton !== undefined)) {
            this.setSelectButton(param.selectButton);
            this.setReverseButton(param.reverseButton);
            this.initSelect();
        }
        // 如果存在查询按钮，点击按钮时查询
        if(this.searchButton) {
            var tablerClass = this;
            $(this.searchButton).bind('click', function() {
                tablerClass.load();
            });
        }
        this.setReadyCall(param.readyCall);
        // 初始完毕后，是否直接发起请求
        if(this.isReadyCall()) {
            this.load();
        }
    }
    // @name JSON 请求加载数据
    this.load = function() {
        // 初始化请求数据
        this.initParams();
        // 请求前函数调用
        this.trigger(this.beforePost);
        // 加载动画效果
        this.loading();
        // 加载数据
        var tablerClass = this;
        $.ajax({
            url: this.buildUrl(),
            type: this.method,
            data: this.param,
            dataType: 'json',
            // ajax请求完毕之后执行(失败成功都会执行)
            complete: function() {

            },
            // 加载信息异常提示
            error: function() {
                tablerClass.loaded();
                jQuery.warning('search program error');
                // layer.msg('search program error', { shift: 6 });
                tablerClass.showError('search program error', 'fa fa-times');
            },
            // 数据加载成功处理方法
            success: function(ret) {
                // 解析返回值
                // var data = (checker_json(ret) === true) ? ret : $.parseJSON(ret);
                var data = ret;
                if(data.code == tablerClass.SuccessCode) {
                    // 渲染模板
                    var _html = template(tablerClass.templater, data);
                    // 填充到table容器
                    if(_html) {
                        tablerClass.renderTabler(_html);
                    }
                    else {
                        tablerClass.showError('nothing has been found', 'fa fa-times-circle');
                    }
                    // 初始化表格相关
                    tablerClass.initTable();
                    // 分页填充到page容器
                    if(data.page) {
                        tablerClass.renderPager(data.page);
                        // 重新初始化分页事件
                        tablerClass.initPageClick();
                    }
                }
                else {
                    jQuery.warning(data.message);
                    // layer.msg(data.message, { shift: 6 });
                    tablerClass.showError(data.message);
                }
                // 请求前函数调用
                tablerClass.trigger(tablerClass.afterPost);
                // 数据加载完毕
                tablerClass.loaded();
            }
        });
    }
    // 获取请求地址
    this.buildUrl = function() {
        var split = '?';
        if(this.url.indexOf('?') >= 0) {
            split = '&';
        }
        return this.url + split + 'page=' + this.page + '&pageSize=' + this.pageSize;
    }
    // 初始化参数
    this.initParams = function() {
        this.setParams({});
        this.addParam('page', this.page);
        this.addParam('pageSize', this.pageSize);
        this.addParam('submit', 'json');
        this.addParam('_csrf', $('meta[name=csrf-token]').attr('content'));
        var tablerClass = this;
        $.each($(this.searcher).find(this.searchClass), function() {
            var name = $(this).attr('name');
            if(tablerClass.isEmpty(name)) {
                return true;
            }
            if(['radio', 'checkbox'].indexOf($(this).attr('type')) >= 0) {
                if( ! $(this).is(':checked')) {
                    return true;
                }
            }
            tablerClass.addParam(name, $(this).val());
        });
        return this;
    }
    // 执行钩子函数
    this.trigger = function(functions, isBreak) {
        if(this.isEmpty(functions)) {
            return true;
        }
        var functionLength = functions.length;
        for(var i = 0; i < functionLength; ++i) {
            if(this.callUserFunction(functions[i], this) !== true && isBreak) {
                return false;
            }
        }
        return true;
    }
    this.initPageClick = function() {
        var tablerClass = this;
        $(this.pager).find('.render a').each(function() {
            // $(this).attr('data-href', $(this).attr('href'));
            $(this).attr('href', 'javascript:;');
            $(this).bind('click', function() {
                tablerClass.setPage(parseInt($(this).attr('data-page')) + 1);
                tablerClass.load();
            });
        });
    }
    // 加载 art-template 插件
    this.loadTemplate = function() {
        if(typeof(template) === undefined) {
            $.getScript('static/art-template/template.js');
        }
        return true;
    }
    // 执行用户自定义函数
    this.callUserFunction = function(functionName, param) {
        if (functionName) {
            if (typeof(functionName) == 'function') {
                return functionName(param);
            } else if (typeof(functionName) == 'string') {
                return eval(functionName);
            }
        }
        return true;
    }
    // 选中、反选事件
    this.initSelect = function() {
        var tablerClass = this;
        // 设置全选按钮点击事件
        if( ! this.isEmpty(this.selectButton)) {
            $(this.selectButton).bind('click', function() {
                // $(tablerClass.tabler).find('input[type=checkbox].list:not(:checked)').parents('tr').click();
                $(tablerClass.tabler).find('tr').not('.select').click();
            });
        }
        // 设置反选按钮点击事件
        if( ! this.isEmpty(this.reverseButton)) {
            $(this.reverseButton).bind('click', function() {
                $.each($(tablerClass.tabler).find('input[type=checkbox].list'), function() {
                    $(this).parents('tr').click();
                });
            });
        }
        return this;
    }
    // 表格行点击事件
    this.initTable = function() {
        var tablerClass = this;
        // 设置表格行
        var tableLine = $(this.tabler).find('tbody tr').length ? $(this.tabler).find('tbody tr') : $(this.tabler);
        // 初始化表格选中类型
        var input = (this.selectType == 'single') ? 'radio' : 'checkbox';
        $(tableLine).bind('click', function() {
            var checkbox = $(this).find('input[type=' + input + '].list');
            if ( ! $(checkbox).length) {
                return;
            }
            // if ($(checkbox).is(':checked')) {
            if ($(this).hasClass('select')) {
                if (tablerClass.selectType == 'single') {
                    return;
                }
                $(this).removeClass('select');
                $(checkbox).removeAttr('checked');
            }
            else {
                if (tablerClass.selectType == 'single') {
                    $(this).siblings('.select').removeClass('select').find('input[type=' + input + '].list:checked').removeAttr('checked');
                }
                $(this).addClass('select');
                $(checkbox).attr('checked', 'checked');
            }
        });
        // 设置表格行背景
        $(this.tabler).find('tr').removeClass('even');
        $(this.tabler).find('tr:odd').addClass('even');
    }
    this.isEmpty = function(value, format) {
        if(value == null || value == '' || value == [] || value == [''] || value == [""] || value == {}) {
            return true;
        }
        return false;
    }
}