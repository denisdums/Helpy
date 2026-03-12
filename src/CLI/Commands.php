<?php
namespace Helpy\CLI;

if ( ! defined( 'ABSPATH' ) ) exit;

use WP_CLI;
use Helpy\Application\ImportExportService;

class Commands {
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

    public function import($args, $assoc_args) {
        $file = $assoc_args['file'] ?? null;
        if (!$file || !file_exists($file)) WP_CLI::error('File not found.');
        $data = json_decode(file_get_contents($file), true);
        if (json_last_error() !== JSON_ERROR_NONE) WP_CLI::error('Invalid JSON.');
        (new ImportExportService())->import($data);
        WP_CLI::success('Imported.');
    }

    public function doctor() {
        global $wpdb;
        $links = $wpdb->prefix.'helpy_links';
        $opts  = $wpdb->prefix.'helpy_options';

        $ls = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $links ) ) );
        $os = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $opts ) ) );

        WP_CLI::line("helpy_links: ".($ls ? 'OK' : 'MISSING'));
        WP_CLI::line("helpy_options: ".($os ? 'OK' : 'MISSING'));
        if ($ls) WP_CLI::line( 'links count: ' . $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM %i', $links ) ) );
        if ($os) WP_CLI::line( 'ticketing option: ' . ( $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM %i WHERE option_name=%s', $opts, 'ticketing' ) ) ? 'SET' : 'NOT SET' ) );
    }

    public function seed() {
        global $wpdb;
        $links = $wpdb->prefix.'helpy_links';
        $wpdb->query( $wpdb->prepare( 'DELETE FROM %i WHERE scope_type=%s AND scope_key=%s', $links, 'global', 'global' ) );
        $wpdb->insert($links, [
            'scope_type'=>'global','scope_key'=>'global','label'=>'Tutorial – Basics','url'=>'https://loom.example/1','target'=>'_blank','sort_order'=>0,'created_at'=>current_time('mysql'),'updated_at'=>current_time('mysql')
        ]);
        $wpdb->insert($links, [
            'scope_type'=>'global','scope_key'=>'global','label'=>'Documentation','url'=>'https://example.com/docs','target'=>'_blank','sort_order'=>1,'created_at'=>current_time('mysql'),'updated_at'=>current_time('mysql')
        ]);
        WP_CLI::success('Seeded global links.');
    }
}
