<?php

namespace Helpy\Taxonomy;

use Helpy\Application\HelpyService;

class TermPanel
{
    public static function register(): void
    {
        $taxonomies = get_taxonomies(['public' => true]);

        foreach ($taxonomies as $tax) {
            add_action("{$tax}_term_edit_form_top", [self::class, 'render'], 10, 2);
        }
    }

    /**
     * @param \WP_Term $term
     * @param string   $taxonomy
     */
    public static function render($term, $taxonomy): void
    {
        $service = new HelpyService();
        $links   = $service->getLinksForTerm($taxonomy, (int)$term->term_id);

        echo '<div class="helpy-term-panel" style="margin:12px 0;padding:12px;border:1px solid #ccd0d4;background:#fff;">';
        echo '<h2 style="margin-top:0;">Helpy</h2>';

        if (!empty($links)) {
            echo '<ul class="helpy-list">';
            foreach ($links as $l) {
                $icon   = $l['icon'] ? esc_html($l['icon']) . ' ' : '';
                $target = $l['target'] === '_self' ? '_self' : '_blank';
                echo '<li class="helpy-li"><a rel="noopener noreferrer" target="' . esc_attr($target) . '" href="' . esc_url($l['url']) . '">' . $icon . esc_html($l['label']) . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo '<p>Aucun lien configuré (term/tax/global).</p>';
        }

        $href = $service->buildTicketUrl([
            'taxonomy' => $taxonomy,
            'termId'   => (int)$term->term_id,
            'term'     => $term->name,
            'title'    => $term->name,
        ]);
        if ($href) {
            echo '<p><a class="button button-primary" target="_blank" rel="noopener noreferrer" href="' . esc_url($href) . '">Create ticket</a></p>';
        }

        echo '</div>';
    }
}
