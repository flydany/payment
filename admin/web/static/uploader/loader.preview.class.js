
/**
 * @name 单个图片上传操作类
 * @author Flydany
 * @create on train at 2017/04/29 13:45
 */
var loaderPerview = function() {
    loaderSingle.apply(this);
    this.maxSize = 1024 * 1024;
    this.init = function(params) {
        this.params = params;
        this.conter = params.conter;
        this.name = params.name;
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
        _html += '<div class="file-picker"></div>';
        _html += '<div class="loader-file-div" style="display:none;"><input class="loader-file" name="' + this.name + '" type="file"></div>';
        _html += '<div class="loader-content"></div>';
        // 填充HTML到DOM中
        $(this.conter).addClass('flyer-loader-single').html(_html);

        // 初始化其他选择图片按钮事件
        $(this.conter).find('.file-picker').bind('click', function() {
            $(loaderClass.conter).find('.loader-file').click();
        });
    }
    // 生成图片预览功能
    this.createPreviewHtml = function(file, e) {
        var _html = '';
        _html += '<div class="file-dispose">';
        _html += '    <img src="' + e.target.result + '">';
        _html += '</div>';
        _html += '<span class="delete-file"></span>';

        return _html;
    }

    // 生成上次已传图片预览功能
    this.createPrevLoaderPreviewHtml = function(file) {
        var _html = '';
        _html += '<div class="file-dispose">';
        _html += '    <img src="' + file.url + '">';
        _html += '</div>';
        _html += '<span class="delete-file"></span>';

        return _html;
    }
    // @name 选择图片时调用的函数
    // @param selectFile file 当前选中的文件
    // @param files array 当前待上传的文件集合
    // @return boolean
    this.onSelect = function(selectFile) {
        var loaderClass = this;
        // 组织预览图片
        var reader = new FileReader();
        reader.onload = function(e) {
            // 把HTML信息填充到图片预览区域
            $(loaderClass.conter).find('.loader-content').html(loaderClass.createPreviewHtml(selectFile, e));
            // 绑定预览区按钮事件 删除
            $(loaderClass.conter).find('.delete-file').bind('click', function() {
                loaderClass.deleteFile();
            });
        }
        reader.readAsDataURL(selectFile);
        // 选中回调方法
        if(typeof(this.params.onSelect) != 'undefined') { this.params.onSelect(selectFile, files); }
    }
    this.onDelete = function() {
        $(this.conter).find('.loader-content').html('');
        if(this.fileInput) {
            var input = $(this.conter).find('.loader-file').clone(true).val("");
            $(this.conter).find('.loader-file-div').html('').append(input);
        }
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
        // 绑定预览区按钮事件 删除
        $(loaderClass.conter).find('.delete-file').bind('click', function() {
            loaderClass.deleteFile();
        });
        // 初始图片加载事件
        if(typeof(this.params.onAddPrevLoaderFile) != 'undefined') { this.params.onAddPrevLoaderFile(file); }
    }
}