<?php

namespace Helpy\Editor;

use Helpy\Application\HelpyService;

class Sidebar
{
    public static function enqueue(): void
    {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;
        if (!$screen || $screen->base !== 'post') return;

        // Données
        $post_type = get_post_type();
        $service = new HelpyService();
        $data = [
            'links' => $service->getLinksForPostType($post_type),
            'redmine' => $service->getRedmine(),
        ];

        wp_enqueue_style(
            'helpy-editor-style',
            HELPY_URL . 'assets/editor/editor.css',
            [],
            HELPY_VER
        );
        // Script Gutenberg
        wp_enqueue_script(
            'helpy-editor',
            HELPY_URL . 'assets/editor/editor.js',
            ['wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-i18n'],
            HELPY_VER,
            true
        );
        wp_add_inline_script('helpy-editor', 'window.HELPY_DATA=' . wp_json_encode($data) . ';', 'before');
    }
}
