<?php
namespace Helpy\DB;

use wpdb;

class OptionsRepository {
    private wpdb $db;
    private string $table;

    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;
        $this->table = $wpdb->prefix . 'helpy_options';
    }

    public function get(string $name, $default = null) {
        $sql = $this->db->prepare("SELECT option_value FROM {$this->table} WHERE option_name=%s", $name);
        $val = $this->db->get_var($sql);
        if ($val === null) return $default;
        $decoded = json_decode($val, true);
        return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $default;
    }

    public function set(string $name, $value, int $autoload = 0): void {
        $json = wp_json_encode($value);
        $exists = $this->db->get_var($this->db->prepare(
            "SELECT option_id FROM {$this->table} WHERE option_name=%s", $name
        ));
        if ($exists) {
            $this->db->update($this->table, ['option_value'=>$json, 'autoload'=>$autoload], ['option_name'=>$name]);
        } else {
            $this->db->insert($this->table, ['option_name'=>$name, 'option_value'=>$json, 'autoload'=>$autoload]);
        }
    }

    public function delete(string $name): void {
        $this->db->delete($this->table, ['option_name' => $name]);
    }
}
