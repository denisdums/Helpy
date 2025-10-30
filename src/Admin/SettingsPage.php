<?php

namespace Helpy\Admin;

use Helpy\Application\ImportExportService;
use Helpy\DB\LinkRepository;
use Helpy\DB\OptionsRepository;
use Helpy\Domain\Scope;

class SettingsPage
{
  public static function register(): void
  {
    add_options_page(
      __('Helpy Settings', 'helpy'),
      __('Helpy', 'helpy'),
      'manage_options',
      'helpy-settings',
      [self::class, 'render']
    );
  }

  public static function render(): void
  {
    if (!current_user_can('manage_options')) wp_die('Forbidden');

    $linksRepo = new LinkRepository();
    $optsRepo  = new OptionsRepository();

    $grouped    = $linksRepo->getAllGrouped();
    $postTypes  = get_post_types(['public' => true], 'objects');
    $taxonomies = get_taxonomies(['public' => true], 'objects');

    $ticketing = $optsRepo->get('ticketing', []);
    $ticketing = wp_parse_args($ticketing, [
      'enabled'        => false,
      'base_url'       => '',
      'project'        => '',
      'new_issue_path' => '/new?project={project}',
      'button_label'   => 'Create ticket',
      'icon'           => '🐞',
    ]);
?>
    <div class="wrap">
      <h1>Helpy — Réglages</h1>
      <?php if (!empty($_GET['updated'])): ?>
        <div class="notice notice-success">
          <p>Réglages enregistrés.</p>
        </div>
      <?php endif; ?>

      <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-1">
          <div id="post-body-content">
            <div id="normal-sortables" class="meta-box-sortables ui-sortable">

              <!-- Liens globaux -->
              <div class="postbox">
                <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Basculer le panneau : Liens globaux</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                <h2 class="hndle"><span>Liens globaux</span></h2>
                <div class="inside">
                  <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('helpy_save'); ?>
                    <input type="hidden" name="action" value="helpy_save">
                    <input type="hidden" name="scope_type" value="global">
                    <input type="hidden" name="scope_key" value="global">
                    <table class="widefat striped helpy-table">
                      <thead>
                        <tr>
                          <th>Ordre</th>
                          <th>Label</th>
                          <th>URL</th>
                          <th>Type</th>
                          <th>Icône</th>
                          <th>Cible</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody id="helpy-global-tbody">
                        <?php
                        $rows = $grouped['global'] ?? [];
                        foreach ($rows as $i => $r): ?>
                          <tr>
                            <td><input type="number" name="items[<?php echo $i; ?>][sort_order]" value="<?php echo (int)$r['sort_order']; ?>" /></td>
                            <td><input type="text" name="items[<?php echo $i; ?>][label]" value="<?php echo esc_attr($r['label']); ?>" /></td>
                            <td><input type="url" name="items[<?php echo $i; ?>][url]" value="<?php echo esc_url($r['url']); ?>" /></td>
                            <td>
                              <select name="items[<?php echo $i; ?>][type]">
                                <?php foreach (['video', 'doc', 'custom'] as $t): ?>
                                  <option value="<?php echo esc_attr($t); ?>" <?php selected($r['type'], $t); ?>><?php echo esc_html($t); ?></option>
                                <?php endforeach; ?>
                              </select>
                            </td>
                            <td><input type="text" name="items[<?php echo $i; ?>][icon]" value="<?php echo esc_attr($r['icon']); ?>" placeholder="🎥" /></td>
                            <td>
                              <select name="items[<?php echo $i; ?>][target]">
                                <option value="_blank" <?php selected($r['target'], '_blank'); ?>>_blank</option>
                                <option value="_self" <?php selected($r['target'], '_self'); ?>>_self</option>
                              </select>
                            </td>
                            <td><button type="button" class="button helpy-remove-row">Supprimer</button></td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                    <p><button type="button" class="button" data-add-row="#helpy-global-tbody">Ajouter un lien</button></p>
                    <p><button type="submit" class="button button-primary">Enregistrer</button></p>
                  </form>
                </div>
              </div>

              <!-- Liens par post type -->
              <div class="postbox">
                <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Basculer le panneau : Liens par post type</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                <h2 class="hndle"><span>Liens par post type</span></h2>
                <div class="inside">
                  <?php foreach ($postTypes as $slug => $obj):
                    $rows = $grouped['post_type'][$slug] ?? []; ?>
                    <div class="helpy-pt-box">
                      <h3 style="margin-top:0;"><?php echo esc_html($obj->labels->singular_name . " ({$slug})"); ?></h3>
                      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('helpy_save'); ?>
                        <input type="hidden" name="action" value="helpy_save">
                        <input type="hidden" name="scope_type" value="post_type">
                        <input type="hidden" name="scope_key" value="<?php echo esc_attr($slug); ?>">
                        <table class="widefat striped helpy-table">
                          <thead>
                            <tr>
                              <th>Ordre</th>
                              <th>Label</th>
                              <th>URL</th>
                              <th>Type</th>
                              <th>Icône</th>
                              <th>Cible</th>
                              <th></th>
                            </tr>
                          </thead>
                          <tbody id="helpy-<?php echo esc_attr($slug); ?>-tbody">
                            <?php foreach ($rows as $i => $r): ?>
                              <tr>
                                <td><input type="number" name="items[<?php echo $i; ?>][sort_order]" value="<?php echo (int)$r['sort_order']; ?>" /></td>
                                <td><input type="text" name="items[<?php echo $i; ?>][label]" value="<?php echo esc_attr($r['label']); ?>" /></td>
                                <td><input type="url" name="items[<?php echo $i; ?>][url]" value="<?php echo esc_url($r['url']); ?>" /></td>
                                <td>
                                  <select name="items[<?php echo $i; ?>][type]">
                                    <?php foreach (['video', 'doc', 'custom'] as $t): ?>
                                      <option value="<?php echo esc_attr($t); ?>" <?php selected($r['type'], $t); ?>><?php echo esc_html($t); ?></option>
                                    <?php endforeach; ?>
                                  </select>
                                </td>
                                <td><input type="text" name="items[<?php echo $i; ?>][icon]" value="<?php echo esc_attr($r['icon']); ?>" /></td>
                                <td>
                                  <select name="items[<?php echo $i; ?>][target]">
                                    <option value="_blank" <?php selected($r['target'], '_blank'); ?>>_blank</option>
                                    <option value="_self" <?php selected($r['target'], '_self'); ?>>_self</option>
                                  </select>
                                </td>
                                <td><button type="button" class="button helpy-remove-row">Supprimer</button></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                        <p><button type="button" class="button" data-add-row="#helpy-<?php echo esc_attr($slug); ?>-tbody">Ajouter un lien</button></p>
                        <p><button type="submit" class="button button-primary">Enregistrer</button></p>
                      </form>
                    </div>
                    <hr>
                  <?php endforeach; ?>
                </div>
              </div>

              <!-- Liens par taxonomie (NOUVEAU) -->
              <div class="postbox">
                <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Basculer le panneau : Liens par taxonomie</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                <h2 class="hndle"><span>Liens par taxonomie</span></h2>
                <div class="inside">
                  <?php foreach ($taxonomies as $tax => $obj):
                    $rows = $grouped['taxonomy'][$tax] ?? []; ?>
                    <div class="helpy-tax-box">
                      <h3 style="margin-top:0;"><?php echo esc_html($obj->labels->singular_name . " ({$tax})"); ?></h3>
                      <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('helpy_save'); ?>
                        <input type="hidden" name="action" value="helpy_save">
                        <input type="hidden" name="scope_type" value="taxonomy">
                        <input type="hidden" name="scope_key" value="<?php echo esc_attr($tax); ?>">
                        <table class="widefat striped helpy-table">
                          <thead>
                            <tr>
                              <th>Ordre</th>
                              <th>Label</th>
                              <th>URL</th>
                              <th>Type</th>
                              <th>Icône</th>
                              <th>Cible</th>
                              <th></th>
                            </tr>
                          </thead>
                          <tbody id="helpy-tax-<?php echo esc_attr($tax); ?>-tbody">
                            <?php foreach ($rows as $i => $r): ?>
                              <tr>
                                <td><input type="number" name="items[<?php echo $i; ?>][sort_order]" value="<?php echo (int)$r['sort_order']; ?>" /></td>
                                <td><input type="text" name="items[<?php echo $i; ?>][label]" value="<?php echo esc_attr($r['label']); ?>" /></td>
                                <td><input type="url" name="items[<?php echo $i; ?>][url]" value="<?php echo esc_url($r['url']); ?>" /></td>
                                <td>
                                  <select name="items[<?php echo $i; ?>][type]">
                                    <?php foreach (['video', 'doc', 'custom'] as $t): ?>
                                      <option value="<?php echo esc_attr($t); ?>" <?php selected($r['type'], $t); ?>><?php echo esc_html($t); ?></option>
                                    <?php endforeach; ?>
                                  </select>
                                </td>
                                <td><input type="text" name="items[<?php echo $i; ?>][icon]" value="<?php echo esc_attr($r['icon']); ?>" /></td>
                                <td>
                                  <select name="items[<?php echo $i; ?>][target]">
                                    <option value="_blank" <?php selected($r['target'], '_blank'); ?>>_blank</option>
                                    <option value="_self" <?php selected($r['target'], '_self'); ?>>_self</option>
                                  </select>
                                </td>
                                <td><button type="button" class="button helpy-remove-row">Supprimer</button></td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                        <p><button type="button" class="button" data-add-row="#helpy-tax-<?php echo esc_attr($tax); ?>-tbody">Ajouter un lien</button></p>
                        <p><button type="submit" class="button button-primary">Enregistrer</button></p>
                      </form>
                    </div>
                    <hr>
                  <?php endforeach; ?>
                </div>
              </div>

              <!-- Ticketing -->
              <div class="postbox">
                <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Basculer le panneau : Ticketing</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                <h2 class="hndle"><span>Ticketing</span></h2>
                <div class="inside">
                  <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('helpy_save'); ?>
                    <input type="hidden" name="action" value="helpy_save">
                    <input type="hidden" name="scope_type" value="ticketing">
                    <table class="form-table">
                      <tr>
                        <th>Enable</th>
                        <td><label><input type="checkbox" name="enabled" value="1" <?php checked($ticketing['enabled']); ?>> Yes</label></td>
                      </tr>
                      <tr>
                        <th>Base URL</th>
                        <td><input type="url" name="base_url" class="regular-text" value="<?php echo esc_attr($ticketing['base_url']); ?>" placeholder="https://yourdesk.example.com"></td>
                      </tr>
                      <tr>
                        <th>Project (optional)</th>
                        <td><input type="text" name="project" class="regular-text" value="<?php echo esc_attr($ticketing['project']); ?>"></td>
                      </tr>
                      <tr>
                        <th>New issue path</th>
                        <td><input type="text" name="new_issue_path" class="regular-text" value="<?php echo esc_attr($ticketing['new_issue_path']); ?>" placeholder="/new?project={project}&title={title}"></td>
                      </tr>
                      <tr>
                        <th>Button label</th>
                        <td><input type="text" name="button_label" class="regular-text" value="<?php echo esc_attr($ticketing['button_label']); ?>"></td>
                      </tr>
                      <tr>
                        <th>Icon (emoji)</th>
                        <td><input type="text" name="icon" class="regular-text" value="<?php echo esc_attr($ticketing['icon']); ?>" placeholder="🐞"></td>
                      </tr>
                    </table>
                    <p><button type="submit" class="button button-primary">Save</button></p>
                  </form>
                </div>
              </div>

              <!-- Import / Export -->
              <div class="postbox">
                <button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Basculer le panneau : Import / Export</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                <h2 class="hndle"><span>Import / Export</span></h2>
                <div class="inside">
                  <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-right:16px;">
                    <?php wp_nonce_field('helpy_export'); ?>
                    <input type="hidden" name="action" value="helpy_export">
                    <button type="submit" class="button">Exporter JSON</button>
                  </form>

                  <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;vertical-align:top;max-width:100%;">
                    <?php wp_nonce_field('helpy_import'); ?>
                    <input type="hidden" name="action" value="helpy_import">
                    <p><textarea name="payload" rows="8" cols="80" style="width:100%;" placeholder="Paste JSON here"></textarea></p>
                    <p><button type="submit" class="button button-secondary">Importer (remplace)</button></p>
                  </form>
                </div>
              </div>

            </div><!-- /normal-sortables -->
          </div><!-- /post-body-content -->
        </div><!-- /post-body -->
      </div><!-- /poststuff -->
    </div>
<?php
  }

