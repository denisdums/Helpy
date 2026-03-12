<?php
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

if (defined('WP_UNINSTALL_PLUGIN')) {
	global $wpdb;

	$helpy_links = $wpdb->prefix . 'helpy_links';
	$helpy_options = $wpdb->prefix . 'helpy_options';

	$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %i', $helpy_links));
	$wpdb->query($wpdb->prepare('DROP TABLE IF EXISTS %i', $helpy_options));

	delete_option('helpy_schema_version');
}
