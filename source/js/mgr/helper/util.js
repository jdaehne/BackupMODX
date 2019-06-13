BackupMODX.util.HiddenForm = function (url, fields) {
    if (!Ext.isObject(fields))
        return;
    var body = Ext.getBody(),
        frame = body.createChild({
            tag: 'iframe',
            cls: 'x-hidden',
            id: 'hiddenform-iframe',
            name: 'iframe'
        }),
        form = body.createChild({
            tag: 'form',
            cls: 'x-hidden',
            id: 'hiddenform-form',
            action: url,
            target: 'iframe',
            method: 'post'
        });

    Ext.iterate(fields, function (name, value) {
        form.createChild({
            tag: 'input',
            type: 'text',
            cls: 'x-hidden',
            id: 'hiddenform-' + name,
            name: name,
            value: value
        });
    });

    form.dom.submit();
};

