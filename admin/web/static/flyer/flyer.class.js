// create by flydany
// 2017-04-04 21:23:00

// 元素美化插件
var flyer = function() {
    // 参数设置
    this.selectClick = true;
    // 初始的DOM数量，用于避免name相同的DOM生成的对象ID相同
    this.index = 0;
    this.renderAttr = 'flyer';
    // 各元素class
    this.selectClass = 'flyer-select';
    this.checkboxClass = 'flyer-choise';
    this.radioClass = 'flyer-choise';
    this.switchClass = 'flyer-switch';
    this.dateClass = 'flyer-date';
    this.inputClass = 'flyer-input';
    // 需需要更改DOM结构的class
    this.tabClass = 'flyer-tabs';
    this.tabHeaderClass = '.header';
    this.tabContenerClass = '.contenter';
    this.activeClass = 'active';
    // 系统参数配置
    this.former = '';

    // @name 设置former容器
    this.setFormer = function(form) {
        this.former = form;
        return this;
    }
    this.getIndex = function() {
        return this.index++;
    }

    // @name Dom美化
    this.init = function(param) {
        if(param.form === undefined) {
            console.log('参数配置异常');
        }
        this.setFormer(param.form);
        // 美化下拉选择框
        this.renderSelects();
        // 美化复选框
        this.renderCheckboxs();
        // 美化单选框
        this.renderRadios();
        // 美化开关
        this.renderSwtichs();
        // 美化日期选择框
        this.renderDates();
        // 绑定多选标签页时间
        this.bindTabs();
    }
    // @name 美化下拉选择框
    this.renderSelects = function() {
        var flyerClass = this;
        $.each($(this.former).find('select[flyer=select]'), function() {
            flyerClass.renderSelect(this);
        });
        // @name 点击其余不相关DOM时关闭菜单
        $('body').bind('click', function() {
            if(flyerClass.selectClick) {
                flyerClass.selectClick = false;
            }
            else {
                $('.' + flyerClass.selectClass + '.active').removeClass('active');
            }
        });
        return this;
    }
    this.renderSelect = function (dom) {
        // var self = this;
        if(typeof(dom) == 'string') {
            dom = $(dom);
        }
        if( ! $(dom).length) {
            return;
        }
        var flyerClass = this;
        // 初始化 select 显示
        $.each($(dom), function(index) {
            // console.log('start init select: ' + $(this).attr('id'));
            // 保留 this
            var self = this;
            // 判断当前 select 是否重新生成
            var is_remove = false;
            // 隐藏当前select
            $(this).hide();
            // select DOM 初始化
            var selecter = $(this).attr('data-selecter');
            if( ! selecter) {
                selecter = '#select-' + $(this).attr('name').replace('[]', '') + '-' + flyerClass.getIndex();
                $(this).attr('data-selecter', selecter);
            }
            var _html = '';
            _html += '<div class="' + flyerClass.selectClass + '" id="' + selecter.replace('#', '') + '">';
            _html += '  <input class="' + flyerClass.inputClass + ' readonly" value="' + $(this).find('option:selected').text() + '" readonly><i class="drop icon-caret-down"></i>';
            _html += '  <ul class="transition">';
            $.each($(this).find('option'), function() {
                // 循环 select > option
                var _css = 'val-' + $(this).val();
                if($(this).is(':selected')) {
                    _css += ' select';
                }
                _html += '      <li class="' + _css + '" data-value="' + $(this).val() + '">' + $(this).text() + '</li>';
            });
            _html += '  </ul>';
            _html += '</div>';
            // console.log('is_remove: ' + is_remove);
            if($(selecter).length) {
                is_remove = true;
                $(selecter).remove();
            }
            // console.log('is_remove: ' + is_remove);
            $(this).after(_html);

            // 添加事件
            // input, .drop 点击事件
            $(selecter).find('input, .drop').bind('click', function() {
                // 隐藏其他下拉
                $('.' + flyerClass.selectClass + '.active').not(selecter).removeClass('active');
                if($(selecter).hasClass('active')) {
                    // 隐藏
                    $(selecter).removeClass('active');
                }
                else {
                    flyerClass.selectClick = true;
                    // 显示下拉
                    $(selecter).addClass('active');
                }
            });
            // Li 点击事件
            $(selecter).find('li').bind('click', function() {
                // console.log('li click: ' + $(this).html());
                // 为了解决程序自行调用 直接返回，屏蔽代码段
                // 如果当前元素是选中状态，退出操作
                // if($(this).hasClass('select')) {
                // $(selecter).removeClass('active');
                // return;
                // }
                // style
                $(this).siblings('.select').removeClass('select');
                $(this).addClass('select');
                // 展示
                $(selecter).find('input').val($(this).text());
                $(selecter).removeClass('active');
                // 赋值
                $(self).val($(this).attr('data-value'));
                $(self).change().blur();
                // select > option 内容改变时事件
                if($(self).attr('data-child')) {
                    // console.log('start child reinit: ' + $(self).attr('data-child'));
                    flyerClass.renderSelect($(self).attr('data-child'));
                }
            });
            if(is_remove) {
                // console.log('模拟click: ' + $(selecter).find('li.select').html());
                $(selecter).find('li.select').click();
            }
            /***
             */
            $(this).bind('change', function() {
                if($(this).find('option:selected').length) {
                    $(selecter).find('input').val($(this).find('option:selected').text());
                }
                else {
                    $(selecter).find('input').val($(this).find('option:first').text());
                }
                $(selecter).find('li').removeClass('select');
                $(selecter).find('.val-' + $(this).val()).addClass('select');
            });
        });
        return this;
    }
    // @name 渲染复选框元素
    this.renderCheckboxs = function() {
        var flyerClass = this;
        $.each($(this.former).find('input[flyer=checkbox]'), function() {
            flyerClass.renderCheckbox(this);
        });
        return this;
    }
    this.renderCheckbox = function(dom) {
        // var self = this;
        if(typeof(dom) == 'string') {
            dom = $(dom);
        }
        if( ! $(dom).length) {
            return;
        }
        var flyerClass = this;
        $.each($(dom), function() {
            // 放入容器 div.flyer-checkbox
            var _title = $(this).attr('title'),
                _css = flyerClass.checkboxClass + ($(this).attr('class') !== undefined ? ' ' + $(this).attr('class') : '');

            flyerClass.wrapChoise(this, _css, _title);
        });
        return this;
    }
    // @name 渲染复选框元素
    this.renderSwtichs = function() {
        var flyerClass = this;
        $.each($(this.former).find('input[flyer=switch]'), function() {
            flyerClass.renderSwitch(this);
        });
        return this;
    }
    this.renderSwitch = function(dom) {
        // var self = this;
        if(typeof(dom) == 'string') {
            dom = $(dom);
        }
        if( ! $(dom).length) {
            return;
        }
        var flyerClass = this;
        $.each($(dom), function() {
            // 放入容器 div.flyer-checkbox
            var _title = '', // $(this).attr('title'),
                _css = flyerClass.switchClass + ($(this).attr('class') ? ' ' + $(this).attr('class') : '');

            flyerClass.wrapChoise(this, _css, _title);
        });
        return this;
    }
    // @name 渲染单选框元素
    this.renderRadios = function() {
        var flyerClass = this;
        $.each($(this.former).find('input[flyer=radio]'), function() {
            flyerClass.renderRadio(this);
        });
        return this;
    }
    this.renderRadio = function(dom) {
        // var self = this;
        if(typeof(dom) == 'string') {
            dom = $(dom);
        }
        if( ! $(dom).length) {
            return;
        }
        var flyerClass = this;
        $.each($(dom), function() {
            var _title = $(this).attr('title'),
                _css = flyerClass.radioClass;
                //_css = flyerClass.radioClass + ($(this).attr('class') ? ' ' + $(this).attr('class') : '');

            flyerClass.wrapChoise(this, _css, _title)
        });
        return this;
    }
    /**
     * @name 渲染checkbox / radio
     * @param dom object 元素节点
     * @param _css string 类名称
     * @param _title string 描述
     */
    this.wrapChoise = function(dom, _css, _title) {
        // 置入外部 div 对象
        $(dom).wrap('<div class="' + _css + '"></div>');
        // 给 box 赋ID值
        var _id = $(dom).attr('id');
        if( ! _id) {
            _id = 'choise-' + ($(dom).attr('name') ? $(dom).attr('name').replace('[]', '') : 'switch') + '-' + this.getIndex();
            $(dom).attr('id', _id);
        }
        // 加入 label 展示元素
        $(dom).after('<label class="transition" for="' + _id + '">' + _title + '</label>');
        return this;
    }
    /**
     * @name 渲染日期插件
     * @returns {flyer}
     */
    this.renderDates = function() {
        var flyerClass = this;
        $.each($(this.former).find('input[flyer=date]'), function() {
            flyerClass.renderDate(this);
        });
        return this;
    }
    this.renderDate = function(dom) {
        var flyerClass = this;
        $.each($(dom), function() {
            $(this).addClass(flyerClass.dateClass);
        });

        return this;
    }
    /**
     * @name 渲染选项卡 header 点击事件
     * @returns {flyer}
     */
    this.bindTabs = function() {
        var flyerClass = this;
        $.each($(this.former).find('div[flyer=tabs]'), function() {
            flyerClass.bindTab(this);
        });
        return this;
    }
    this.bindTab = function(dom) {
        var flyerClass = this;
        $(dom).find(this.tabHeaderClass).bind('click', function() {
            $(dom).find(flyerClass.tabHeaderClass).removeClass(flyerClass.activeClass);
            $(dom).find(flyerClass.tabContenerClass).removeClass(flyerClass.activeClass);
            $(this).addClass(flyerClass.activeClass);
            $(dom).find(flyerClass.tabContenerClass).eq($(this).prevAll().length).addClass(flyerClass.activeClass);
        });
        $(dom).find(flyerClass.tabHeaderClass + '.' + flyerClass.activeClass).click();
    }
}