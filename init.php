<?php
class Version_Info extends Plugin {
  /** @return array<null|float|string|bool> */
  public function about() {
    return [
      null, // version
      'Show Tiny Tiny RSS (and related, e.g. PHP and OS) version info using Shift+V.', // description
      'wn', // author
      false, // is system
      'https://github.com/tt-rss/tt-rss-plugin-version-info/', // more info URL
    ];
  }


  /** @return int */
  public function api_version() {
    return 2;
  }


  /**
   * @param PluginHost $host
   *
   * @return void
   **/
  public function init($host): void {
    $host->add_hook($host::HOOK_HOTKEY_MAP, $this);
    $host->add_hook($host::HOOK_HOTKEY_INFO, $this);
  }


  /**
   * @param array<string, string> $hotkeys
   * @return array<string, string>
   */
  public function hook_hotkey_map($hotkeys) {
    $hotkeys['V'] = 'show_version';
    return $hotkeys;
  }


  /**
   * @param array<string, array<string, string>> $hotkeys
   * @return array<string, array<string, string>>
   */
  public function hook_hotkey_info($hotkeys) {
    $hotkeys[__('Other')]['show_version'] = __('Show tt-rss version info');
    // @phpstan-ignore-next-line '__()' returns strings
    return $hotkeys;
  }


  public function get_version(): void {
    /** @var array{'status': int, 'version': string, 'commit'?: string, 'timestamp'?: int|string} */
    $ttrss_version = Config::get_version(false);
    $is_git = array_key_exists('commit', $ttrss_version) && array_key_exists('timestamp', $ttrss_version);

    if (!$is_git) {
      print $ttrss_version['version'];
      return;
    }

    $ttrss_version['friendly_timestamp'] = TimeHelper::make_local_datetime(gmdate('Y-m-d H:i:s', (int) $ttrss_version['timestamp']), true, null, true);

    $alpine_version = trim(file_get_contents('/etc/alpine-release') ?: 'unknown');

    print json_encode([
      'versions' => [
        'ttrss' => $ttrss_version,
        'php' => ['PHP_MAJOR_VERSION' => \PHP_MAJOR_VERSION, 'PHP_VERSION' => \PHP_VERSION],
        'alpine' => $alpine_version,
        'db' => ORM::for_table('unused')->raw_query('SELECT VERSION()')->find_one()->version,
      ]
    ]);
  }


  /** @return string */
  public function get_js() {
    return file_get_contents(__DIR__ . '/init.js') ?: '';
  }
}
