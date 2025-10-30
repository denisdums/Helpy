<?php
namespace Helpy\Editor;

use Helpy\Application\HelpyService;

class Metabox {
    public static function register(): void {
        $pt = get_current_screen()?->post_type;
        if (function_exists('use_block_editor_for_post_type') && $pt && use_block_editor_for_post_type($pt)) {
            return;
        }
        add_meta_box('helpy_box', 'Helpy', [self::class, 'render'], null, 'side', 'high');
    }

    public static function render(\WP_Post $post): void {
        $service = new HelpyService();
        $links   = $service->getLinksForPostType($post->post_type);

        echo '<div class="helpy-box">';
        if ($links) {
            echo '<ul class="helpy-list">';
            foreach ($links as $l) {
                $icon = $l['icon'] ? esc_html($l['icon']).' ' : '';
                $target = $l['target'] === '_self' ? '_self' : '_blank';
                echo '<li class="helpy-li"><a rel="noopener noreferrer" target="'.esc_attr($target).'" href="'.esc_url($l['url']).'">'.$icon.esc_html($l['label']).'</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>Aucun lien configuré pour ce type de contenu.</p>';
        }

        $href = $service->buildTicketUrl([
            'postId'   => $post->ID,
            'postType' => $post->post_type,
            'title'    => get_the_title($post),
        ]);
        if ($href) {
            echo '<p><a class="button button-primary" target="_blank" rel="noopener noreferrer" href="'.esc_url($href).'">Create ticket</a></p>';
        }
        echo '</div>';
    }
}
