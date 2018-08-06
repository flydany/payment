/**
 * table-template js
 * TABLE 模版 公用js函数定义
 */
// @created by flydany
// 2017-04-04 13:33:00

var tableHandler = {
    SuccessCode: 200,

    /**
     * @name 添加一条插入表记录
     * @param param 参数配置
     * @param param.button  插入行事件按钮
     * @param param.beforeAlert  提交前函数校验
     * @param param.clone  行克隆对象
     * @param param.afterAlert  提交后执行函数
     */
    insertLine: function(param) {
        if( ! param.button) {
            console.log('parameters wrong');
        }
        $(param.button).bind('click', function() {
            param.mthis = this;
            // 如果存在事件触发前调用的函数，调用函数
            if (tableHandler.callUserFunction(param.beforeAlert, param) === false) {
                return false;
            }

            // 处理正文
            param.table = $(this).attr('data-table');
            if(param.table == undefined) {
                param.table = $(this).parents('table');
            }
            // console.log(param.table);
            // 报错
            if(param.table == undefined) {
                console.log('can\'t find table');
            }
            var clone = $(param.clone).clone(true);
            // console.log(clone);
            // console.log($(this).attr('data-id'));
            // console.log($(this).parents('tr').attr('data-id'));
            // 初始化clone对象
            $(clone).find('.first .handle').hide();
            // 获取
            var id = $(this).attr('data-id') ? $(this).attr('data-id') : $(this).parents('tr').attr('data-id');
            if (id == 0 || id == undefined) {
                if ($(param.table).find('tbody td').length < $(param.table).find('thead th').length) {
                    $(param.table).find('tbody').html(clone);
                }
                else {
                    if($(this).attr('data-location') == 'after') {
                        $(param.table).find('tbody tr:last').after($(clone));
                    }
                    else {
                        $(param.table).find('tbody tr:first').before($(clone));
                    }
                }
            }
            else {
                $(this).parents('tr').after($(clone));
            }

            // 如果存在事件触发后调用的函数，调用函数
            param.cloneDom = clone;
            tableHandler.callUserFunction(param.afterAlert, param);
        });
        return tableHandler;
    },
    /**
     * @name 保存行内元素
     * @param param 更新参数
     * @param param.button  保存事件按钮
     * @param param.beforeAlert  数据提交前函数校验
     * @param param.url  数据提交地址
     * @param param.afterAlert  数据提交后执行函数
     */
    saveLine: function(param) {
        $(param.button).bind('click', function() {
            param.mthis = this;
            param.tr = $(this).parents('tr');
            // 如果存在事件触发前调用的函数，调用函数
            if (tableHandler.callUserFunction(param.beforeAlert, param) === false) {
                return false;
            }
            // 组合请求参数
            var data = {};
            if (/^(-){0,1}\d+$/.test($(param.tr).attr('data-id'))) {
                data.id = $(param.tr).attr('data-id');
            }
            $.each($(param.tr).find('.edit'), function() {
                var value = $(this).val();
                if ($(this).attr('type') == 'checkbox' && ! $(this).is(':checked')) {
                    value = '';
                }
                data[$(this).attr('name')] = value;
            });
            data['submit'] = 'json';
            data['_csrf'] = $('meta[name=csrf-token]').attr('content');
            // 弹出loading动画
            var url = param.url ? param.url : $(this).attr('data-href');
            // $(param.button).attr('data-layer-index', layer.load(0, { shade: [0.3, '#000'] }));
            var dialog = jQuery.loading();
            // 保存数据
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                dataType: 'json',
                // ajax请求完毕之后执行(失败成功都会执行)
                complete: function() {
                    // layer.close($(param.button).attr('data-layer-index'));
                    jQuery.loaded(dialog);
                },
                // 加载信息异常提示
                error: function() {
                    // layer.msg('json request failed', { shift: 6 });
                    jQuery.warning('json request failed');
                },
                // 数据加载成功处理方法
                success: function(ret) {
                    var data = ret;
                    // 处理成功
                    if (data.code == tableHandler.SuccessCode) {
                        // layer.msg('save successful!');
                        jQuery.warning('save successful!');
                        if (data.id) {
                            $(param.tr).attr('data-id', data.id).attr('data-type', 'insert');
                        }
                        else {
                            $(param.tr).attr('data-type', 'update');
                        }
                        param.data = data;
                        // 如果存在事件触发后调用的函数，调用函数
                        tableHandler.callUserFunction(param.afterAlert, param);
                    }
                    // 处理失败
                    else {
                        // layer.msg(data.message, { shift: 6 });
                        jQuery.warning(data.message);
                    }
                }
            });
        });
    },
    /**
     * @name ajax 添加数据 弹层处理事件初始化
     * @param param 弹层配置参数
     * @param param.button  弹窗事件按钮
     * @param param.beforeAlert  弹窗前函数校验
     * @param param.content  弹出框对象
     * @param param.src  如果弹出框内容不存在则为弹出frame
     * @param param.area  弹出框大小
     * @param param.afterAlert  弹出后执行函数
     */
    alertDialog: function(param) {
        $.each($(param.button), function() {
            $(this).bind('click', function() {
                param.mthis = this;
                param.tr = $(param.mthis).parents('tr');
                // 如果存在事件触发前调用的函数，调用函数
                if (tableHandler.callUserFunction(param.beforeAlert, param) === false) {
                    return false;
                }
                var url = param.src ? param.src : $(this).attr('data-href');
                url += ((url.indexOf('?') > 0) ? '&' : '?') + 'id=' + $(param.tr).attr('data-id');
                // 弹层
                jQuery.dialog(param.title, url);

                $(param.mthis).attr('data-index', param.index);
                if ($(param.tr).length) {
                    $(param.tr).attr('data-index', param.index);
                }

                // 如果存在事件触发后调用的函数，调用函数
                tableHandler.callUserFunction(param.afterAlert, param);
            });
        });
    },
    /**
     * @name 渲染td中的分类标识
     * @param param 配置参数
     * @param param.beforeRender  渲染前函数校验
     * @param param.functionName  是否执行函数渲染
     * @param param.category  需要被渲染的DOM
     * @param param.select  值来源DOM
     * @param param.afterRender  渲染后执行函数
     */
    renderCategory: function(param) {
        $(param.category).each(function() {
            param.mthis = this;
            if (tableHandler.callUserFunction(param.beforeRender, param) === false) {
                return false;
            }
            if ($.trim($(this).text()) == '') {
                $(this).html((param.default == undefined) ? '' : param.default);
            }
            else {
                var texts = $.trim($(this).text()).split(',');
                var _html = [];
                for(var i = 0; i < texts.length; ++i) {
                    var value = icon_class = '',
                        status = 'blue';
                    if(texts[i] == '') {
                        continue;
                    }
                    var text = texts[i];
                    if (param.functionName) {
                        var value = tableHandler.callUserFunction(param.functionName, text);
                    }
                    else {
                        // 判断是名字 / 选择器
                        if (/^[\w_-]{1,}$/.test(param.select)) {
                            param.select = '.search select[name=' + param.select + ']';
                        }
                        var option = param.select + ' option[value=' + text + ']';
                        if ($(option).length) {
                            value = $(option).text();
                            icon_class = $(option).attr('data-icon');
                            status = $(option).attr('data-status') ? $(option).attr('data-status') : '';
                        }
                        else {
                            value = (param.default == undefined) ? text : param.default;
                        }
                    }
                    _html.push(value);
                }
                $(this).html(_html.join(param.splite ? param.splite : ''));
            }
            if (tableHandler.callUserFunction(param.afterRender, param) === false) {
                return false;
            }
        });
    },
    /**
     * @name 单条操作
     * @param param 配置参数
     * @param param.button  操作触发按钮
     * @param param.url  数据提交地址
     */
    requestSingle: function(param) {
        if(param.button) {
            $(param.button).bind('click', function() {
                tableHandler.requestImmediately(param, this);
            });
        }
        else {
            tableHandler.requestImmediately(param);
        }
        return true;
    },
    requestImmediately: function(param, mthis) {
        // 初始化配置参数
        param.data = {};
        param.mthis = mthis ? mthis : $('body');
        if(mthis) {
            param.mthis = mthis;
            param.table = $(mthis).parents(param.tableKey ? param.tableKey : 'table');
            param.tr = $(mthis).parents(param.trKey ? param.trKey : 'tr');
            var id = $(param.tr).attr('data-id');
            if (id <= 0) {
                // layer.msg('there was something wrong, please try again!', { shift: 6 });
                jQuery.warning('there was something wrong, please try again!');
                return false;
            }
            param.data.id = id;
            if ($(mthis).data()) {
                for (var name in $(mthis).data()) {
                    if($.inArray(name, ['id', 'table', 'href']) >= 0) {
                        continue;
                    }
                    param.data[name] = $(mthis).attr('data-' + name);
                }
            }
        }
        if (tableHandler.callUserFunction(param.beforeRequest, param) == false) {
            return false;
        }
        param.url = param.url ? param.url : $(mthis).attr('data-href');
        // 确认弹窗
        if(param.isConfirm || param.isConfirm === undefined) {
            jQuery.confirm(function() {
                    tableHandler.request(param);
                },
                param.title
            );
        }
        else {
            tableHandler.request(param);
        }
    },
    /**
     * @name 批量操作
     * @param param 配置参数
     * @param param.button  操作触发按钮
     * @param param.url  数据提交地址
     */
    requestMulti: function(param) {
        $(param.button).bind('click', function() {
            // 初始化配置参数
            param.mthis = this;
            param.table = $(this).attr('data-table');
            // 校验是否存在删除数据
            var checkbox = $(param.table).find('input[type=checkbox].list:checked');
            var checkboxLength = $(checkbox).length;
            if (checkboxLength <= 0) {
                // layer.msg((param.title ? param.title : 'batch operation') + ' need select the line which you want to operator', { shift: 6 });
                jQuery.warning((param.title ? param.title : 'batch operation') + ' need select the line which you want to operator');
                return false;
            }
            if (tableHandler.callUserFunction(param.beforeRequest, param) == false) {
                return false;
            }
            // 初始化请求参数
            param.data = {};
            param.data.ids = [];
            for (var i = 0; i < checkboxLength; ++i) {
                param.data.ids.push($(checkbox).eq(i).val());
            }
            if ($(this).data()) {
                for (var name in $(this).data()) {
                    if($.inArray(name, ['id', 'table', 'href']) >= 0) {
                        continue;
                    }
                    param.data[name] = $(this).attr('data-' + name);
                }
            }
            param.tr = $(checkbox).parents(param.trKey ? trKey.trKey : 'tr');
            param.url = param.url ? param.url : $(this).attr('data-href');
            // 确认弹窗
            jQuery.confirm(function() {
                    tableHandler.request(param);
                },
                param.title ? param.title : 'batch operation'
            );
            return true;
        });
    },
    /**
     * @name 执行操作
     * @param param 配置参数
     * @param param.data  操作提交数据
     * @param param.url  数据提交地址
     * @param param.mthis  数据提交按钮
     * @param param.isKeep int 是否保留 操作行的数据
     * @param param.isAlert int 是否提示进度
     * @param param.table  对应表单
     * @param param.tr  对应行
     */
    request: function(param) {
        if (tableHandler.callUserFunction(param.beforePost, param) == false) {
            return false;
        }
        // 添加系统参数
        param.data['submit'] = 'json';
        param.data['_csrf'] = $('meta[name="csrf-token"]').attr("content");
        if(param.isShadow == undefined || param.isShadow) {
            // 加载弹层
            // $(param.mthis).attr('data-layer-index', layer.load(0, { shade: [0.3, '#000'] }));
            var dialog = jQuery.loading();
        }
        // 保存数据
        $.ajax({
            url: param.url,
            type: 'POST',
            data: param.data,
            async: (param.isAsync || param.isAsync === undefined) ? true : false,
            dataType: 'json',
            // ajax请求完毕之后执行(失败成功都会执行)
            complete: function() {
                if(param.isShadow == undefined || param.isShadow) {
                    // 加载弹层
                    // layer.close($(param.mthis).attr('data-layer-index'));
                    jQuery.loaded(dialog);
                }
                // 如果存在事件触发后调用的函数，调用函数
                tableHandler.callUserFunction(param.afterPost, param);
            },
            // 加载信息异常提示
            error: function() {
                // layer.msg('ajax request failed', { shift: 6 });
                jQuery.warning('ajax request failed');
            },
            // 数据加载成功处理方法
            success: function(ret) {
                param.response = ret;
                if (param.response.code == tableHandler.SuccessCode) {
                    if(param.isAlert || param.isAlert === undefined) {
                        // layer.msg(param.response.message ? param.response.message : (param.title ? param.title : 'operation') + ' successful');
                        jQuery.success(param.response.message ? param.response.message : (param.title ? param.title : 'operation') + ' successful');
                    }
                    if(param.isKeep || param.isKeep === undefined) {

                    }
                    else {
                        $(param.tr).remove();
                    }
                    if ($(param.table).find('tbody').length) {
                        if ( ! $(param.table).find('tbody tr').length) {
                            $(param.table).find('tbody').html('<tr><td class="first" colspan="' + $(param.table).find('thead th').length + '"><i class="icon-ban-circle"></i> current data clear already</td></tr>');
                        }
                    }
                    else {
                        if (param.trKey && ! $(param.table).find(param.trKey).length) {
                            $(param.table).html('<div class="pd"><i class="icon-ban-circle"></i> current data clear already</div>');
                        }
                    }

                    // 如果存在事件触发后调用的函数，调用函数
                    tableHandler.callUserFunction(param.requestSuccess, param);
                }
                else {
                    // layer.alert(param.response.message, { icon: 2 });
                    jQuery.warning(param.response.message);
                    tableHandler.callUserFunction(param.requestFail, param);
                }
            }
        });
        return true;
    },
    // 执行用户自定义函数
    callUserFunction: function(functionName, param) {
        if (functionName) {
            if (typeof(functionName) == 'function') {
                return functionName(param);
            }
            else if (typeof(functionName) == 'string') {
                return eval(functionName + '(' + param + ')');
            }
        }
        return true;
    }
}