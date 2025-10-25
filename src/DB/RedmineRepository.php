<?php
namespace Helpy\DB;

use wpdb;

class RedmineRepository {
    private wpdb $db;
    private string $table;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $wpdb->prefix . 'helpy_redmine';
    }

    public function get(): array {
        $row = $this->db->get_row("SELECT * FROM {$this->table} WHERE id=1", ARRAY_A);
        return $row ? $row : [
            'id' => 1, 'enabled' => 0, 'base_url' => '', 'project' => '', 'new_issue_path' => '/projects/{project}/issues/new'
        ];
    }

    public function save(array $data): void {
        $payload = [
            'enabled' => !empty($data['enabled']) ? 1 : 0,
            'base_url' => esc_url_raw(untrailingslashit($data['base_url'] ?? '')),
            'project' => sanitize_text_field($data['project'] ?? ''),
            'new_issue_path' => sanitize_text_field($data['new_issue_path'] ?? '/projects/{project}/issues/new'),
            'updated_at' => current_time('mysql'),
        ];
        $this->db->update($this->table, $payload, ['id' => 1]);
    }
}
