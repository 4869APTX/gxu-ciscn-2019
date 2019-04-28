/*!
 * static/js/webftp.core.js
 * Core模块
 */

'use strict';
(function (window, $, undefined) {
    var app = function () {
        this.api = {
            key : '',
            url : ''
        };
        this.lang = 'zh_CN';
        this.debug = false;
    }

    // 获取语言
    app.getLang = function (item, lang) {
        return;
    };

    app.log = function () {
        if (!this.debug)
            return;
        if (console)
            console.log.apply(console, arguments);
        // console.log(arguments[0], arguments[1]);
    }

    app.dir = function () {
        if (!this.debug)
            return;
        if (console)
            console.dir.apply(console, arguments);
        // console.dir(arguments);
    };

    // Ajax请求接口
    app.ajaxRequest = function (mod, act, data, callback, sync, url) {
        var url = url || this.api.url;

        $.ajax({
            url : url + '?mod=' + mod + '&act=' + act + '&isajax=true&key=' + this.api.key, //&callback=?
            data : data, cache: false,
            context : this,
            async : !sync,
            dataType : 'json',
            success : callback,
            error : function (xhr, textStatus, thrownError) {
                var msg = '';
                if ('timeout' == textStatus) {
                    msg += '<div>Http status: Ajax请求超时</div>';
                } else if ('error' == textStatus) {
                    msg += '<div>Http status:  ' + xhr.status + ' ' + xhr.statusText + '</div>';
                    msg += '<div>Http readyState:  ' + xhr.readyState + '</div>';
                    msg += '<div>thrownError:  ' + thrownError + '</div>';
                    msg += '<div>responseText: ' + xhr.responseText + '</div>';
                } else {
                    msg += textStatus + ':' + thrownError;
                }
                $.dialog.alert(msg).title('请求出错');
                window.app.mui.loading(0);
            }
        });
    };

    app.prototype = app;
    window.app = new app();
})(window, jQuery);
