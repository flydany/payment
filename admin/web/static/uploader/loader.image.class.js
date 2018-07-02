
/**
 * @name 单个图片上传操作类
 * @author Flydany
 * @create on train at 2017/04/29 13:45
 */
var loaderImage = function() {
    loaderSingle.apply(this);
    this.maxSize = 1024 * 1024;
    this.init = function(params) {
        this.params = params;
        this.conter = params.conter;
        if(params.maxSize) {
            this.maxSize = params.maxSize;
        }
        // 填充HTML代码
        this.createHtml();
        this.fileInput = $(this.conter).find('.loader-file');
        this.loaderButton = $(this.conter).find('.start-loader');
        this.action = params.action;
        // 初始上次已传图片的预览
        if ( ! checker_empty(params.prevLoaders)) {
            this.addPrevLoaderFile(params.prevLoaders);
        }
        this.loader();
        return this;
    }
    //  过滤文件类型
    this.filterFile = function(files) {
        if( ! files) {
            this.clearFiles();
            return false;
        }
        var file = files[0];
        if (file.size >= this.maxSize) {
            layer.msg('图片：' + file.name + ' 太大！');
            return false;
        }
        else {
            //获得文件后缀名
            var fileExt = file.name.substr(file.name.lastIndexOf(".")).toLowerCase();
            if ( ! (fileExt == ".png" || fileExt == ".gif" || fileExt == ".jpg" || fileExt == ".jpeg")) {
                layer.msg("图片仅限于 png, gif, jpeg, jpg格式 !");
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
        _html += '    <div class="load-bar">';
        _html += '        <span class="file-picker">选择图片</span>';
        _html += '        <span class="uploader">开始上传</span>';
        _html += '        <input class="loader-file" name="uploadfiles[]" type="file" size="30" style="display:none;">';
        _html += '        <button class="start-loader" type="button" style="display:none;">确认上传文件</button>';
        _html += '    </div>';
        _html += '    <span class="load-status">点击左侧选择图片，选择需要上传的图片。</span>';
        _html += '</div>';
        _html += '<div class="loader-content single"></div>';
        // 填充HTML到DOM中
        $(this.conter).addClass('flyer-loader').html(_html);

        // 初始化其他选择图片按钮事件
        $(this.conter).find('.file-picker').bind('click', function() {
            $(this).siblings('.loader-file').click();
        });
        // 初始化上传按钮事件
        $(this.conter).find('.uploader').bind('click', function() {
            if (loaderClass.selectFile) {
                $(this).siblings('.start-loader').click();
            }
            else {
                layer.msg('请先选择一个文件，再进行上传操作！');
            }
        });
    }
    // 生成图片预览功能
    this.createPreviewHtml = function(file, e) {
        var _html = '';
        _html += '<div class="load-image-single image-1">';
        _html += '    <div class="file-dispose">';
        _html += '        <img src="' + e.target.result + '">';
        _html += '    </div>';
        _html += '    <p class="load-progress"></p>';
        _html += '    <p class="load-failure">上传失败，请重试！</p>';
        _html += '    <p class="load-success"></p>';
        _html += '</div>';

        return _html;
    }

    // 生成上次已传图片预览功能
    this.createPrevLoaderPreviewHtml = function(file) {
        var _html = '';
        _html += '<div class="load-image-single image-1">';
        _html += '    <div class="file-dispose">';
        _html += '        <img src="' + file.url + '">';
        _html += '    </div>';
        _html += '    <p class="load-success" style="display:block;"></p>';
        _html += '</div>';

        return _html;
    }
    // @name 显示上传文件信息
    // @param files 所有文件集合
    // @return boolean
    this.setStatusInfo = function(file) {
        if (file.size > 1024 * 1024) {
            size = (Math.round(file.size * 100 / (1024 * 1024)) / 100).toString() + 'MB';
        }
        else {
            size = (Math.round(file.size * 100 / 1024) / 100).toString() + 'KB';
        }
        // 设置内容
        $(this.conter).find('.load-status').html("<span class='flyer-status red thin'>图片：" + file.name + "，共 " + size + "。</span>");
    }
    // @name 选择图片时调用的函数
    // @param selectFile file 当前选中的文件
    // @param files array 当前待上传的文件集合
    // @return boolean
    this.onSelect = function(selectFile) {
        var loaderClass = this;
        // 设置上传信息
        this.setStatusInfo(selectFile);
        // 组织预览图片
        var reader = new FileReader();
        reader.onload = function(e) {
            // 把HTML信息填充到图片预览区域
            $(loaderClass.conter).find('.loader-content').html(loaderClass.createPreviewHtml(selectFile, e));
        }
        reader.readAsDataURL(selectFile);
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
        $(loaderClass.conter).find('.loader-content').html(this.createPrevLoaderPreviewHtml(file));
        // 初始图片加载事件
        if(typeof(this.params.onAddPrevLoaderFile) != 'undefined') { this.params.onAddPrevLoaderFile(file); }
    }
    // @name 文件上传进度改变时调用函数
    // @param file file 当前正在上传的文件
    // @param loaded float 已上传的部分的大小
    // @param total float 文件总大小
    // @return boolean
    this.onProgress = function(file, loaded, total) {
        // 隐藏失败状态
        $(this.conter).find('.image-1 .load-failure').hide();
        // 计算 进度条 进度
        var progressElement = $(this.conter).find('.image-1 .load-progress'), percent = (loaded / total * 100).toFixed(2) + '%';
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
        $(this.conter).find('.image-1 .load-progress').hide().siblings('.load-success').show();
        $(this.conter).find('.load-status').html("<span class='flyer-status green'>" + $(this.conter).find('.load-status').text() + "已上传完毕。</span>");
        // 回调方法
        if(typeof(this.params.onSuccess) != 'undefined') { this.params.onSuccess(file, response); }
    }
    // @name 提供给外部获取单个文件上传失败，供外部实现失败效果
    // @param file file 当前上传失败的文件
    // @param response json 当前请求的返回
    // @return boolean
    this.onFailure = function(file, response) {
        // 隐藏进度条，显示失败状态
        $(this.conter).find('.image-1 .load-progress').hide().siblings('.load-failure').show();
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