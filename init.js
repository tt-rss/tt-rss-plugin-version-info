require(['dijit/Dialog', 'dojo/ready'], (Dialog, ready) => {
  ready(() => {
    const dialog = new Dialog({
      id: 'showVersionDlg',
      title: __('Versions'),
    });

    App.hotkey_actions['show_version'] = () => {
      xhr.json('backend.php', {op: 'PluginHandler', plugin: 'version_info', method: 'get_version'}, (reply) => {
        dialog.attr('content', `
            <div id='version-info'>
              <strong>tt-rss:</strong> <a target='_blank' rel='noreferrer noopener' href='https://github.com/tt-rss/tt-rss/commit/${reply.versions.ttrss.commit}'>
              ${reply.versions.ttrss.version} (${reply.versions.ttrss.friendly_timestamp})</a>
              <br>
              <strong>PHP:</strong> <a target='_blank' rel='noreferrer noopener' href='https://www.php.net/ChangeLog-${reply.versions.php.PHP_MAJOR_VERSION}.php#${reply.versions.php.PHP_VERSION}'>${reply.versions.php.PHP_VERSION}</a>
              <br>
              <strong>Alpine Linux:</strong> <a target='_blank' rel='noreferrer noopener' href='https://git.alpinelinux.org/aports/log/?h=v${reply.versions.alpine}'>${reply.versions.alpine}</a>
							<br>
              <strong>Database:</strong> <a target='_blank' rel='noreferrer noopener' href='https://www.postgresql.org/docs/release/'>${reply.versions.db}</a>
            </div>
            <hr>
            ${App.FormFields.icon('content_copy')} <a href='#' onclick='navigator.clipboard.writeText(document.querySelector("#version-info").innerText); return false'>${__('Copy to clipboard')}</a>
        `);
        dialog.show();
      });
    }
  });
});
