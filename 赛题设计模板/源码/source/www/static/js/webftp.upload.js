/*!
 * static/js/webftp.upload.js
 * NFS文件系统API接口
 */
;
(function (window, app, $, undefined) {
    var upload = {
        _instance : null
    };

    // 初始化NFS系统
    upload.init = function () {
        app.log('app.upload.init:', new Date().getTime());
    };

    upload.instance = function () {
        app.log('app.upload.instance:', new Date().getTime());

        if (this._instance) {
            this._instance.destroy();
        }

        $('#uploader').pluploadQueue({
            // General settings
            runtimes : 'html5,flash,html4', //
            url : app.api.url + '&key=' + app.api.key,

            // 自定义的插入http请求的键值对
            headers : {},
            chunk_size : '2M',
            max_file_size : '10M',

            // 上传之前可以重命名文件
            rename : true,

            // 是否生成唯一的文件名，避免与服务器文件重名
            unique_names : false,

            // 多选对话框
            multi_selection : true,

            // 是否可以多次上传
            multiple_queues : true,

            // 重新调整图片大小
            // resize : {  width : 320,  height : 240, quality : 90},
            flash_swf_url : 'static/plupload/plupload.swf',

            filters : [{
                    title : 'All Files (*.rar;*.htm;*.jpg;*.pdf;*.doc;*.*)',
                    extensions : '*,rar,zip,tar,gz,7z,php,js,css,htm,html,xml,jpg,png,gif,bmp,ico,pdf,doc,ppt,xls,docx,pptx,xlsx,wps,et,dps'
                }, {
                    title : 'Archive Files (*.rar;*.zip;*.tar;*.gz;*.7z)',
                    extensions : 'rar,zip,tar,gz,7z'
                }, {
                    title : 'Script Files (*.php;*.js;*.css;*.htm;*.xml)',
                    extensions : 'php,js,css,htm,html,xml'
                }, {
                    title : 'Images Files (*.jpg;*.png;*.gif;*.bmp;*.ico)',
                    extensions : 'jpg,png,gif,bmp,ico'
                }, {
                    title : 'Document Files (*.doc;*.ppt;*.xls;*.pdf;wps;*.et;*.dps)',
                    extensions : 'pdf,doc,ppt,xls,docx,pptx,xlsx,wps,et,dps'
                }
            ],
            // PreInit events, bound before any internal events
            preinit : {
                Init : function (up, info) {
                    up.settings.multipart_params = {
                        'webftp_sessid' : app.api.webftp_sessid,
                        'webftp_uname' : app.api.webftp_uname,
                        'webftp_tokey' : app.api.webftp_tokey
                    };
                    up.settings.max_file_size = parseInt(app.settings.max_file_size) * 1024 * 1024;
                    up.settings.chunk_size = parseInt(app.settings.chunk_size) * 1024 * 1024;
                    app.log('Current runtime:', info.runtime, '[Info]:', info, '[Upload]:', up.settings);
                },

                UploadFile : function (up, file) {
                    // You can override settings before the file is uploaded
                    // up.settings.url = 'upload.php?id=' + file.id;

                    up.settings.url = app.api.url + '&key=' + app.api.key + '&fs-path=' + app.api.path + '&fs-cover=' + app.api.cover;
                    app.log('[preinit.UploadFile]:', up, file);
                }
            },

            // Post init events, bound after the internal events
            init : {
                // Called when upload shim is moved
                Refresh : function (up) {
                    app.log('[Refresh]');
                },

                // Called when the state of the queue is changed
                StateChanged : function (up) {
                    app.log('[StateChanged]', up.state == plupload.STARTED ? 'STARTED' : 'STOPPED');
                    // if (up.state == plupload.STOPPED) up.init();
                },

                // Called when the files in queue are changed by adding/removing files
                QueueChanged : function (up) {
                    app.log('[QueueChanged]', up);
                },

                // Callced when files are added to queue
                FilesAdded : function (up, files) {
                    app.log('[FilesAdded]');

                    plupload.each(files, function (file) {
                        app.log('  File:', file);
                    });

                    window.dialog.button({
                        name : '关闭',
                        disabled : true
                    });
                    window.dialog.button({
                        name : '继续上传',
                        disabled : true
                    });
                },

                // Called when files where removed from queue
                FilesRemoved : function (up, files) {
                    app.log('[FilesRemoved]');
                    plupload.each(files, function (file) {
                        app.log('  File:', file);
                    });
                },

                // Called while a file is being uploaded
                UploadProgress : function (up, file) {
                    app.log('[UploadProgress]', 'File:', file, 'Total:', up.total);
                },

                // Called when a file has finished uploading
                FileUploaded : function (up, file, info) {
                    info.response = eval('(' + info.response + ')');
                    if (200 != info.response.code) {
                        up.stop();
                        top.$.dialog.alert(info.response.message);
                    }
                    app.log('[FileUploaded] File:', file, 'Info:', info);
                },

                // Called when a file chunk has finished uploading
                ChunkUploaded : function (up, file, info) {
                    info.response = eval('(' + info.response + ')');
                    if (200 != info.response.code) {
                        up.stop();
                        top.$.dialog.alert(info.response.message);
                    }
                    app.log('[ChunkUploaded] File:', file, 'Info:', info);
                },

                UploadComplete : function (up, files) {
                    app.log('[UploadComplete] Files:', files);
                    window.dialog.button({
                        name : '关闭',
                        disabled : false
                    });
                    window.dialog.button({
                        name : '继续上传',
                        disabled : false,
                        focus : true
                    });
                },

                // Called when a error has occured
                Error : function (up, args) {
                    top.$.dialog.alert(args.code + ':' + args.message);
                    if (args.file) {
                        app.log('[error]', args, 'File:', args.file);
                    } else {
                        app.log('[error]', args);
                    }
                }
            }
        });

        this._instance = $('#uploader').pluploadQueue();
        return this._instance;
    };

    upload.setcover = function () {
        app.api.cover = 1;
        window.dialog.button({
            name : '覆盖模式',
            disabled : true,
            focus : false
        });
        window.dialog.button({
            name : '继续上传',
            disabled : false,
            focus : true
        });
    };

    app.upload = upload;
})(window, app, jQuery);
