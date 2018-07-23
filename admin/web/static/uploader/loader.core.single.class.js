
/**
 * @name 文件上传操作类
 * @author Flydany
 * @create on train at 2017/04/29 13:45
 */
var loaderSingle = function() {
    // 上传插件容器
    this.conter = null;
    // 文件选择输入框
    this.fileInput = null;
    // 上传文件按钮
    this.loaderButton = null;
    // 图片上传地址
    this.action = null;
    // 前次上传的文件数组
    this.prevLoaderFile = '';
    this.allow = ['.'];
    // 当前图片选择框选中的图片数组
    this.selectFile = {};
    // @name 添加前次已上传文件的添加事件
    // @describe 创建文件预览等
    // @param file object 文件详细信息
    // @return boolean
    this.onAddPrevLoaderFile = function(file) {

    }
    // @name 过滤文件时调用函数
    // @param files array 选中的文件集合
    // @return files array 过滤之后的文件集合
    this.filterFile = function(files) {
        return files;
    }
    // @name 选择图片时调用的函数
    // @param selectFile file 当前选中的文件
    // @param files array 当前待上传的文件集合
    // @return boolean
    this.onSelect = function(selectFile, files) {

    }
    // @name 文件上传进度改变时调用函数
    // @param file file 当前正在上传的文件
    // @param loaded float 已上传的部分的大小
    // @param total float 文件总大小
    // @return boolean
    this.onProgress = function(file, loaded, total) {

    }
    // @name 提供给外部获取单个文件上传成功，供外部实现成功效果
    // @param file file 当前成功上传的文件
    // @param response json 当前请求的返回
    // @return boolean
    this.onSuccess = function(file, response) {

    }
    // @name 提供给外部获取单个文件上传失败，供外部实现失败效果
    // @param file file 当前上传失败的文件
    // @param response json 当前请求的返回
    // @return boolean
    this.onFailure = function(file, response) {

    }
    // @name 提供给外部获取全部文件上传完成，供外部实现完成效果
    // @param response json 当前请求的返回
    // @return boolean
    this.onComplete = function(response) {

    }

    // 初始化方法，给选择、上传按钮绑定事件
    this.loader = function() {
        var loaderSingleClass = this;

        // 如果选择按钮存在
        if(this.fileInput) {
            // 绑定change事件
            $(this.fileInput).bind('change', function(e) { loaderSingleClass.findFile(e); });
        }
        // 如果上传按钮存在
        if(this.loaderButton) {
            // 绑定click事件
            $(this.loaderButton).bind('click', function(e) { loaderSingleClass.uploadFile(e); });
        }
    }

    // @name 添加单个上次已上传文件
    // @param urls array 前次已上传的文件的url路径
    // @return boolean
    this.addPrevLoaderFile = function(url) {
        var spName = url.split('/');
        // 设置当前已上传文件的下标信息
        var file = { name: spName[spName.length - 1], size: 0, url: url };
        ++this.fileNumber;
        // 添加文件 回调处理函数，设置文件预览等
        this.onAddPrevLoaderFile(file);
    }
    // @name 获取选中的文件
    // @param e object window
    // @return boolean
    this.findFile = function(e) {
        var loaderSingleClass = this;
        // 从事件中获取选中的所有文件
        var files = e.target.files || e.dataTransfer.files;
        // 过滤重复的文件
        this.selectFile = this.filterFile(files);
        if(this.selectFile == false) {
            return false;
        }
        // 处理每个文件的下标
        this.dealtFile();
    }
    // @name 处理每个文件的配置
    // @param this.selectFile array 当前选中的文件集合
    // @return boolean
    this.dealtFile = function() {
        // 执行文件选择时的回调 onSelect，供外部实现文件预览操作
        this.onSelect(this.selectFile);
        // 激活上传图片按钮
        $(this.loaderButton).removeAttr('disabled');
        return true;
    }
    // @name 清空文件
    this.deleteFile = function() {
        // 执行回调删除方法，供外部进行删除效果的实现
        this.onDelete();
        this.selectFile = null;
    }
    // @name 上传单个文件
    // @param file object 需要上传的文件
    // @return boolean
    this.uploadFile = function() {
        var loaderSingleClass = this;
        var form = new FormData();
        form.append('upload', this.selectFile[0]);
        form.append('_csrf', $('meta[name=csrf-token]').attr('content'));
        var xhr = new XMLHttpRequest();
        // 绑定上传事件
        // 绑定进度事件
        xhr.upload.addEventListener('progress', function(e) {
            // 进度改变回调
            loaderSingleClass.onProgress(loaderSingleClass.selectFile, e.loaded, e.total);
        }, false);
        // 绑定上传失败事件
        xhr.addEventListener('error', function(e) {
            // 失败回调
            loaderSingleClass.onFailure(loaderSingleClass.selectFile, xhr.responseText);
        }, false);
        // 绑定上传完成事件
        xhr.addEventListener('load', function(e) {
            var response = $.parseJSON(xhr.responseText);
            if (response == 'undefined' || response.code != 200) {
                // 回调失败函数
                loaderSingleClass.onFailure(loaderSingleClass.selectFile[0], xhr.responseText);
            }
            else {
                // 回调成功函数
                loaderSingleClass.onSuccess(loaderSingleClass.selectFile[0], xhr.responseText);
                // 回调上传完毕函数
                loaderSingleClass.onComplete('All done!');
                loaderSingleClass.selectFile = {};
                // 禁用图片上传按钮
                $(loaderSingleClass.loaderButton).attr('disabled', true);
            }
        }, false);
        xhr.open('POST', this.action, true);
        xhr.send(form);
    }
}