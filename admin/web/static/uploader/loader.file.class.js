/**
 * @name 文件上传操作类
 * @author Flydany
 * @create on train at 2017/04/29 13:45
 */
var loaderFile = function() {
    loaderSingle.apply(this);
    this.init = function(params) {
        this.params = params;
        this.conter = params.conter;
        if(params.allow) {
            this.allow = params.allow;
        }
        // 填充HTML代码
        this.createHtml();
        this.fileInput = $(this.conter).find('.loader-file');
        this.loaderButton = $(this.conter).find('.start-loader');
        this.action = params.action;
        // 初始上次已传文件的预览
        if ( ! checker_empty(params.prevLoaders)) {
            this.addPrevLoaderFiles(params.prevLoaders);
        }
        this.loader();
        return this;
    }
    //  过滤文件类型
    this.filterFiles = function(files) {
        if( ! files) {
            this.clearFiles();
            return false;
        }
        var file = files[0];
        if (file.size >= this.maxSize) {
            jQuery.warning('file: ' + file.name + ' was too large!');
            return false;
        }
        else {
            //获得文件后缀名
            var fileExt = file.name.substr(file.name.lastIndexOf(".")).toLowerCase();
            if((this.allow[0] != '.') && ! (this.allow.indexOf(fileExt) >= 0)) {
                jQuery.warning('file format error!');
                return false;
            }
        }
        return file;
    }
    // Loader HTML 创建 --------------------
    this.createHtml = function() {
        var loaderClass = this;
        var _html = '';
        _html += '<div class="loader-title">';
        _html += '        <div class="load-status"><font class="flyer-status orange">choose an upload file.</font></div>';
        _html += '        <div class="load-bar">';
        _html += '              <span class="file-picker">choose</span>';
        _html += '              <span class="uploader">upload</span>';
        _html += '              <input class="loader-file" name="uploadfile" type="file" style="display:none;">';
        _html += '              <button class="start-loader" type="button" style="display:none;">confirm upload</button>';
        _html += '        </div>';
        _html += '</div>';
        _html += '<div class="load-progress-wrap"><div class="load-progress"></div></div>';
        // 填充HTML到DOM中
        $(this.conter).addClass('flyer-loader').addClass('single').html(_html);

        // 初始化其他选择文件按钮事件
        $(this.conter).find('.file-picker').bind('click', function() {
            $(this).siblings('.loader-file').click();
        });
        // 初始化上传按钮事件
        $(this.conter).find('.uploader').bind('click', function() {
            if (loaderClass.selectFile.length > 0) {
                $(this).siblings('.start-loader').click();
            }
            else {
                jQuery.warning('there is no file that needs to be uploaded!');
            }
        });
    }
    // @name 显示上传文件信息
    // @param files 所有文件集合
    // @return boolean
    this.setStatusInfo = function(files) {
        if( ! files.length) {
            // 设置内容
            $(this.conter).find('.load-status').html("<font class='flyer-status red'>please choose to upload a file</font>");
            return true;
        }
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
        $(this.conter).find('.load-status').html("<font class='flyer-status orange'>file #" + files[0].name+ "#, size: " + size + ".</font>");
        return true;
    }
    // @name 显示原始已传文件内容提示
    // @param file file 当前需要展示的已上传文件
    // @return boolean
    this.showPrevLoaderHtml = function(file) {
        var loaderClass = this;
        // 显示原始已传文件内容
        if(file) {
            $(this.conter).find('.load-status').html("<font class='flyer-status blue'>transmitted document: " + file.name+ ".</font>");
        }
    }
    // @name 选择文件时调用的函数
    // @param selectFile file 当前选中的文件
    // @param files array 当前待上传的文件集合
    // @return boolean
    this.onSelect = function(selectFile, files) {
        var loaderClass = this;
        // 设置上传信息
        this.setStatusInfo(selectFile);
        // 选中回调方法
        if(typeof(this.params.onSelect) != 'undefined') { this.params.onSelect(selectFile, files); }
    }
    // 上次已传的文件数组 生成预览调用函数
    // @name 添加前次已上传文件的添加事件
    // @describe 创建文件预览等
    // @param file object 文件详细信息
    // @return boolean
    this.onAddPrevLoaderFile = function(file) {
        var loaderClass = this;
        // 组织预览图片
        this.showPrevLoaderHtml(file);

        // 组织预览文件
        if(typeof(this.params.onAddPrevLoaderFile) != 'undefined') { this.params.onAddPrevLoaderFile(file); }
    }
    // @name 删除文件时调用的函数
    // @param file file 当前选中的需要删除的文件
    // @param files array 当前所有文件的集合
    // @param boolean
    this.onDelete = function(file, files) {
        // 重新计算文件
        this.setStatusInfo(files);
        // 回调方法
        if(typeof(this.params.onDelete) != 'undefined') { this.params.onDelete(file, files); }
    }
    // @name 文件上传进度改变时调用函数
    // @param file file 当前正在上传的文件
    // @param loaded float 已上传的部分的大小
    // @param total float 文件总大小
    // @return boolean
    this.onProgress = function(file, loaded, total) {
        // 隐藏失败状态
        $(this.conter).find('.load-progress').removeClass('load-failure').removeClass('load-success');
        // 计算 进度条 进度
        var progressElement = $(this.conter).find('.load-progress'), percent = (loaded / total * 100).toFixed(2) + '%';
        if(progressElement.is(':hidden')) {
            progressElement.show();
        }
        progressElement.css("width", percent);
        // 回调方法
        if(typeof(this.params.onProgress) != 'undefined') { this.params.onProgress(file, loaded, total); }
    }
    // @name 提供给外部获取单个文件上传成功，供外部实现成功效果
    // @param file file 当前成功上传的文件
    // @param response json 当前请求的返回
    // @return boolean
    this.onSuccess = function(file, response) {
        // 隐藏进度条，显示成功图标
        $(this.conter).find('.load-progress').removeClass('load-failure').addClass('load-success').show();
        $(this.conter).find('.load-status').html("<font class='flyer-status blue'>transmitted document: " + file.name+ ".</font>");
        // 回调方法
        if(typeof(this.params.onSuccess) != 'undefined') { this.params.onSuccess(file, response); }
    }
    // @name 提供给外部获取单个文件上传失败，供外部实现失败效果
    // @param file file 当前上传失败的文件
    // @param response json 当前请求的返回
    // @return boolean
    this.onFailure = function(file, response) {
        // 隐藏进度条，显示失败状态
        $(this.conter).find('.load-progress').removeClass('load-success').addClass('load-failure').show();
        // 回调方法
        if(typeof(this.params.onFailure) != 'undefined') { this.params.onFailure(file, response); }
    }
    // @name 提供给外部获取全部文件上传完成，供外部实现完成效果
    // @param response json 当前请求的返回
    // @return boolean
    this.onComplete = function(response) {
        // 回调方法
        if(typeof(this.params.onComplete) != 'undefined') { this.params.onComplete(response); }
    }
}