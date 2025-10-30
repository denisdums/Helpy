<?php
namespace Helpy\Editor;

use Helpy\Application\HelpyService;

class Sidebar {
    public static function enqueue(): void {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if (!$screen || $screen->base !== 'post') return;

        $post_id   = get_the_ID();
        $post_type = get_post_type($post_id) ?: get_post_type();

        $service = new HelpyService();
        $data = [
            'links'     => $service->getLinksForPostType($post_type),
            'ticketing' => $service->getTicketing(),
            'ctx'       => [
                'postId'   => $post_id,
                'postType' => $post_type,
                'title'    => get_the_title($post_id),
            ],
        ];

        wp_enqueue_script(
            'helpy-editor',
            HELPY_URL . 'assets/editor/editor.js',
            ['wp-plugins','wp-edit-post','wp-element','wp-components','wp-i18n'],
            HELPY_VER,
            true
        );
        wp_enqueue_style(
            'helpy-editor-style',
            HELPY_URL . 'assets/editor/editor.css',
            [],
            HELPY_VER
        );
        wp_add_inline_script('helpy-editor', 'window.HELPY_DATA=' . wp_json_encode($data) . ';', 'before');
    }
}
