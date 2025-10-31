<?php

namespace Helpy\Admin;

use Helpy\DB\LinkRepository;
use Helpy\DB\OptionsRepository;
use Helpy\Application\HelpyService;

class PublicLinksPage
{
    public static function register(): void
    {
        $svg_path = plugin_dir_path(__FILE__) . '../../assets/img/admin-menu-icon.svg';

        if (file_exists($svg_path)) {
            $svg = file_get_contents($svg_path);
            if ($svg && !empty(trim($svg))) {
                $icon_url = 'data:image/svg+xml;base64,' . base64_encode($svg);
            }
        }

        add_menu_page(
            __('Helpy - Links', 'helpy'),
            __('Liens utiles', 'helpy'),
            'read',
            'helpy-links',
            [self::class, 'render'],
            $icon_url,
            100
        );
    }

    public static function render(): void
    {
        if (! current_user_can('read')) wp_die('Forbidden');

        $linksRepo   = new LinkRepository();
        $optsRepo    = new OptionsRepository();
        $service     = new HelpyService();

        $grouped     = $linksRepo->getAllGrouped();
        $postTypes   = get_post_types(['public' => true], 'objects');
        $taxonomies  = get_taxonomies(['public' => true], 'objects');

        $ticketing = $optsRepo->get('ticketing', []);
        $ticketing = wp_parse_args($ticketing, [
            'enabled'        => false,
            'base_url'       => '',
            'project'        => '',
            'new_issue_path' => '/new?project={project}',
            'button_label'   => 'Create ticket',
        ]);

        wp_enqueue_style('helpy-admin');

?>
        <div class="helpy-page wrap">
            <h1 class="helpy-page__title">Helpy - Links directory</h1>
            <div class="helpy-page__header">
                <img src="<?php echo esc_url(plugins_url('../../assets/img/plugin-banner-background.png', __FILE__)); ?>" alt="Helpy Banner Background" class="helpy-page__header-banner" />
                <div class="helpy-page__header__content">
                    <img src="<?php echo esc_url(plugins_url('../../assets/img/plugin-logo-white.svg', __FILE__)); ?>" alt="Helpy Logo" class="helpy-page__header-logo" />
                    <p class="helpy-page__header-baseline">for agencies and project managers who care about users.</p>
                </div>
            </div>

            <div class="helpy-page__content">
                <ul class="helpy-page__content__menu">
                    <li><a href="#helpy-links-global">Globaux</a></li>
                    <li><a href="#helpy-links-posttypes">Par type de contenu</a></li>
                    <li><a href="#helpy-links-taxonomies">Par taxonomie</a></li>
                </ul>

                <div id="helpy-links-global" class="helpy-page__content__menu-tab">
                    <div class="helpy-page__content__menu-tab__content">
                        <p><b>Liens globaux</b></p>
                        <p>Retrouvez ici les liens d’aide mis à votre disposition pour vous accompagner dans l’utilisation du site. Que ce soit pour créer une page, mettre à jour un contenu ou suivre un tutoriel, ces ressources vous guideront pas à pas sans quitter l’administration.</p>
                        <?php
                        $rows = $grouped['global'] ?? [];
                        if ($rows): ?>
                            <ul class="helpy-list">
                                <?php foreach ($rows as $l): ?>
                                    <li>
                                        <a href="<?php echo esc_url($l['url']); ?>" target="_blank" rel="noopener noreferrer">
                                            <?php echo esc_html($l['label']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>Aucun lien global configuré.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="helpy-links-posttypes" class="helpy-page__content__menu-tab">
                    <div class="helpy-page__content__menu-tab__content">
                        <p><b>Liens par type de contenu</b></p>
                        <p>Vous trouverez ici des liens adaptés au type de contenu que vous êtes en train d’éditer. Chaque section peut proposer des ressources spécifiques - par exemple des conseils pour rédiger une actualité, mettre en page une page d’accueil ou gérer un produit.</p>
                        <hr>
                        <?php foreach ($postTypes as $slug => $obj):
                            $rows = $grouped['post_type'][$slug] ?? []; ?>
                            <?php if ($rows): ?>
                                <details class="helpy-details" <?php if ($slug === array_key_first($postTypes)) echo 'open'; ?>>
                                    <summary>
                                        <h3 style="display:inline;margin-right:6px;"><?php echo esc_html($obj->labels->singular_name); ?></h3>
                                    </summary>

                                    <ul class="helpy-list">
                                        <?php foreach ($rows as $l): ?>
                                            <li>
                                                <a href="<?php echo esc_url($l['url']); ?>" target="_blank" rel="noopener noreferrer">
                                                    <?php echo esc_html($l['label']); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </details>
                                <hr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div id="helpy-links-taxonomies" class="helpy-page__content__menu-tab">
                    <div class="helpy-page__content__menu-tab__content">
                        <p><b>Liens par taxonomie</b></p>
                        <p>Ces liens vous aident à mieux comprendre comment organiser le contenu du site à l’aide des catégories, étiquettes ou autres taxonomies. Consultez-les pour savoir quand créer une nouvelle catégorie, comment nommer vos termes ou structurer vos articles de façon cohérente.</p>
                        <hr>
                        <?php foreach ($taxonomies as $tax => $obj):
                            $rows = $grouped['taxonomy'][$tax] ?? []; ?>
                            <?php if ($rows): ?>
                                <details class="helpy-details" <?php if ($tax === array_key_first($taxonomies)) echo 'open'; ?>>
                                    <summary>
                                        <h3 style="display:inline;margin-right:6px;"><?php echo esc_html($obj->labels->singular_name); ?></h3>
                                    </summary>
                                    <ul class="helpy-list">
                                        <?php foreach ($rows as $l): ?>
                                            <li>
                                                <a href="<?php echo esc_url($l['url']); ?>" target="_blank" rel="noopener noreferrer">
                                                    <?php echo esc_html($l['label']); ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </details>
                                <hr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}
