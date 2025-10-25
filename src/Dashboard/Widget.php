<?php
namespace Helpy\Dashboard;

use Helpy\Application\HelpyService;

class Widget {
    public static function register(): void {
        wp_add_dashboard_widget('helpy_widget', 'Helpy — Liens globaux', [self::class, 'render']);
    }

    public static function render(): void {
        $service = new HelpyService();
        $links = $service->getGlobalLinks();
        $redmine = $service->getRedmine();

        echo '<div class="helpy-widget">';
        if ($links) {
            echo '<ul class="helpy-list">';
            foreach ($links as $l) {
                $icon = $l['icon'] ? esc_html($l['icon']).' ' : '';
                $target = $l['target'] === '_self' ? '_self' : '_blank';
                echo '<li style="margin-bottom:6px;"><a rel="noopener noreferrer" target="'.esc_attr($target).'" href="'.esc_url($l['url']).'">'.$icon.esc_html($l['label']).'</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>Aucun lien global configuré.</p>';
        }
        if (!empty($redmine['enabled']) && $redmine['base_url'] && $redmine['project']) {
            $href = rtrim($redmine['base_url'], '/').'/'.ltrim(str_replace('{project}', rawurlencode($redmine['project']), $redmine['new_issue_path']), '/');
            echo '<p style="margin-top:8px;"><a class="button button-primary" target="_blank" rel="noopener noreferrer" href="'.esc_url($href).'">Créer un ticket Redmine</a></p>';
        }
        echo '</div>';
    }
}
