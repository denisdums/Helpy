<?php
namespace Helpy\CLI;

use WP_CLI;
use WP_CLI\Utils;
use Helpy\Application\ImportExportService;
use Helpy\DB\LinkRepository;
use Helpy\DB\RedmineRepository;
use Helpy\Domain\Scope;

class Commands {
    /**
     * Export Helpy config to JSON.
     *
     * ## OPTIONS
     * [--out=<file>]
     *
     * ## EXAMPLES
     * wp helpy export --out=helpy.json
     */
    public function export($args, $assoc_args) {
        $svc = new ImportExportService();
        $json = wp_json_encode($svc->export(), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        if (!empty($assoc_args['out'])) {
            file_put_contents($assoc_args['out'], $json);
            WP_CLI::success('Exported to '.$assoc_args['out']);
        } else {
            WP_CLI::line($json);
        }
    }

    /**
     * Import Helpy config from JSON file.
     *
     * ## OPTIONS
     * --file=<file>
     */
    public function import($args, $assoc_args) {
        $file = $assoc_args['file'] ?? null;
        if (!$file || !file_exists($file)) {
            WP_CLI::error('File not found.');
        }
        $data = json_decode(file_get_contents($file), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            WP_CLI::error('Invalid JSON.');
        }
        (new ImportExportService())->import($data);
        WP_CLI::success('Imported.');
    }

    /**
     * Doctor: checks tables and counts.
     */
    public function doctor() {
        global $wpdb;
        $links = $wpdb->prefix.'helpy_links';
        $red   = $wpdb->prefix.'helpy_redmine';

        $ls = $wpdb->get_var("SHOW TABLES LIKE '{$links}'");
        $rs = $wpdb->get_var("SHOW TABLES LIKE '{$red}'");
        WP_CLI::line("helpy_links: ".($ls ? 'OK' : 'MISSING'));
        WP_CLI::line("helpy_redmine: ".($rs ? 'OK' : 'MISSING'));
        if ($ls) WP_CLI::line('links count: '.$wpdb->get_var("SELECT COUNT(*) FROM {$links}"));
        if ($rs) WP_CLI::line('redmine row: '.$wpdb->get_var("SELECT COUNT(*) FROM {$red} WHERE id=1"));
    }

    /**
     * Seed demo data.
     */
    public function seed() {
        $links = new LinkRepository();
        $links->deleteScope(Scope::GLOBAL, Scope::GLOBAL);
        $links->bulkInsert(Scope::GLOBAL, Scope::GLOBAL, [
            ['label'=>'Tutoriel – Mise à jour', 'url'=>'https://loom.example/1', 'type'=>'video', 'icon'=>'🎥', 'target'=>'_blank', 'sort_order'=>0],
            ['label'=>'Doc – Rédaction', 'url'=>'https://example.com/doc', 'type'=>'doc', 'icon'=>'📄', 'target'=>'_blank', 'sort_order'=>1],
        ]);
        WP_CLI::success('Seeded global links.');
    }
}
