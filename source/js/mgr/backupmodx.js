let backupmodx = function (config) {
    config = config || {};
    Ext.applyIf(config, {});
    backupmodx.superclass.constructor.call(this, config);
    return this;
};
Ext.extend(backupmodx, Ext.Component, {
    config: {}, util: {},
    downloadTpl: new Ext.XTemplate(
        '<tpl for=".">',
        '<div>' +
        '<a class="downloadLink" onclick="BackupMODX.download(\'{filename}\',\'{name}\')">' +
        '<i class="icon icon-{icon}"></i> {label} <i>({size})</i> <i class="download icon icon-download"></i>' +
        '</a>' +
        '</div>',
        '</tpl>'
    ),
    restoreTpl: new Ext.XTemplate(
        '<tpl for=".">',
        '<div class="restoreItem">',
        '<div><strong>{date_format}</strong>' +
        '<tpl if="note != \'\'"> | {note:htmlEncode}</tpl>' +
        '</div>',
        '<label class="selectBox">',
        '<input type="radio" name="database" value="{name}">',
        '<div class="downloadLink"><i class="icon icon-sql"></i> {[_(\'backupmodx.database\')]} <i class="download icon icon-check"></i></div>',
        '</label>',
        '</div>',
        '</tpl>'
    ),
    templatingFiles: function (files) {
        const container = Ext.get('backupmodx-download-container');
        container.dom.innerText = '';
        Ext.each([
            {key: 'database', icon: 'sql', label: _('backupmodx.database')},
            {key: 'files', icon: 'zip', label: _('backupmodx.files')},
            {key: 'note', icon: 'txt', label: _('backupmodx.note')}
        ], function (type) {
            if (files[type.key] !== '') {
                this.downloadTpl.append(container, Ext.apply({icon: type.icon, label: type.label}, files[type.key]));
            }
        }, this);
    },
    backup: function () {
        const database = document.getElementById('backupmodx-input-database').checked,
            files = document.getElementById('backupmodx-input-files').checked,
            note = document.getElementById('backupmodx-input-note').value;

        if (!database && !files) {
            this.showMessage(_('backupmodx.err_missing_backup_options'), Ext.MessageBox.WARNING);
            return;
        }

        this.showParts(['spinner']);
        this.request({
            action: 'backup',
            database: database,
            files: files,
            note: note
        }, function (data) {
            if (data.success) {
                this.templatingFiles(data.results.files);
                Ext.get('backupmodx-remove-btn').removeClass('hide');
                this.showParts(['form-download']);
            } else {
                this.showError(data, Ext.MessageBox.ERROR);
                this.showParts(['form-backup']);
            }
        }, function () {
            this.showMessage(_('backupmodx.err_timeout'), Ext.MessageBox.ERROR);
            this.showParts(['form-backup']);
        }, ((BackupMODX.config.timelimit || 120) * 1000) + 1000);
    },
    restore: function () {
        this.showParts(['form-restore']);
        this.request({
            action: 'getbackups'
        }, function (data) {
            const container = Ext.get('backupmodx-restore-container');
            if (data.success) {
                if (data.results !== '') {
                    this.restoreTpl.overwrite(container, data.results);
                }
                Ext.get('backupmodx-restore-btn').removeClass('hide');
            } else {
                container.dom.innerText = data.message;
                Ext.get('backupmodx-restore-btn').addClass('hide');
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
        this.showParts(['form-backup']);
    },
    removeBackup: function () {
        this.request({
            action: 'removebackup'
        }, function (data) {
            if (Ext.isArray(data.results) && data.results.length === 0) {
                Ext.get('backupmodx-restore-container').dom.innerText = '';
                this.showParts(['form-backup']);
            }
        });
    },
    restoreBackup: function () {
        const database = Ext.DomQuery.selectNode('input[name=database]:checked', 'backupmodx-restore-container');
        if (!database) {
            this.showMessage(_('backupmodx.err_missing_restore_options'), Ext.MessageBox.WARNING);
            return;
        }

        this.request({
            action: 'restorebackup',
            database: database.value
        }, function (data) {
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
                this.showError(data, Ext.MessageBox.WARNING);
            }
        });
    },
    about: function () {
        const assetsUrl = BackupMODX.config.assetsUrl,
            msg = '<span style="display: inline-block; text-align: center">' +
                '<img width="200" style="margin: 0 50px;" src="' + assetsUrl + 'img/mgr/quadro.png" srcset="' + assetsUrl + 'img/mgr/quadro@2x.png 2x" alt="Quadro"><br>' +
                '<span style="display: block;margin-bottom: 20px">&copy; 2015-2026 by <a href="https://www.quadro-system.de" target="_blank">www.quadro-system.de</a></span>' +
                '<img width="200" src="' + assetsUrl + 'img/mgr/treehill-studio.png" srcset="' + assetsUrl + 'img/mgr/treehill-studio@2x.png 2x" alt="Treehill Studio"><br>' +
                'Version 3.x refactored by <a href="https://treehillstudio.com" target="_blank">treehillstudio.com</a>' +
                '</span>';
        Ext.Msg.show({
            title: _('backupmodx') + ' ' + BackupMODX.config.version,
            msg: msg,
            buttons: Ext.Msg.OK,
            cls: 'backupmodx_window',
            width: 358
        });
    },
    /**
     * Send a request to the BackupMODX connector
     *
     * @param {Object} params The request parameters
     * @param {Function} success Called with the decoded response data
     * @param {Function} [failure] Called on a failed request, defaults to an error message
     * @param {Number} [timeout] The request timeout in milliseconds
     */
    request: function (params, success, failure, timeout) {
        const options = {
            url: BackupMODX.config.connectorUrl,
            params: params,
            scope: this,
            success: function (response) {
                success.call(this, this.decode(response));
            },
            failure: failure || function (response) {
                this.showError(this.decode(response), Ext.MessageBox.ERROR);
            }
        };
        if (timeout) {
            options.timeout = timeout;
        }
        Ext.Ajax.request(options);
    },
    /**
     * Decode a connector response, an invalid response results in an empty object
     *
     * @param {Object} response
     * @returns {Object}
     */
    decode: function (response) {
        try {
            return Ext.decode(response.responseText) || {};
        } catch (e) {
            return {};
        }
    },
    showMessage: function (msg, icon) {
        Ext.Msg.show({
            title: _('backupmodx.err_msg_title'),
            msg: msg,
            buttons: Ext.MessageBox.OK,
            icon: icon
        });
    },
    showError: function (data, icon) {
        this.showMessage((data.message) ? data.message : _('backupmodx.err_unknown'), icon);
    },
    /**
     * Show the given widget parts and hide the other ones
     *
     * @param {Array} visible The ids of the visible parts without the backupmodx- prefix
     */
    showParts: function (visible) {
        Ext.each(['form-backup', 'form-download', 'form-restore', 'spinner'], function (part) {
            Ext.get('backupmodx-' + part)[(visible.indexOf(part) !== -1) ? 'removeClass' : 'addClass']('hide');
        });
    }
});
Ext.reg('backupmodx', backupmodx);
BackupMODX = new backupmodx();
