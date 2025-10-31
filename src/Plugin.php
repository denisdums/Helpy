<?php
namespace Helpy;

use Helpy\Admin\SettingsPage;
use Helpy\Admin\PublicLinksPage;
use Helpy\Dashboard\Widget;
use Helpy\Editor\Sidebar;
use Helpy\Editor\Metabox;
use Helpy\Taxonomy\TermPanel; // NEW

class Plugin {
    public function init(): void {
        // Admin page + actions
        add_action('admin_menu', [PublicLinksPage::class, 'register']);
        add_action('admin_menu', [SettingsPage::class, 'register']);
        add_action('admin_post_helpy_save', [SettingsPage::class, 'handleSave']);
        add_action('admin_post_helpy_import', [SettingsPage::class, 'handleImport']);
        add_action('admin_post_helpy_export', [SettingsPage::class, 'handleExport']);

        // Editor UI
        add_action('enqueue_block_editor_assets', [Sidebar::class, 'enqueue']);
        add_action('add_meta_boxes', [Metabox::class, 'register']);

        // Dashboard widget
        add_action('wp_dashboard_setup', [Widget::class, 'register']);

        // Term edit panels (taxonomies)
        add_action('admin_init', [TermPanel::class, 'register']);

        // Admin assets (settings page)
        add_action('admin_enqueue_scripts', function($hook){
            if ($hook === 'settings_page_helpy-settings') {
                wp_enqueue_style('helpy-admin', HELPY_URL.'assets/admin/admin.css', [], HELPY_VER);
                wp_enqueue_script('helpy-admin', HELPY_URL.'assets/admin/admin.js', ['jquery'], HELPY_VER, true);
                wp_enqueue_script('postbox');
                wp_enqueue_style('dashboard');
                wp_add_inline_script('postbox', "jQuery(function($){ postboxes.add_postbox_toggles('helpy-settings'); });");
            }
            // List bullets in admin globally (helps term panel too)
            if (in_array($hook, ['toplevel_page_helpy-links','settings_page_helpy-settings','term.php','edit-tags.php','index.php','post.php','post-new.php'], true)) {
                wp_enqueue_style('helpy-admin', HELPY_URL.'assets/admin/admin.css', [], HELPY_VER);
            }
        });
    }
}
