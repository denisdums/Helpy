<?php

/**
 * Plugin Name:     Helpy
 * Plugin URI:      https://denisdums.com/helpy
 * Description:     Build for agencies and project managers who care about user autonomy and clarity.
 * Author:          denisdums
 * Author URI:      https://denisdums.com
 * Text Domain:     helpy
 * Domain Path:     /languages
 * Version:         0.1.0
 * 
 *
 * @package         Helpy
 */

if (! defined('ABSPATH')) exit;

define('HELPY_FILE', __FILE__);
define('HELPY_DIR', plugin_dir_path(__FILE__));
define('HELPY_URL', plugin_dir_url(__FILE__));
define('HELPY_VER', '1.0.0');

require_once __DIR__ . '/src/Plugin.php';

spl_autoload_register(function ($class) {
    if (strpos($class, 'Helpy\\') !== 0) return;
    $path = __DIR__ . '/src/' . str_replace(['Helpy\\', '\\'], ['', '/'], $class) . '.php';
    if (file_exists($path)) require_once $path;
});

register_activation_hook(__FILE__, [Helpy\DB\Installer::class, 'activate']);
register_uninstall_hook(__FILE__, 'helpy_uninstall_hook');

function helpy_uninstall_hook()
{
    // works on uninstall.php
}

add_action('plugins_loaded', function () {
    load_plugin_textdomain('helpy', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    (new Helpy\Plugin())->init();
});

// WP-CLI
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('helpy', Helpy\CLI\Commands::class);
}
