<?php
namespace Helpy\Editor;

use Helpy\Application\HelpyService;

class Metabox {
    public static function register(): void {
        // Si on est en block editor pour ce post type, pas de metabox
        $pt = get_current_screen()?->post_type;
        if (function_exists('use_block_editor_for_post_type') && $pt && use_block_editor_for_post_type($pt)) {
            return;
        }
        add_meta_box('helpy_box', 'Helpy', [self::class, 'render'], null, 'side', 'high');
    }

    public static function render(\WP_Post $post): void {
        $service = new HelpyService();
        $links = $service->getLinksForPostType($post->post_type);
        $redmine = $service->getRedmine();
        echo '<div class="helpy-box">';
        if ($links) {
            echo '<ul class="helpy-list">';
            foreach ($links as $l) {
                $icon = $l['icon'] ? esc_html($l['icon']).' ' : '';
                $target = $l['target'] === '_self' ? '_self' : '_blank';
                echo '<li style="margin-bottom:6px;"><a rel="noopener noreferrer" target="'.esc_attr($target).'" href="'.esc_url($l['url']).'">'.$icon.esc_html($l['label']).'</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>Aucun lien configuré pour ce type de contenu.</p>';
        }
        if (!empty($redmine['enabled']) && $redmine['base_url'] && $redmine['project']) {
            $href = rtrim($redmine['base_url'], '/').'/'.ltrim(str_replace('{project}', rawurlencode($redmine['project']), $redmine['new_issue_path']), '/');
            echo '<p><a class="button button-primary" target="_blank" rel="noopener noreferrer" href="'.esc_url($href).'">Créer un ticket Redmine</a></p>';
        }
        echo '</div>';
    }
}
