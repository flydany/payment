
/**
 * @name 文件上传操作类
 * @author Flydany
 * @create on train at 2017/04/29 13:45
 */
var loaderPreviews = function() {
    loaderMulti.apply(this);
    this.maxSize = 1024 * 1024;
    this.init = function(params) {
        this.params = params;
        this.conter = params.conter;
        // 默认最多只能上传9张图片
        if(params.maxCounter) {
            this.maxCounter = params.maxCounter;
        }
        if(params.maxSize) {
            this.maxSize = params.maxSize;
        }
        // 填充HTML代码
        this.createHtml();
        this.fileInput = $(this.conter).find('.loader-file');
        // 初始上次已传图片的预览
        if ( ! checker_empty(params.prevLoaders)) {
            this.addPrevLoaderFiles(params.prevLoaders);
        }
        this.loader();
        return this;
    }
    //  过滤文件类型
    this.filterFiles = function(files) {
        var filterFiles = [];
        for(var i = 0, file; file = files[i]; ++i) {
            if (file.size >= this.maxSize) {
            layer.msg('图片：' + file.name + ' 太大！');
            }
            else {
                //获得文件后缀名
                var fileExt = file.name.substr(file.name.lastIndexOf(".")).toLowerCase();
                if(fileExt==".png" || fileExt==".gif" || fileExt==".jpg" || fileExt==".jpeg") {
                    filterFiles.push(file);
                }
                else {
                    layer.msg("图片仅限于 png, gif, jpeg, jpg格式 !");
                }
            }
        }
        return filterFiles;
    }
    // Loader HTML 创建 --------------------
    this.createHtml = function() {
        var loaderClass = this;
        var _html = '';
        _html += '<div class="loader-title">';
        _html += '    <div class="load-bar">';
        _html += '        <span class="file-picker">选择图片</span>';
        _html += '        <input class="loader-file" name="uploadfiles[]" type="file" size="' + this.maxCounter + '" multiple style="display:none;">';
        _html += '    </div>';
        _html += '    <span class="load-status"><span class="flyer-status blue thin">最多可以上传' + this.maxCounter + '张图片</span>，当前选中 0 张图片，共 0KB。</span>';
        _html += '</div>';
        _html += '<div class="loader-content multi"></div>';
        // 填充HTML到DOM中
        $(this.conter).addClass('flyer-loader').html(_html);
        
        // 初始化其他选择图片按钮事件
        $(this.conter).find('.file-picker').bind('click', function() {
            $(this).siblings('.loader-file').click();
        });
        // 初始化上传按钮事件
        $(this.conter).find('.uploader').bind('click', function() {
            if (loaderClass.waitLoaderFiles.length > 0) {
                $(this).siblings('.start-loader').click();
            }
            else {
                layer.msg('不存在需要上传的文件！');
            }
        });
    }
    // 生成图片预览功能
    this.createPreviewHtml = function(file, e) {
        var _html = '';
        _html += '<div class="load-image image-' + file.index + '" id="image-' + file.index + '" data-index="' + file.index + '">';
        _html += '    <div class="handle-bar">';
        _html += '        <p class="file-name">' + file.name + '</p>';
        _html += '        <span class="delete-file" title="删除"></span>';
        _html += '    </div>';
        _html += '    <div class="file-dispose">';
        _html += '        <img src="' + e.target.result + '">'; //  style="width:expression(this.width > ' + param.thumb_width + ' ? ' + param.thumb_width + 'px : this.width);"
        _html += '    </div>';
        _html += '</div>';
        
        return _html;
    }
    
    // 生成上次已传图片预览功能
    this.createPrevLoaderPreviewHtml = function(file) {
        var _html = '';
        _html += '<div class="load-image image-' + file.index + '" data-index="' + file.index + '">';
        _html += '    <div class="handle-bar">';
        _html += '        <p class="file-name">' + file.name + '</p>';
        _html += '        <span class="delete-file" title="删除"></span>';
        _html += '    </div>';
        _html += '    <div class="file-dispose">';
        _html += '        <img src="' + file.url + '">';
        _html += '    </div>';
        _html += '    <input name="loaded[]" value="' + file.url + '" type="hidden">';
        _html += '    <p class="load-success" style="display:block;"></p>';
        _html += '</div>';
        
        return _html;
    }
    // @name 显示上传文件信息
    // @param files 所有文件集合
    // @return boolean
    this.setStatusInfo = function(files) {
        var size = 0,
            number = files.length;
        // 计算所有文件大小
        $.each(files, function(k, v) {
            size += v.size;
        });
        if (size > 1024 * 1024) {
            size = (Math.round(size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
        }
        else {
            size = (Math.round(size * 100 / 1024) / 100).toString() + 'KB';
        }
        // 设置内容
        $(this.conter).find('.load-status').html("<span class=\"flyer-status blue thin\">还可以上传" + (this.maxCounter - this.counter) + "张图片</span>，当前选中 " + number + " 张图片，共 " + size + "。");
    }
    // @name 选择图片时调用的函数
    // @param selectFile file 当前选中的文件
    // @param files array 当前待上传的文件集合
    // @return boolean
    this.onSelect = function(selectFile, files) {
        var loaderClass = this;
        // 设置上传信息
        this.setStatusInfo(this.totalFiles);
        // 组织预览图片
        $.each(selectFile, function(k, v) {
            var reader = new FileReader();
            reader.onload = function(e) {
                // 把HTML信息填充到图片预览区域
                $(loaderClass.conter).find('.loader-content').append(loaderClass.createPreviewHtml(v, e));

                // 绑定预览区按钮事件 删除
                $(loaderClass.conter).find('.image-' + v.index + ' .delete-file').bind('click', function() {
                    loaderClass.deleteFile(parseInt($(this).parents('.load-image').attr('data-index')), true);
                });
                // 鼠标经过， 操作栏显示
                $(loaderClass.conter).find('.image-' + v.index).hover(
                    function(e) {
                        $(this).find('.handle-bar').stop().animate({ top: '0px' }, 'fast');
                    },
                    function(e) {
                        $(this).find('.handle-bar').stop().animate({ top: '-25px' }, 'fast');
                    }
                );
            }
            reader.readAsDataURL(v);
        });
        // 选中回调方法
        if(typeof(this.params.onSelect) != 'undefined') { this.params.onSelect(selectFile, files); }
    }
    // 上次已传的图片数组 生成预览调用函数
    // @name 添加前次已上传文件的添加事件
    // @describe 创建文件预览等
    // @param file object 文件详细信息
    // @return boolean
    this.onAddPrevLoaderFile = function(file) {
        var loaderClass = this;
        // 组织预览图片
        // 把HTML信息填充到图片预览区域
        $(loaderClass.conter).find('.loader-content').append(this.createPrevLoaderPreviewHtml(file));

        // 绑定预览区按钮事件 删除
        $(loaderClass.conter).find('.image-' + file.index + ' .delete-file').bind('click', function() {
            loaderClass.deleteFile(parseInt($(this).parents('.load-image').data('index')), true);
        });
        // 鼠标经过， 操作栏显示
        $(loaderClass.conter).find('.image-' + file.index).hover(
            function(e) {
                $(this).find('.handle-bar').stop().animate({top: '0px'}, 'fast');
            },
            function(e) {
                $(this).find('.handle-bar').stop().animate({top: '-25px'}, 'fast');
            }
        );
        if(typeof(this.params.onAddPrevLoaderFile) != 'undefined') { this.params.onAddPrevLoaderFile(file); }
    }
    // @name 删除文件时调用的函数
    // @param file file 当前选中的需要删除的文件
    // @param files array 当前所有文件的集合
    // @param boolean
    this.onDelete = function(file, files) {
        // 删除 DIV
        $(this.conter).find('.image-' + file.index).remove();
        // 重新计算文件
        this.setStatusInfo(files);
        // 回调方法
        if(typeof(this.params.onDelete) != 'undefined') { this.params.onDelete(file, files); }
    }
}