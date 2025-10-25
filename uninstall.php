<?php
// Sécurité
if ( ! defined('WP_UNINSTALL_PLUGIN') ) exit;

global $wpdb;
$links   = $wpdb->prefix . 'helpy_links';
$redmine = $wpdb->prefix . 'helpy_redmine';

// Supprime les tables
$wpdb->query("DROP TABLE IF EXISTS $links");
$wpdb->query("DROP TABLE IF EXISTS $redmine");

// Supprime la version de schéma
delete_option('helpy_schema_version');
