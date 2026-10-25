BackupMODX.util.HiddenForm = function (url, fields) {
    if (!Ext.isObject(fields)) {
        return;
    }

    // Use a separate iframe for each call to keep running downloads alive
    const target = Ext.id(null, 'hiddenform-iframe-'),
        body = Ext.getBody(),
        form = body.createChild({
            tag: 'form',
            cls: 'x-hidden',
            action: url,
            target: target,
            method: 'post'
        });
    body.createChild({
        tag: 'iframe',
        cls: 'x-hidden',
        id: target,
        name: target
    });

    Ext.iterate(fields, function (name, value) {
        form.createChild({
            tag: 'input',
            type: 'hidden',
            name: name,
            value: value
        });
    });

    form.dom.submit();
    form.remove();
};
