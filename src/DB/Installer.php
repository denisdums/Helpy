<?php
namespace Helpy\DB;

class Installer {
    public static function activate(): void {
        global $wpdb;

        $charset = $wpdb->get_charset_collate();
        $links   = $wpdb->prefix . 'helpy_links';
        $redmine = $wpdb->prefix . 'helpy_redmine';

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

CREATE TABLE $redmine (
  id TINYINT UNSIGNED NOT NULL,
  enabled TINYINT(1) NOT NULL DEFAULT 0,
  base_url VARCHAR(255) NULL,
  project  VARCHAR(120) NULL,
  new_issue_path VARCHAR(255) NULL DEFAULT '/projects/{project}/issues/new',
  updated_at DATETIME NOT NULL,
  PRIMARY KEY (id)
) $charset;
";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

        if (!get_option('helpy_schema_version')) {
            add_option('helpy_schema_version', '1');
        }
        // Seed Redmine row if not exists
        $exists = $wpdb->get_var("SELECT COUNT(*) FROM $redmine WHERE id=1");
        if (!$exists) {
            $wpdb->insert($redmine, [
                'id' => 1,
                'enabled' => 0,
                'base_url' => '',
                'project' => '',
                'new_issue_path' => '/projects/{project}/issues/new',
                'updated_at' => current_time('mysql')
            ]);
        }
    }
}
