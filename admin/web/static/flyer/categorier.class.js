// create by flydany
// 2017-04-04 21:23:00

// 分类选择自动分级
var categorier = function() {
    // 参数设置
    this.index = 0;
    this.clone = '.input-inline';
    this.selectClass = '.flyer-select';
    this.data = [];
    this.relate = [];
    // 是否固定长度
    this.fixed = true;

    this.getIndex = function() {
        return this.index++;
    }
    // @name 设置data 数据
    this.setData = function(data) {
        this.data = data;
        return this;
    }
    // @name 设置data 数据
    this.setRelate = function(relate) {
        this.relate = relate;
        return this;
    }
    this.setFixed = function(fixed) {
        this.fixed = !!fixed;
        return this;
    }
    this.isFixed = function() {
        return this.fixed;
    }

    // @name Dom美化
    // @param param object 配置参数
    this.init = function(param) {
        if(param.data === undefined) {
            console.log('参数配置异常');
        }
        // 设置data\relate
        this.setData(param.data).setRelate(param.relate);
        // 设置是否固宽
        this.setFixed(param.fix);

        // 初始化加载一级分类
        this.renderCategory(param.dom, param.start, param.select);
        // 固定宽度分类值改变事件
        if(this.isFixed()) {
            // 一级分类改变时事件
            this.fixedCategoryChange(param.dom);
        }
        else {
            // 一级分类改变时事件
            this.categoryChange(param.dom);
        }
    }
    /**
     * @name 渲染当前等级的select元素
     * @param dom 元素
     * @param pid 父元素选中编号
     * @param select int 选中值
     *
     */
    this.renderCategory = function(dom, pid, select) {
        // 默认一级分类索引
        var _html = '';
        // console.log('select:' + select);
        if (select == undefined) {
            select = $(dom).attr('data-select');
        }
        select = this.findTopId(select, pid, this.relate);
        // 循环子分类
        if ($(dom).attr('data-empty') == 'true') {
            _html += '<option value="">--</option>';
        }
        if(pid in this.data) {
            for (var code in this.data[pid]) {
                _html += "<option value='" + code + "'";
                if (code == select) {
                    _html += " selected";
                }
                _html += ">" + this.data[pid][code] + "</option>";
            }
        }
        $(dom).html(_html).change().blur();
        return this;
    }
    // @name 固定宽度分类改变事件
    // @param dom object 父元素
    this.fixedCategoryChange = function(dom) {
        var categorierClass = this;
        var child = $(dom).attr('data-child');
        if(child == undefined) {
            return true;
        }
        $(dom).bind('change', function() {
            var pid = $(this).val();
            // console.log('parent value:' + pid);
            // console.log('child select:' + $(child).attr('data-select'));
            if($(child).attr('data-select') == undefined) {
                $(child).attr('data-select', $(this).attr('data-select'));
            }
            // console.log('child select:' + $(child).attr('data-select'));
            // 重新加载子分类
            categorierClass.renderCategory(child, pid, $(child).attr('data-select'));
        }).change();
        // 子分类改变时事件绑定
        this.fixedCategoryChange(child);
    }
    // @name 自定义宽度分类改变事件
    // @param dom object 父元素
    this.categoryChange = function(dom) {
        var categorierClass = this;
        $(dom).bind('change', function() {
            var pid = $(this).val();
            // console.log('parent value:' + $(dom).val());
            if( ! ($(this).val() in categorierClass.data)) {
                // 隐藏 子dom
                var child = $(dom).attr('data-child');
                $(child).val('').change();
                // $($(child).attr('data-selecter')).remove();
                $(child).parent(categorierClass.clone).remove();
                $(this).removeAttr('data-child');
                return true;
            }
            if( ! $(this).attr('data-child')) {
                id = $(this).attr('id') + '-child' + categorierClass.getIndex();
                var child = $(this).parent(categorierClass.clone).clone();
                $(child).find(categorierClass.selectClass).remove();
                $(child).find('select').removeAttr('data-selecter').attr('id', id);
                // var child = '<div class="' + $(dom).parent(categorierClass.clone).attr('class') + '"><select class="' + $(dom).attr('class') + '" id="' + id + '" name="' + $(dom).attr('name') + '" data-select="' + $(dom).attr('data-select') + '" data-mulit-index="' + index + '" data-empty="true"></select></div>';
                // $(child).attr('id', id);
                $(this).attr('data-child', '#' + id);
                $(this).parent(categorierClass.clone).after(child);
                // 重新加载子分类
                // console.log($(dom).attr('data-child'));
                // console.log(pid);
                categorierClass.renderCategory($(this).attr('data-child'), pid);
                // 子分类改变时事件绑定
                categorierClass.categoryChange($(this).attr('data-child'));
            }
            else {
                // 重新加载子分类
                categorierClass.renderCategory($(this).attr('data-child'), pid, $(this).attr('data-select'));
            }
        }).change();
        return this;
    }
    /**
     * @name 获取顶级父编号
     * @param id 当前父编号
     * @param currentId 当前编号
     * @param relate 关系对象
     * @returns int
     */
    this.findTopId = function (id, currentId, relate) {
        // console.log('id:' + id);
        // console.log('relate:');
        // console.log(relate);
        // console.log('current id:' + currentId);
        // console.log('relate id:' + relate[id]);
        if (id <= 0 || ! relate[id]) {
            return 0;
        }
        if (relate[id] == currentId) {
            // console.log('complete -------------------- ');
            return id;
        }
        else {
            return this.findTopId(relate[id], currentId, relate);
        }
    }
    /**
     * @name 获取分类标题，多选
     * @param ids string 分类编号
     * @return string
     */
    this.renderTitles = function(ids) {
        if($.trim(ids) == '' || ids == undefined) {
            return '--';
        }
        var _html = [];
        var texts = $.trim(ids).split(',');
        for(var i = 0; i < texts.length; ++i) {
            if(texts[i] == '') {
                continue;
            }
            _html.push(this.renderTitle(texts[i]));
        }
        return _html.splice('');
    }
    /**
     * @name 获取分类标题
     * @param id int 分类的编号
     * @returns int/string
     */
    this.renderTitle = function(id) {
        id = parseInt(id);
        if ( ! this.relate.hasOwnProperty(id)) {
            return id;
        }
        var category = this.data[this.relate[id]];
        if(category[id] != undefined && this.relate[id] > 0 && this.relate[this.relate[id]]) {
            return this.renderTitle(this.relate[id]) + ' <i class="icon-double-angle-right"></i> ' + category[id];
        }
        if(category[id] != undefined) {
            return category[id];
        }
        return id;
    }
}