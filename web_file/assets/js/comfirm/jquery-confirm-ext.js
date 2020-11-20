/* 2017-08-23 by yuni
 * vision:v3.3.0
 * 必要檔案 Z:\catch-lottery\web_file\assets\plugin\js\jquery-confirm-master
 *
 * Custom extension method with jquery-confirm.
 * jquery-confirm.js must loaded before this.
 *
 * Example
 * ------------------------------------------------------
 * Change theme:
 * 		popup.theme.alert = 'light';
 *
 * Use return object:
 * 		var loader = popup.loading('hello', 'world');
 * 	 	loader.close();
 *
 * ------------------------------------------------------
 */
var jqConfirmExt = function() {
    var _this = this;
    var colClass = 'col-lg-4 col-lg-offset-4 col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2 col-xs-12 col-xs-offset-0';
    return {
        theme: {
            // 'light','dark','supervan','material','bootstrap'
            alert: 'material',
            confirm: 'material',
            dialog: 'material',
            block: 'supervan',
            loading: 'supervan'
        },
        alert: function(title, content, callback, options) {
            var opt = $.extend({}, {
                title: title || false,
                content: content || false,
                theme: popup.theme.alert,
                buttons: {
                    '關閉': {
                        btnClass: 'btn-primary',
                        action: callback || function() {}
                    }
                },
                columnClass: colClass,
                onOpen: function() {
                    $('button').focus();
                }
            }, options || {});

            return $.alert(opt);
        },
        alertSuccess: function(content, callback, options) {
            var opt = $.extend({}, {
                title: "成功" || "success",
                content: content || false,
                type: 'green',
                theme: popup.theme.alert,
                buttons: {
                    '關閉': {
                        btnClass: 'btn-primary',
                        action: callback || function() {}
                    }
                },
                columnClass: colClass,
                onOpen: function() {
                    $('button').focus();
                }
            }, options || {});

            return $.alert(opt);
        },
        alertFalid: function(content, callback, options) {
            var opt = $.extend({}, {
                title: "失敗" || "falid",
                content: content || false,
                type: 'red',
                theme: popup.theme.alert,
                buttons: {
                    '關閉': {
                        btnClass: 'btn-primary',
                        action: callback || function() {}
                    }
                },
                columnClass: colClass,
                onOpen: function() {
                    $('button').focus();
                }
            }, options || {});

            return $.alert(opt);
        },
        prompt: function(title, content, onConf, onCancel, options) {
            var opt = $.extend({}, {
                title: title || false,
                content: content || 　null,
                theme: popup.theme.confirm,
                icon: 'fa fa-plus-circle',
                backgroundDismiss: false,
                buttons: {
                    '送出': {
                    	 text: '送出',
                        btnClass: 'btn-blue',
                        action: onConf || function() {}
                    },
                    '關閉': {
                        btnClass: 'btn-primary',
                        action: onCancel || function() {}
                    }
                },
                columnClass: colClass,
                onOpen: function() {
                    $('button').focus();
                }
            }, options || {});

            return $.confirm(opt);
        },
        confirm: function(title, content, onConf, onCancel, options) {
            var opt = $.extend({}, {
                title: title || false,
                content: content || false,
                theme: popup.theme.confirm,
                icon: 'glyphicon glyphicon-question-sign',
                backgroundDismiss: false,
                buttons: {
                    '確定': {
                        btnClass: 'btn-primary',
                        action: onConf || function() {}
                    },
                    '取消': {
                        action: onCancel || function() {}
                    }
                },
                columnClass: colClass,
                onOpen: function() {
                    $('button').focus();
                }
            }, options || {});

            return $.confirm(opt);
        },
        dialog: function(title, content, open, close, options) {
            var opt = $.extend({}, {
                title: title || false,
                content: content || false,
                theme: popup.theme.dialog,
                backgroundDismiss: true,
                closeIcon: true,
                closeIconClass: 'fa fa-close',
                columnClass: colClass,
                onOpen: open || function() {},
                onClose: close || function() {}
            }, options || {});

            return $.dialog(opt);
        },
        block: function(title, content, open, close, options) {
            var opt = $.extend({}, {
                title: title,
                content: content,
                theme: popup.theme.block,
                backgroundDismiss: false,
                closeIcon: false,
                columnClass: colClass,
                onOpen: open || function() {},
                cancel: close || function() {}
            }, options || {});
            return $.dialog(opt);
        },
        loading: function(title, content, open, close, options) {
            var opt = $.extend({}, {
                title: title || 'Loading',
                content: content || false,
                theme: popup.theme.loading,
                icon: 'fa fa-spinner fa-spin',
                backgroundDismiss: false,
                closeIcon: false,
                columnClass: colClass,
                onOpen: open || function() {},
                cancel: close || function() {}
            }, options || {});

            return $.dialog(opt);
        },
        custom: function(title, content, onConf, options) {
            var opt = $.extend({}, {
                title: title,
                content: content,
                theme: popup.theme.confirm,
                backgroundDismiss: true,
                confirmButton: '確定',
                cancelButton: '取消',
                columnClass: colClass,
                confirm: onConf || function() {},
                cancel: function() {}
            }, options || {});

            return $.dialog(opt);
        }
    }
};
var popup = new jqConfirmExt();
