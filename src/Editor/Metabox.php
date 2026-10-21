<?php
namespace Helpy\Editor;

if ( ! defined( 'ABSPATH' ) ) exit;

use Helpy\Application\HelpyService;
use Helpy\Domain\EditedContent;

class Metabox {
    public static function register(): void {
        $pt = get_current_screen()?->post_type;
        if (function_exists('use_block_editor_for_post_type') && $pt && use_block_editor_for_post_type($pt)) {
            return;
        }
        add_meta_box('helpy_box', 'Helpy', [self::class, 'render'], null, 'side', 'high');
    }

    public static function render(\WP_Post|\WC_Order $edited): void {
        $content = EditedContent::from($edited);
        $service = new HelpyService();
        $links   = $service->getLinksForPostType($content->getPostType());

        echo '<div class="helpy-box">';
        if ($links) {
            echo '<ul class="helpy-list">';
            foreach ($links as $l) {
                $target = $l['target'] === '_self' ? '_self' : '_blank';
                echo '<li class="helpy-li"><a rel="noopener noreferrer" target="'.esc_attr($target).'" href="'.esc_url($l['url']).'">'.esc_html($l['label']).'</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>Aucun lien configuré pour ce type de contenu.</p>';
        }

        $href = $service->buildTicketUrl($content->getTicketContext());
        $label = $service->getTicketButtonLabel();

        if ($href) {
            echo '<p><a class="button button-primary" target="_blank" rel="noopener noreferrer" href="'.esc_url($href).'">'.esc_html($label).'</a></p>';
        }
        echo '</div>';
    }
}
