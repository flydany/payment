
/**
 * @name 文件上传操作类
 * @author Flydany
 * @create on train at 2017/04/29 13:45
 */
var loaderMulti = function() {
    // 上传插件容器
    this.conter = null;
    // 文件选择输入框
    this.fileInput = null;
    // 上传文件按钮
    this.loaderButton = null;
    // 图片上传地址
    this.action = null;
    // 图片域名地址
    this.uri = null;
    // 前次上传的文件数组
    this.prevLoaderFiles = [];
    // 需要上传的图片数组
    this.waitLoaderFiles = [];
    // 当前图片选择框选中的图片数组
    this.selectFiles = [];
    // 所有的图片数组
    this.totalFiles = [];
    // 上次已传的图片数组
    this.loadedFiles = [];
    // 图片计数号码
    this.fileNumber = 0;
    // 图片计数器
    this.maxCounter = 9;
    this.counter = 0;
    // @name 添加前次已上传文件的添加事件
    // @describe 创建文件预览等
    // @param file object 文件详细信息
    // @return boolean
    this.onAddPrevLoaderFile = function(file) {

    }
    // @name 过滤文件时调用函数
    // @param files array 选中的文件集合
    // @return files array 过滤之后的文件集合
    this.filterFiles = function(files) {
        return files;
    }
    // @name 选择图片时调用的函数
    // @param selectFile file 当前选中的文件
    // @param files array 当前待上传的文件集合
    // @return boolean
    this.onSelect = function(selectFile, files) {

    }
    // @name 删除文件时调用的函数
    // @param file file 当前选中的需要删除的文件
    // @param files array 当前所有文件的集合
    // @param boolean
    this.onDelete = function(file, files) {

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
        var loaderMultiClass = this;
        
        // 如果选择按钮存在
        if(this.fileInput) {
            // 绑定change事件
            $(this.fileInput).bind('change', function(e) {
                loaderMultiClass.findFiles(e);
                $(this).after($(this).clone(true).val("")).remove();
            });
        }
        // 如果上传按钮存在
        if(this.loaderButton) {
            // 绑定click事件
            $(this.loaderButton).bind('click', function(e) { loaderMultiClass.uploadFiles(e); });
        }
        return this;
    }
    
    // @name 添加多个上次已上传文件
    // @param urls array 前次已上传的文件的url路径集合
    // @return boolean
    this.addPrevLoaderFiles = function(urls) {
        var loaderMultiClass = this;
        $.each(urls, function(k, url) {
            loaderMultiClass.addPrevLoaderFile(url);
        });
        return true;
    }
    // @name 添加单个上次已上传文件
    // @param urls array 前次已上传的文件的url路径
    // @return boolean
    this.addPrevLoaderFile = function(url) {
        var spName = url.split('/');
        // 设置当前已上传文件的下标信息
        var file = { index: this.fileNumber, name: spName[spName.length - 1], size: 0, url: url };
        // 放入上次已上传文件数组中
        this.loadedFiles.push(file);
        this.prevLoaderFiles.push(file);
        ++this.fileNumber;
        ++this.counter;
        // 添加文件 回调处理函数，设置文件预览等
        this.onAddPrevLoaderFile(file);
    }
    // @name 获取选中的文件
    // @param e object window
    // @return boolean
    this.findFiles = function(e) {
        var loaderMultiClass = this;
        // 从事件中获取选中的所有文件
        var files = e.target.files || e.dataTransfer.files;
        // 过滤重复的文件
        this.selectFiles = this.filterFiles(files);
        var oldFiles = [],
            newFiles = [],
            tmpFiles = [];
        // 获取新选择文件的名称数组
        $.each(this.selectFiles, function(k, v) {
            newFiles.push(v.name);
        });
        // 获取之前选择的文件的名称数组
        $.each(this.totalFiles, function(k, v) {
            oldFiles.push(v.name);
        });
        // 去重并保存
        $.each(newFiles, function(k, v) {
            if($.inArray(v, oldFiles) < 0) {
                tmpFiles.push(loaderMultiClass.selectFiles[k]);
            }
        });
        this.selectFiles = tmpFiles;
        if(this.counter + this.selectFiles.length > this.maxCounter) {
            this.selectFiles = [];
            // layer.msg('exceeding the maximum number of allowed uploads: ' + this.maxCounter);
            jQuery.warning('exceeding the maximum number of allowed uploads: ' + this.maxCounter);
            return false;
        }
        // 处理每个文件的下标
        this.dealtFiles();
    }
    // @name 处理每个文件的配置
    // @param this.selectFiles array 当前选中的文件集合
    // @return boolean
    this.dealtFiles = function() {
        var loaderMultiClass = this;
        $.each(this.selectFiles, function(k, v) {
            // 给每个文件定义下标
            v.index = loaderMultiClass.fileNumber;
            // 下标计数器+1
            ++loaderMultiClass.fileNumber;
            ++loaderMultiClass.counter;
        });
        // 添加文件到文件列表
        this.totalFiles = this.totalFiles.concat(this.selectFiles);
        // 添加文件到待上传文件列表
        this.waitLoaderFiles = this.waitLoaderFiles.concat(this.selectFiles);
        // 执行文件选择时的回调 onSelect，供外部实现文件预览操作
        this.onSelect(this.selectFiles, this.waitLoaderFiles);
        return true;
    }
    // @name 删除文件操作
    // @param index int 文件下标
    // @param isCallback boolean 是否执行文件删除回调函数
    // @return boolean
    this.deleteFile = function(index, isCallback) {
        var loaderMultiClass = this, file = [];
        // 遍历待上传文件数组，并删除下标为index的文件
        $.each(this.waitLoaderFiles, function(k, v) {
            if(index == v.index) {
                file = loaderMultiClass.waitLoaderFiles.splice(k, 1);
                return false;
            }
        });
        if(isCallback) {
            // 遍历所有文件数组， 并删除下标为index的文件
            $.each(this.totalFiles, function(k, v) {
                if(index == v.index) {
                    file = loaderMultiClass.totalFiles.splice(k, 1);
                    return false;
                }
            });
        }
        // 遍历前次上传文件数组， 并删除下标为index的文件
        $.each(this.prevLoaderFiles, function(k, v) {
            if(index == v.index) {
                file = loaderMultiClass.prevLoaderFiles.splice(k , 1);
                return false;
            }
        });
        --this.counter;
        if(isCallback && file.length) {
            // 执行回调删除方法，供外部进行删除效果的实现
            this.onDelete(file[0], this.waitLoaderFiles);
        }
        return true;
    }
    // @name 清空文件
    this.clearFiles = function() {
        var loaderMultiClass = this, file = [];
        // 遍历待上传文件数组，并删除下标为index的文件
        $.each(this.waitLoaderFiles, function(k, v) {
            file = loaderMultiClass.waitLoaderFiles.splice(k, 1);
            // 执行回调删除方法，供外部进行删除效果的实现
            loaderSingleClass.onDelete(file[0], loaderSingleClass.waitLoaderFiles);
        });
    }
    // @name 上传多个文件
    // @return boolean
    this.uploadFiles = function() {
        var loaderMultiClass = this;
        $.each(this.waitLoaderFiles, function(k, v) {
            loaderMultiClass.uploadFile(v);
        });
    }
    // @name 上传单个文件
    // @param file object 需要上传的文件
    // @return boolean
    this.uploadFile = function(file) {
        var loaderMultiClass = this;
        var form = new FormData();
        form.append('upload', file);
        form.append('_csrf', $('meta[name=csrf-token]').attr('content'));
        var xhr = new XMLHttpRequest();
        // 绑定上传事件
        // 绑定进度事件
        xhr.upload.addEventListener('progress', function(e) {
            // 进度改变回调
            loaderMultiClass.onProgress(file, e.loaded, e.total);
        }, false);
        // 绑定上传失败事件
        xhr.addEventListener('error', function(e) {
            // 失败回调
            loaderMultiClass.onFailure(file, xhr.responseText);
        }, false);
        // 绑定上传完成事件
        xhr.addEventListener('load', function(e) {
            var response = $.parseJSON(xhr.responseText);
            if (response == 'undefined' || response.code != 200) {
                // 回调失败函数
                loaderMultiClass.onFailure(file, xhr.responseText);
            }
            else {
                loaderMultiClass.deleteFile(file.index, false);
                // 回调成功函数
                loaderMultiClass.onSuccess(file, xhr.responseText);
            }
            if(loaderMultiClass.waitLoaderFiles.length <= 0) {
                // 回调上传完毕函数
                loaderMultiClass.onComplete('All done!');
            }
        }, false);
        xhr.open('POST', this.action, true);
        xhr.send(form);
    }
}