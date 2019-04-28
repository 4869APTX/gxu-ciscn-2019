/*!
 * static/js/webftp.nfs.js
 * NFS文件系统
 */
;
(function (window, app, $, undefined) {
    var nfs = {
        fschs : 'GB2312',
        ppath : '/',
        cpath : '/',
        _cache : {},
        _clipboard : {}
    };

    // 初始化NFS系统
    nfs.init = function () {
        app.log('app.nfs.init:', new Date().getTime());

        this.nlist('/', true);
    };

    nfs.auth = function (item) {
        if (-1 != $.inArray('*', app.api.auth) || -1 != $.inArray(item, app.api.auth)) {
            return true;
        }
        return false;
    };

    // 文件列表
    nfs.nlist = function (path, force) {
        app.log('app.nfs.nlist:', path, force);
        app.mui.loading(1);

        // 校验路径合法性
        var path = path || '/';
        if (path.charAt(0) != '/') {
            path = '/' + path;
        }
        if (path.charAt(path.length - 1) != '/') {
            path += '/';
        }

        var phash = app.util.md5(path);
        if (!force && this._cache[phash]) {
            var data = this._cache[phash];
            app.nfs.ppath = data.path.parent;
            app.nfs.cpath = data.path.current;
            app.mui.display(data);
            return;
        }

        app.ajaxRequest('nfs', 'nlist', {
            'fs-path' : path,
            'fs-otype' : app.mui.otype,
            'fs-osort' : app.mui.osort
        }, function (json) {
            if (200 == json.code) {
                json.data.path.WF_FURI = window.WF_FURI;
                app.nfs.ppath = json.data.path.parent;
                app.nfs.cpath = json.data.path.current;
                app.nfs._cache[phash] = json.data;
                app.mui.display(json.data);
            } else {
                app.mui.loading(0);
                $.dialog.alert(json.message);
            }
        }, false);

    };

    // 新建目录
    nfs.mkdir = function (type) {
        if (!app.nfs.auth('mkdir')) {
            $.dialog.alert('管理员没有授予权限：新建文件');
            return;
        }

        var szMsg = '[\\/:*?"\'<>|：？“’《》]';
        $.dialog.prompt('请输入文件名', function (name) {
            if (!name) {
                $.dialog.alert('请输入文件名');
                return false;
            }
            for (var i = 1, achar; i < szMsg.length + 1; i++) {
                achar = szMsg.substring(i - 1, i);
                if (name.indexOf(achar) > -1) {
                    $.dialog.alert('文件名包含非法字符：' + achar, 'error');
                    return false;
                }
            }

            app.log('app.nfs.mkdir:', name);
            app.ajaxRequest('nfs', 'mkdir', {
                'fs-type' : type || 'dir',
                'fs-path' : app.nfs.cpath + name
            }, function (json) {
                if (200 != json.code) {
                    $.dialog.alert(json.message);
                    return;
                }
                $.dialog.tips('新建成功');
                app.nfs.nlist(app.nfs.cpath, true);
            }, false);
        }, 'file' == type ? '新建文本文档.txt' : '新建文件夹');
    };

    // 删除文件夹、文件
    nfs.rmdir = function (path, name, type) {
        if (!app.nfs.auth('rmdir')) {
            $.dialog.alert('管理员没有授予权限：删除文件');
            return;
        }

        app.mui.loading(1);
        if (false === path) {
            path = app.mui.SelectCheck();
            name = '[' + path.length + ']项';
            type = 'all';
        }

        art.dialog.confirm('你确定要删除 < ' + name + ' > 吗？', function () {
            app.ajaxRequest('nfs', 'rmdir', {
                'fs-path' : path,
                'fs-name' : name,
                'fs-type' : type

            }, function (json) {
                if (200 != json.code) {
                    $.dialog.alert(json.message);
                    return;
                }

                if ('file' == type) {
                    $.dialog.tips('文件删除成功', 1.5);
                    app.nfs.nlist(app.nfs.cpath, true);
                    return;
                }
                var message = juicer('#app-nfs-del', json);
                $.each(json.data.el, function (idx, item) {
                    app.nfs._cache[app.util.md5(item)] = null; // 清空删除失败的缓存数据
                });

                $.dialog.alert(message);
                app.mui.loading(0);
                app.nfs.nlist(app.nfs.cpath, json.data.el ? true : false);
            }, false);
        }, function () {
            app.mui.loading(0);
        });

    }

    // 重命名
    nfs.rename = function ($dom, path, oldname, type) {
        var szMsg = '[\\/:*?"\'<>|：？“’《》]';
        $.dialog.prompt('请输入文件名', function (newname) {
            if (!newname) {
                $.dialog.alert('文件名不能为空');
                return false;
            }
            if (newname == oldname) {
                return;
            }

            for (i = 1; i < szMsg.length + 1; i++) {
                if (name.indexOf(szMsg.substring(i - 1, i)) > -1) {
                    $.dialog.alert('文件名包含非法字符', 'error');
                    return false;
                }
            }

            app.ajaxRequest('nfs', 'rename', {
                'fs-path' : path,
                'fs-oname' : oldname,
                'fs-nname' : newname
            }, function (json) {
                json.name = newname;
                var message = juicer('#app-nfs-rename', json);
                if (200 != json.code) {
                    $.dialog.alert(message);
                    return;
                }
                $.dialog.tips(message);
                app.nfs.nlist(app.nfs.cpath, true);
            }, false);
        }, oldname);
    }

    // 目录属性
    nfs.pathinfo = function () {
        if (!app.nfs.auth('pathinfo')) {
            $.dialog.alert('管理员没有授予权限：目录属性');
            return;
        }

        app.mui.loading(1);
        app.ajaxRequest('nfs', 'pathinfo', {
            'fs-path' : app.nfs.cpath
        }, function (json) {
            var message = juicer('#app-nfs-pathinfo', json);
            if (200 != json.code)
                $.dialog.alert(message, 'error');
            else
                $.dialog.alert(message);
            app.mui.loading(0);
        }, false);
    }

    // 文件夹压缩
    nfs.zip = function (path, name) {
        art.dialog.confirm('打包文件夹可能耗时较长，请耐心等待。', function () {
            app.mui.loading(1);
            app.ajaxRequest('nfs', 'zip', {
                'fs-path' : path,
                'fs-name' : name
            }, function (json) {
                app.mui.loading(0);
                var message = juicer('#app-nfs-zip', json);
                if (200 != json.code) {
                    $.dialog.alert(message, 'error');
                    return;
                }

                $.dialog.alert(message);
                app.nfs.nlist(app.nfs.cpath, true);
            }, false);

        });
    }

    nfs.unzip = function (path, name) {
        art.dialog.confirm('解压文件可能耗时较长，请耐心等待。', function () {
            app.mui.loading(1);
            app.ajaxRequest('nfs', 'unzip', {
                'fs-path' : path,
                'fs-name' : name
            }, function (json) {
                app.mui.loading(0);
                var message = juicer('#app-nfs-unzip', json);
                if (200 != json.code) {
                    $.dialog.alert(message, 'error');
                    return;
                }

                $.dialog.alert(message);
                app.nfs.nlist(app.nfs.cpath, true);
            }, false);

        });
    }

    // 下载
    nfs.download = function (path, name, type) {
        if ('dir' == type) {
            art.dialog.confirm('下载文件夹可能耗时较长，确定要下载？', function () {
                var url = app.api.url + '?key=' + app.api.key + '&mod=nfs&act=download&fs-type=' + type + '&fs-path=' + path + '&fs-name=' + name;
                $('#app-nfs-down').attr('src', url);
            });
        } else {
            var url = app.api.url + '?key=' + app.api.key + '&mod=nfs&act=download&fs-type=' + type + '&fs-path=' + path + '&fs-name=' + name;
            $('#app-nfs-down').attr('src', url);
        }
    };

    // 文件上传
    nfs.upload = function () {
        if (!app.nfs.auth('upload')) {
            $.dialog.alert('管理员没有授予权限：文件上传');
            return;
        }

        var iframe = {};
        var dialog = $.dialog.open('upload.php', {
                id : 'upload',
                title : '加载中...',
                width : 660,
                height : 415,
                resize : false,
                fixed : true,
                lock : true,
                init : function () {
                    iframe = this.iframe.contentWindow;
                    iframe.app.api.path = app.nfs.cpath;
                    iframe.app.upload.instance();
                },
                button : [{
                        name : '覆盖模式',
                        focus : true,
                        disabled : false,
                        callback : function () {
                            art.dialog.confirm('覆盖模式将直接覆盖同名文件，确定吗？', function () {
                                iframe.app.upload.setcover();
                            });
                            return false;
                        }
                    }, {
                        name : '继续上传',
                        focus : false,
                        disabled : true,
                        callback : function () {
                            iframe.app.upload.instance();
                            return false;
                        }
                    }, {
                        name : '关闭',
                        disabled : false,
                        callback : function () {
                            app.mui.refresh(true);
                            return true;
                        }
                    }
                ]
            }, true);
    }

    // 更改权限
    nfs.chmod = function (path, name, chmod, type) {
        $.dialog({
            content : juicer('#app-nfs-chmod', {
                'path' : path,
                'name' : name,
                'type' : type,
                'chmod' : chmod
            }),
            lock : true,
            fixed : true,
            padding : 0,
            init : function () {
                set_chmod_deep(chmod)
            },
            ok : function () {
                app.nfs._cache[app.util.md5(path)] = null;
                var args = get_chmod_num();
                app.ajaxRequest('nfs', 'chmod', {
                    'fs-path' : path,
                    'fs-name' : name,
                    'fs-chmod' : args[1],
                    'fs-deep' : args[0]
                }, function (json) {
                    app.mui.loading(0);
                    json.data.name = name;
                    json.data.chmod = chmod + '=>' + args[1];
                    var message = ('dir' == type && '1' == args[0]) ? juicer('#app-nfs-chmod-x', json) : (json.message || '权限修改成功');
                    if (200 != json.code) {
                        $.dialog.alert(message, 'error');
                        return;
                    }

                    $.dialog.alert(message);
                    app.nfs.nlist(app.nfs.cpath, true);
                }, false);
            }
        });
    }

    // 复制、剪切、粘贴
    nfs.cut = function (path, name, type) {
        this._clipboard.mode = 'cut';
        this._clipboard.path = app.nfs.cpath;
        this._clipboard.time = (new Date()).getTime();
        this._clipboard.list = path ? [path] : app.mui.SelectCheck();

        if (!this._clipboard.list.length) {
            return;
        }
        $.dialog.notice({
            title : '剪切 - 剪贴板',
            icon : 'succeed',
            width : 220,
            time : 2,
            content : '【' + this._clipboard.list.length + '】项已复制到剪贴板'
        });
    };
    nfs.copy = function (path, name, type) {
        this._clipboard.mode = 'copy';
        this._clipboard.path = app.nfs.cpath;
        this._clipboard.time = (new Date()).getTime();
        this._clipboard.list = path ? [path] : app.mui.SelectCheck();

        if (!this._clipboard.list.length) {
            return;
        }
        $.dialog.notice({
            title : '复制 - 剪贴板',
            icon : 'succeed',
            width : 220,
            time : 2,
            content : '【' + this._clipboard.list.length + '】项已复制到剪贴板'
        });
    };
    nfs.paste = function (force) {
        if (!app.nfs.auth('paste')) {
            $.dialog.alert('管理员没有授予权限：文件剪切、复制、粘贴');
            return;
        }

        // 目标文件夹是源文件夹的子文件夹 跳过
        var time = (new Date()).getTime();
        if ((time - this._clipboard.time) > 30 * 1000) {
            $.dialog.notice({
                title : '提示 - 剪贴板',
                icon : 'error',
                width : 180,
                time : 2,
                content : '超过30秒未操作，请重新选择'
            });
            return;
        } else if (!this._clipboard.list || this._clipboard.list.length < 1) {
            $.dialog.notice({
                title : '提示 - 剪贴板',
                icon : 'error',
                width : 180,
                time : 2,
                content : '剪贴板还没有数据，请重新选择'
            });
            return;
        }

        this._cache[app.util.md5(this._clipboard.path)] = null;
        this._clipboard.path = app.nfs.cpath;
        this._clipboard.force = force;
        $.each(this._clipboard.list, function (idx, item) {
            if (app.nfs._clipboard.path.indexOf(item) > -1) {
                $.dialog.notice({
                    title : '提示 - 剪贴板',
                    icon : 'error',
                    width : 180,
                    time : 2,
                    content : '目标文件夹是源文件夹的子文件夹，粘贴操作已取消'
                });

                app.nfs._cache[app.util.md5(item)] = null;
                app.nfs._clipboard = {};
                return false;
            }
        });
        if (this._clipboard.list && this._clipboard.list.length > 0) {
            this._dopaste();
            this._clipboard = {};
        }
    }
    nfs._dopaste = function () {
        app.mui.loading(0);
        var data = app.nfs._clipboard;
        app.ajaxRequest('nfs', 'paste', data, function (json) {
            var message = juicer('#app-nfs-paste', {
                    'data' : data,
                    'json' : json
                });
            if (200 != json.code) {
                $.dialog.alert(message, 'error');
                return;
            }

            $.dialog.alert(message || 'ok');
            app.nfs.nlist(app.nfs.cpath, true);
        }, true);
        return;
    };

    app.nfs = nfs;
})(window, app, jQuery);
