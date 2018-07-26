
jQuery.warning = function(message) {
    return BootstrapDialog.show({
        type : BootstrapDialog.TYPE_DANGER,
        title : '<i class="fa fa-times fa-fw"></i>EXCEPTION',
        message : message,
        size : BootstrapDialog.SIZE_SMALL,
    });
};

jQuery.success = function(message) {
    return BootstrapDialog.show({
        type : BootstrapDialog.TYPE_SUCCESS,
        title : '<i class="fa fa-check fa-fw"></i>SUCCESS',
        message : message,
        size : BootstrapDialog.SIZE_SMALL,
    });
};

jQuery.alert = function(message, _function) {
    return BootstrapDialog.show({
        type : BootstrapDialog.TYPE_PRIMARY,
        title : '<i class="fa fa-gears fa-fw"></i>OPERATE',
        message : message,
        size : BootstrapDialog.SIZE_SMALL,
        buttons : [ {
            label : 'close',
            action : function(dialog) {
                dialog.close();
            }
        } ],
        onhide : _function
    });
};

jQuery.loading = function(_function) {
    return BootstrapDialog.show('loading...', _function);
};
jQuery.loaded = function(dialog, _function) {
    dialog.close();
    if(_function) {
        _function.call();
    }
}

jQuery.dialog = function(title, message, options) {
    var _option = jQuery.extends(true, {
        type: BootstrapDialog.TYPE_DEFAULT,
        title: title, cssClass: 'fade',
        size: BootstrapDialog.SIZE_LARGE,
        closable: true,
        message: message
    }, options);
    if(message.indexOf('http') == 0) {
        _option = jQuery.extends(true, _option, {
            message: function (dialog) {
                var $message = $('<div></div>');
                var pageToLoad = dialog.getData('pageToLoad');
                $message.load(pageToLoad);
                return $message;
            },
            data: {pageToLoad: url}
        });
    }
    return BootstrapDialog.show(_option);
};

jQuery.confirm = function(_function, message, title, _close) {
    if ( ! title) {
        title = 'OPERATION';
    }
    if ( ! message) {
        message = 'are you sure you want to perform this operation?';
    }
    return BootstrapDialog.confirm({
        type: BootstrapDialog.TYPE_WARNING,
        title: title,
        message: message,
        closable: true,
        btnCancelLabel: 'cancel',
        btnOKLabel: 'confirm',
        onhide: _close,
        callback: function (result) {
            if ( ! result) {
                return false;
            }
            _function.call();
            return true;
        }
    });
};