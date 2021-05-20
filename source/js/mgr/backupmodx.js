var backupmodx = function (config) {
    config = config || {};
    Ext.applyIf(config, {});
    backupmodx.superclass.constructor.call(this, config);
    return this;
};
Ext.extend(backupmodx, Ext.Component, {
    config: {}, util: {},
    initComponent: function () {
        this.stores = {};
        this.ajax = new Ext.data.Connection({
            disableCaching: true
        });
    },
    templatingFiles: function (files) {
        var tpl = new Ext.XTemplate(
            '<tpl for=".">',
            '<div>' +
            '<a class="downloadLink" onclick="BackupMODX.download(\'{filename}\',\'{name}\')">' +
            '<i class="icon icon-{icon}"></i> {label} <i>({size})</i> <i class="download icon icon-download"></i>' +
            '</a>' +
            '</div>',
            '</tpl>'
        );
        Ext.get('backupmodx-download-container').dom.innerText = '';
        if (files.database !== '') {
            files.database['icon'] = 'sql';
            files.database['label'] = 'Database';
            tpl.append(Ext.get('backupmodx-download-container'), files.database);
        }
        if (files.files !== '') {
            files.files['icon'] = 'zip';
            files.files['label'] = 'Files';
            tpl.append(Ext.get('backupmodx-download-container'), files.files);
        }
        if (files.note !== '') {
            files.note['icon'] = 'txt';
            files.note['label'] = 'Note';
            tpl.append(Ext.get('backupmodx-download-container'), files.note);
        }
    },
    backup: function () {
        var database = document.getElementById('backupmodx-input-database').checked,
            files = document.getElementById('backupmodx-input-files').checked,
            note = document.getElementById('backupmodx-input-note').value;

        if (database || files) {
            Ext.get('backupmodx-form-backup').addClass('hide');
            Ext.get('backupmodx-spinner').removeClass('hide');
            Ext.Ajax.request({
                url: BackupMODX.config.connectorUrl,
                timeout: ((BackupMODX.config.timelimit || 120) * 1000) + 1000,
                params: {
                    action: 'backup',
                    database: database,
                    files: files,
                    note: note
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);
                    if (data.success) {
                        BackupMODX.templatingFiles(data.results.files);
                        Ext.get('backupmodx-remove-btn').removeClass('hide');
                        Ext.get('backupmodx-form-download').removeClass('hide');
                    } else {
                        Ext.Msg.show({
                            title: _('backupmodx.err_msg_title'),
                            msg: (data.message) ? data.message : _('backupmodx.err_unknown'),
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.ERROR
                        });
                        Ext.get('backupmodx-form-backup').removeClass('hide');
                    }
                    Ext.get('backupmodx-spinner').addClass('hide');
                },
                failure: function (response) {
                    var data = Ext.decode(response.responseText);
                    Ext.Msg.show({
                        title: _('backupmodx.err_msg_title'),
                        msg: _('backupmodx.err_timeout'),
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.ERROR
                    });
                    Ext.get('backupmodx-spinner').addClass('hide');
                    Ext.get('backupmodx-form-backup').removeClass('hide');
                }
            });
        } else {
            Ext.Msg.show({
                title: _('backupmodx.err_msg_title'),
                msg: _('backupmodx.err_missing_backup_options'),
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.WARNING
            });
        }
    },
    restore: function () {
        Ext.get('backupmodx-form-backup').addClass('hide');
        Ext.get('backupmodx-form-download').addClass('hide');
        Ext.get('backupmodx-form-restore').removeClass('hide');

        Ext.Ajax.request({
            url: BackupMODX.config.connectorUrl,
            params: {
                action: 'getbackups'
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);
                if (data.success) {
                    var files = data.results,
                        tpl = new Ext.XTemplate(
                            '<tpl for=".">',
                            '<div class="restoreItem">',
                            '<div><strong>{date_format}</strong>' +
                            '<tpl if="note != \'\'"> | {note}</tpl>' +
                            '</div>',
                            '<label class="selectBox">',
                            '<input type="radio" name="database" value="{name}">',
                            '<div class="downloadLink"><i class="icon icon-sql"></i> ' + _('backupmodx.database') + ' <i class="download icon icon-check"></i></div>',
                            '</label>',
                            '</div>',
                            '</tpl>'
                        );
                    if (files !== '') {
                        tpl.overwrite(Ext.get('backupmodx-restore-container'), files);
                    }
                    Ext.get('backupmodx-restore-btn').removeClass('hide');
                } else {
                    Ext.get('backupmodx-restore-container').dom.innerText = data.message;
                    Ext.get('backupmodx-restore-btn').addClass('hide');
                }
            },
            failure: function (response) {
                var data = Ext.decode(response.responseText);
                Ext.Msg.show({
                    title: _('backupmodx.err_msg_title'),
                    msg: (data.message) ? data.message : _('backupmodx.err_unknown'),
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
        });
    },
    download: function (file, folder) {
        BackupMODX.util.HiddenForm(BackupMODX.config.connectorUrl, {
            action: 'download',
            file: file,
            folder: folder,
            HTTP_MODAUTH: MODx.siteId
        });
    },
    cancel: function () {
        Ext.get('backupmodx-form-backup').removeClass('hide');
        Ext.get('backupmodx-form-download').addClass('hide');
        Ext.get('backupmodx-form-restore').addClass('hide');
    },
    removeBackup: function () {
        Ext.Ajax.request({
            url: BackupMODX.config.connectorUrl,
            params: {
                action: 'removebackup'
            },
            success: function (response) {
                var data = Ext.decode(response.responseText),
                    files = data.results;
                if (typeof files != "undefined" && files != null && files.length != null && files.length === 0) {
                    Ext.get('backupmodx-restore-container').dom.innerText = '';
                    Ext.get('backupmodx-form-backup').removeClass('hide');
                    Ext.get('backupmodx-form-download').addClass('hide');
                }
            },
            failure: function (response) {
                var data = Ext.decode(response.responseText);
                Ext.Msg.show({
                    title: _('backupmodx.err_msg_title'),
                    msg: (data.message) ? data.message : _('backupmodx.err_unknown'),
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            }
        });
    },
    restoreBackup: function () {
        var database = Ext.DomQuery.selectNode('input[name=database]:checked');
        if (database) {
            Ext.Ajax.request({
                url: BackupMODX.config.connectorUrl,
                params: {
                    action: 'restorebackup',
                    database: database.value
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);
                    if (data.success) {
                        Ext.Msg.show({
                            title: _('backupmodx.success_msg_title'),
                            msg: _('backupmodx.success_restore'),
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.INFO,
                            fn: function () {
                                location.reload();
                            }
                        });
                    } else {
                        Ext.Msg.show({
                            title: _('backupmodx.err_msg_title'),
                            msg: (data.message) ? data.message : _('backupmodx.err_unknown'),
                            buttons: Ext.MessageBox.OK,
                            icon: Ext.MessageBox.WARNING
                        });
                    }
                },
                failure: function (response) {
                    var data = Ext.decode(response.responseText);
                    Ext.Msg.show({
                        title: _('backupmodx.err_msg_title'),
                        msg: (data.message) ? data.message : _('backupmodx.err_unknown'),
                        buttons: Ext.MessageBox.OK,
                        icon: Ext.MessageBox.ERROR
                    });
                }
            });
        } else {
            Ext.Msg.show({
                title: _('backupmodx.err_msg_title'),
                msg: _('backupmodx.err_missing_restore_options'),
                buttons: Ext.MessageBox.OK,
                icon: Ext.MessageBox.WARNING
            });
        }
    },
    about: function () {
        var msg = '<span style="display: inline-block; text-align: center">' +
            '<img width="200" style="margin: 0 50px;" src="' + BackupMODX.config.assetsUrl + 'img/mgr/quadro.png" srcset="' + BackupMODX.config.assetsUrl + 'img/mgr/quadro@2x.png 2x" alt"Quadro"><br>' +
            '<span style="display: block;margin-bottom: 20px">© 2015-2021 by <a href="https://www.quadro-system.de" target="_blank">www.quadro-system.de</a></span>' +
            '<img width="200" src="' + BackupMODX.config.assetsUrl + 'img/mgr/treehill-studio.png" srcset="' + BackupMODX.config.assetsUrl + 'img/mgr/treehill-studio@2x.png 2x" alt="Treehill Studio"><br>' +
            'Version 3.x refactored by <a href="https://treehillstudio.com" target="_blank">treehillstudio.com</a>' +
            '</span>';
        Ext.Msg.show({
            title: _('backupmodx') + ' ' + BackupMODX.config.version,
            msg: msg,
            buttons: Ext.Msg.OK,
            cls: 'backupmodx_window',
            width: 330
        });
    }
});
Ext.reg('backupmodx', backupmodx);
BackupMODX = new backupmodx();
