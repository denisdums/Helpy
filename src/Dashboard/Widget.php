<?php
namespace Helpy\Dashboard;

use Helpy\Application\HelpyService;

class Widget {
    public static function register(): void {
        wp_add_dashboard_widget('helpy_widget', 'Helpy — Global links', [self::class, 'render']);
    }

    public static function render(): void {
        $service = new HelpyService();
        $links   = $service->getGlobalLinks();

        echo '<div class="helpy-widget">';
        if ($links) {
            echo '<ul class="helpy-list">';
            foreach ($links as $l) {
                $icon = $l['icon'] ? esc_html($l['icon']).' ' : '';
                $target = $l['target'] === '_self' ? '_self' : '_blank';
                echo '<li class="helpy-li"><a rel="noopener noreferrer" target="'.esc_attr($target).'" href="'.esc_url($l['url']).'">'.$icon.esc_html($l['label']).'</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No global links configured.</p>';
        }

        $href = $service->buildTicketUrl();
        if ($href) {
            echo '<p style="margin-top:8px;"><a class="button button-primary" target="_blank" rel="noopener noreferrer" href="'.esc_url($href).'">Create ticket</a></p>';
        }
        echo '</div>';
    }
}
