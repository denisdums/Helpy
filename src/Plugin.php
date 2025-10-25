<?php

namespace Helpy;

use Helpy\Admin\SettingsPage;
use Helpy\Dashboard\Widget;
use Helpy\Editor\Sidebar;
use Helpy\Editor\Metabox;

class Plugin
{
    public function init(): void
    {
        // Admin page + actions
        add_action('admin_menu', [SettingsPage::class, 'register']);
        add_action('admin_post_helpy_save', [SettingsPage::class, 'handleSave']);
        add_action('admin_post_helpy_import', [SettingsPage::class, 'handleImport']);
        add_action('admin_post_helpy_export', [SettingsPage::class, 'handleExport']);

        // Editor UI
        add_action('enqueue_block_editor_assets', [Sidebar::class, 'enqueue']);
        add_action('add_meta_boxes', [Metabox::class, 'register']);

        // Dashboard widget
        add_action('wp_dashboard_setup', [Widget::class, 'register']);

        // Admin assets (pour la page Settings)
        add_action('admin_enqueue_scripts', function ($hook) {
            $hooks_ok = ['settings_page_helpy-settings', 'index.php', 'post.php', 'post-new.php'];
            if (!in_array($hook, $hooks_ok, true)) return;
        
            wp_enqueue_style('helpy-admin', HELPY_URL . 'assets/admin/admin.css', [], HELPY_VER);

            if ($hook === 'settings_page_helpy-settings') {
                wp_enqueue_script('helpy-admin', HELPY_URL . 'assets/admin/admin.js', [], HELPY_VER, true);
                wp_enqueue_script('postbox');
                wp_enqueue_style('dashboard');
                wp_add_inline_script('postbox', "
            jQuery(function($){
              postboxes.add_postbox_toggles('helpy-settings');
            });
        ");
            }
        });
    }
}
