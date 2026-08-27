<?php
namespace WOMToolkit\Core;

if (!defined('ABSPATH')) {
    exit;
}

class Updater
{
    private static $instance = null;

    private $plugin_file;
    private $plugin_basename;
    private $github_repo;
    private $plugin_slug;
    private $cache_key;
    private $failure_cache_key;
    private $release_asset_name;

    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
        $this->plugin_file = WOM_TOOLKIT_PATH . 'wom-toolkit.php';
        $this->plugin_basename = plugin_basename($this->plugin_file);
        $this->github_repo = defined('WOM_TOOLKIT_GITHUB_REPO') ? WOM_TOOLKIT_GITHUB_REPO : '';
        $this->plugin_slug = 'wom-toolkit';
        $this->cache_key = 'wom_toolkit_github_release_data';
        $this->failure_cache_key = 'wom_toolkit_github_release_failure';
        $this->release_asset_name = 'wom-toolkit.zip';

        if (empty($this->github_repo)) {
            return;
        }

        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));
        add_filter('plugins_api', array($this, 'plugin_info'), 20, 3);
        add_action('upgrader_process_complete', array($this, 'purge_cache'), 10, 2);
    }

    public function check_update($transient)
    {
        if (!is_object($transient) || empty($transient->checked)) {
            return $transient;
        }

        $remote = $this->get_remote_data();

        if (!$remote || empty($remote['tag_name'])) {
            return $transient;
        }

        $remote_version = ltrim($remote['tag_name'], 'v');

        if (
        isset($transient->checked[$this->plugin_basename]) &&
        version_compare($transient->checked[$this->plugin_basename], $remote_version, '<')
        ) {
            $package = $this->get_package_url($remote);

            if ($package) {
                $obj = new \stdClass();
                $obj->slug = $this->plugin_slug;
                $obj->plugin = $this->plugin_basename;
                $obj->new_version = $remote_version;
                $obj->url = !empty($remote['html_url']) ? $remote['html_url'] : '';
                $obj->package = $package;

                $transient->response[$this->plugin_basename] = $obj;
            }
        }

        return $transient;
    }

    public function plugin_info($result, $action, $args)
    {
        if ($action !== 'plugin_information') {
            return $result;
        }

        if (empty($args->slug) || $args->slug !== $this->plugin_slug) {
            return $result;
        }

        $remote = $this->get_remote_data();

        if (!$remote) {
            return $result;
        }

        $remote_version = !empty($remote['tag_name']) ? ltrim($remote['tag_name'], 'v') : WOM_TOOLKIT_VERSION;
        $package = $this->get_package_url($remote);

        $tested_up_to = defined('WOM_TOOLKIT_TESTED_UP_TO') ? WOM_TOOLKIT_TESTED_UP_TO : get_bloginfo('version');

        $obj = new \stdClass();
        $obj->name = 'Mirox Toolkit';
        $obj->slug = $this->plugin_slug;
        $obj->version = $remote_version;
        $obj->author = '<span>Mirox</span>';
        $obj->homepage = !empty($remote['html_url']) ? $remote['html_url'] : '';
        $obj->download_link = $package;
        $obj->trunk = $package;
        $obj->requires = '6.0';
        $obj->tested = $tested_up_to;
        $obj->requires_php = '7.4';
        $obj->last_updated = !empty($remote['published_at']) ? $remote['published_at'] : '';
        $obj->sections = array(
            'description' => 'Modular WordPress toolkit for frontend enhancements and admin utilities.',
            'installation' => 'Install the plugin, activate it, then go to Mirox Toolkit in the admin menu.',
            'changelog' => $this->get_changelog($remote),
        );

        return $obj;
    }

    public function purge_cache($upgrader, $options)
    {
        if (
        isset($options['action'], $options['type']) &&
        $options['action'] === 'update' &&
        $options['type'] === 'plugin'
        ) {
            delete_transient($this->cache_key);
            delete_transient($this->failure_cache_key);
        }
    }

    private function get_remote_data()
    {
        $cached = get_transient($this->cache_key);

        if ($cached !== false) {
            return $cached;
        }

        $failure_cached = get_transient($this->failure_cache_key);
        if ($failure_cached !== false) {
            return false;
        }

        $url = 'https://api.github.com/repos/' . trim($this->github_repo) . '/releases/latest';

        $response = wp_remote_get(
            $url,
            array(
            'timeout' => 20,
            'headers' => array(
                'Accept' => 'application/vnd.github+json',
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url('/'),
            ),
        )
        );

        if (is_wp_error($response)) {
            set_transient($this->failure_cache_key, true, 30 * MINUTE_IN_SECONDS);
            return false;
        }

        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);

        if ($code !== 200 || empty($body)) {
            set_transient($this->failure_cache_key, true, 30 * MINUTE_IN_SECONDS);
            return false;
        }

        $data = json_decode($body, true);

        if (empty($data) || !is_array($data) || empty($data['tag_name'])) {
            set_transient($this->failure_cache_key, true, 30 * MINUTE_IN_SECONDS);
            return false;
        }

        set_transient($this->cache_key, $data, 12 * HOUR_IN_SECONDS);
        delete_transient($this->failure_cache_key);

        return $data;
    }

    private function get_package_url($remote)
    {
        if (!empty($remote['assets']) && is_array($remote['assets'])) {
            foreach ($remote['assets'] as $asset) {
                if (
                !empty($asset['name']) &&
                $asset['name'] === $this->release_asset_name &&
                !empty($asset['browser_download_url'])
                ) {
                    $url = $asset['browser_download_url'];

                    if (wp_parse_url($url, PHP_URL_SCHEME) !== 'https') {
                        return false;
                    }

                    return $url;
                }
            }
        }

        return false;
    }

    private function get_changelog($remote)
    {
        if (!empty($remote['body'])) {
            return nl2br(esc_html($remote['body']));
        }

        return 'No changelog available.';
    }
}
