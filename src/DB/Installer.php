<?php

namespace Helpy\DB;

class Installer
{
    public static function activate(): void
    {
        global $wpdb;

        $charset = $wpdb->get_charset_collate();
        $links   = $wpdb->prefix . 'helpy_links';
        $options = $wpdb->prefix . 'helpy_options';

        $sql = "
CREATE TABLE $links (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  scope_type VARCHAR(20) NOT NULL,
  scope_key  VARCHAR(60) NOT NULL,
  label VARCHAR(255) NOT NULL,
  url TEXT NOT NULL,
  type VARCHAR(20) NOT NULL,
  icon VARCHAR(16) NULL,
  target VARCHAR(10) NOT NULL DEFAULT '_blank',
  sort_order INT UNSIGNED NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  PRIMARY KEY  (id),
  KEY scope (scope_type, scope_key, sort_order)
) $charset;

CREATE TABLE $options (
  option_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  option_name VARCHAR(191) NOT NULL,
  option_value LONGTEXT NULL,
  autoload TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (option_id),
  UNIQUE KEY option_name (option_name)
) $charset;
";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        $current = get_option('helpy_schema_version');
        if (!$current) {
            add_option('helpy_schema_version', '2');
        } elseif (version_compare((string)$current, '2', '<')) {
            update_option('helpy_schema_version', '2');
        }
    }
}
