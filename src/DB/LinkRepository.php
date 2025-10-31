<?php
namespace Helpy\DB;

use wpdb;

class LinkRepository {
    private wpdb $db;
    private string $table;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $wpdb->prefix . 'helpy_links';
    }

    public function getByScope(string $scopeType, string $scopeKey): array {
        $sql = $this->db->prepare(
            "SELECT * FROM {$this->table} WHERE scope_type=%s AND scope_key=%s ORDER BY sort_order ASC, id ASC",
            $scopeType, $scopeKey
        );
        return $this->db->get_results($sql, ARRAY_A) ?: [];
    }

    public function getAllGrouped(): array {
        $rows = $this->db->get_results("SELECT * FROM {$this->table} ORDER BY scope_type, scope_key, sort_order, id", ARRAY_A) ?: [];
        $out = ['global' => [], 'post_type' => []];
        foreach ($rows as $r) {
            if ($r['scope_type'] === 'global') {
                $out['global'][] = $r;
            } else if ($r['scope_type'] === 'post_type') {
                $out['post_type'][$r['scope_key']][] = $r;
            } else {
                $out['taxonomy'][$r['scope_key']][] = $r;
            }
        }

        return $out;
    }

    public function deleteScope(string $scopeType, string $scopeKey): void {
        $this->db->delete($this->table, ['scope_type' => $scopeType, 'scope_key' => $scopeKey]);
    }

    public function bulkInsert(string $scopeType, string $scopeKey, array $links): void {
        foreach ($links as $i => $l) {
            $this->db->insert($this->table, [
                'scope_type' => $scopeType,
                'scope_key'  => $scopeKey,
                'label'      => sanitize_text_field($l['label']),
                'url'        => esc_url_raw($l['url']),
                'target'     => ($l['target'] ?? '_blank') === '_self' ? '_self' : '_blank',
                'sort_order' => intval($l['sort_order'] ?? $i),
                'created_at' => current_time('mysql'),
                'updated_at' => current_time('mysql'),
            ]);
        }
    }
}
