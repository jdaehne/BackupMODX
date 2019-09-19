<div class="backupmodxWidget">
    <form class="hide" method="post" onsubmit="BackupMODX.removeBackup();return false;" id="backupmodx-form-download">
        <h3 style="color:grey">[[%backupmodx.msg_finished? &namespace=`backupmodx`]]</h3>
        <p>[[%backupmodx.download? &namespace=`backupmodx`]]:</p>
        <div class="downloads" id="backupmodx-download-container"></div>
        <button class="x-btn primary-button" type="submit" id="backupmodx-remove-btn">[[%backupmodx.btn_remove? &namespace=`backupmodx`]]</button>
        <button class="x-btn" onclick="BackupMODX.restore();return false">[[%backupmodx.btn_restore? &namespace=`backupmodx`]]</button>
        <button class="x-btn" onclick="BackupMODX.cancel();return false">[[%backupmodx.btn_cancel? &namespace=`backupmodx`]]</button>
    </form>

    <form class="hide" method="post" onsubmit="BackupMODX.restoreBackup();return false;" id="backupmodx-form-restore">
        <p>[[%backupmodx.restore? &namespace=`backupmodx`]]:</p>
        <div class="restore" id="backupmodx-restore-container"></div>
        <button class="x-btn primary-button" type="submit" id="backupmodx-restore-btn">[[%backupmodx.btn_restore? &namespace=`backupmodx`]]</button>
        <button class="x-btn" onclick="BackupMODX.cancel();return false">[[%backupmodx.btn_cancel? &namespace=`backupmodx`]]</button>
    </form>

    <div id="backupmodx-spinner" class="spinnerWrapper hide">
        <div class="spinner">
            <i class="icon icon-modx"></i>
            <i class="icon icon-modx"></i>
            <i class="icon icon-modx"></i>
        </div>
        <div class="text">
            <h3>[[%backupmodx.msg_progress_title? &namespace=`backupmodx`]]</h3>
            <p>[[%backupmodx.msg_progress_text? &namespace=`backupmodx`]]</p>
        </div>
    </div>

    <form onsubmit="BackupMODX.backup();return false;" id="backupmodx-form-backup">
        <p>[[%backupmodx.intro? &namespace=`backupmodx`]]:</p>
        <label class="selectBox">
            <input type="checkbox" name="files" value="1" id="backupmodx-input-files">
            <span class="downloadLink">
                <i class="icon icon-zip"></i> [[%backupmodx.files? &namespace=`backupmodx`]]
                <i class="download icon icon-check"></i>
            </span>
        </label>
        <label class="selectBox">
            <input type="checkbox" name="database" value="1" id="backupmodx-input-database">
            <span class="downloadLink">
                <i class="icon icon-sql"></i> [[%backupmodx.database? &namespace=`backupmodx`]]
                <i class="download icon icon-check"></i>
            </span>
        </label>

        <label class="textBox">
            <p>[[%backupmodx.add_readme? &namespace=`backupmodx`]]</p>
            <textarea class="textarea" name="note" placeholder="[[%backupmodx.notes? &namespace=`backupmodx`]]" id="backupmodx-input-note"></textarea>
        </label>

        <div class="buttons">
            <button class="x-btn primary-button" type="submit">[[%backupmodx.btn_start? &namespace=`backupmodx`]]</button>
            <button class="x-btn" onclick="BackupMODX.restore();return false">[[%backupmodx.btn_restore? &namespace=`backupmodx`]]</button>
        </div>
    </form>
</div>
