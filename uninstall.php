<?php
if ( ! defined('WP_UNINSTALL_PLUGIN') ) exit;

global $wpdb;
$links   = $wpdb->prefix . 'helpy_links';
$options = $wpdb->prefix . 'helpy_options';

$wpdb->query("DROP TABLE IF EXISTS $links");
$wpdb->query("DROP TABLE IF EXISTS $options");

delete_option('helpy_schema_version');