  public static function handleSave(): void
  {
    if (!current_user_can('manage_options')) wp_die('Forbidden');
    check_admin_referer('helpy_save');

    $scopeType = sanitize_text_field($_POST['scope_type'] ?? '');

    if (in_array($scopeType, ['global', 'post_type', 'taxonomy'], true)) {
      $scopeKey = sanitize_text_field($_POST['scope_key'] ?? '');
      if ($scopeType === 'global') $scopeKey = Scope::GLOBAL;

      $items = array_values(array_map(function ($row) {
        return [
          'sort_order' => intval($row['sort_order'] ?? 0),
          'label'      => sanitize_text_field($row['label'] ?? ''),
          'url'        => esc_url_raw($row['url'] ?? ''),
          'type'       => in_array($row['type'] ?? 'custom', ['video', 'doc', 'custom'], true) ? $row['type'] : 'custom',
          'icon'       => isset($row['icon']) ? sanitize_text_field($row['icon']) : '',
          'target'     => ($row['target'] ?? '_blank') === '_self' ? '_self' : '_blank',
        ];
      }, $_POST['items'] ?? []));

      $items = array_values(array_filter($items, fn($i) => $i['label'] && $i['url']));

      $repo = new LinkRepository();
      $repo->deleteScope($scopeType, $scopeKey);
      $repo->bulkInsert($scopeType, $scopeKey, $items);
    } elseif ($scopeType === 'ticketing') {
      (new OptionsRepository())->set('ticketing', [
        'enabled'        => !empty($_POST['enabled']),
        'base_url'       => esc_url_raw($_POST['base_url'] ?? ''),
        'project'        => sanitize_text_field($_POST['project'] ?? ''),
        'new_issue_path' => sanitize_text_field($_POST['new_issue_path'] ?? '/new?project={project}'),
        'button_label'   => sanitize_text_field($_POST['button_label'] ?? 'Create ticket'),
        'icon'           => sanitize_text_field($_POST['icon'] ?? '🐞'),
      ]);
    }

    wp_safe_redirect(add_query_arg('updated', '1', admin_url('options-general.php?page=helpy-settings')));
    exit;
  }

  public static function handleExport(): void
  {
    if (!current_user_can('manage_options')) wp_die('Forbidden');
    check_admin_referer('helpy_export');

    $data = (new ImportExportService())->export();
    nocache_headers();
    header('Content-Type: application/json; charset=utf-8');
    header('Content-Disposition: attachment; filename=helpy-config.json');
    echo wp_json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
  }

  public static function handleImport(): void
  {
    if (!current_user_can('manage_options')) wp_die('Forbidden');
    check_admin_referer('helpy_import');

    $payload = wp_unslash($_POST['payload'] ?? '');
    $data = json_decode($payload, true);

    if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
      (new ImportExportService())->import($data);
      wp_safe_redirect(add_query_arg('updated', '1', admin_url('options-general.php?page=helpy-settings')));
    } else {
      wp_die('Invalid JSON.');
    }
    exit;
  }
}
