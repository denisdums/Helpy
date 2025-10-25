<?php

namespace Helpy\Admin;

use Helpy\Application\ImportExportService;
use Helpy\DB\LinkRepository;
use Helpy\DB\RedmineRepository;
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

    $linksRepo  = new LinkRepository();
    $redRepo    = new RedmineRepository();

    $grouped    = $linksRepo->getAllGrouped();
    $redmine    = $redRepo->get();
    $postTypes  = get_post_types(['public' => true], 'objects');
?>
    <div class="wrap">
      <h1>Helpy — Réglages</h1>

      <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-1">
          <div id="post-body-content">
            <div id="normal-sortables" class="meta-box-sortables ui-sortable">
              <!-- Postbox : Liens globaux -->
              <div class="postbox closed">
                <div class="postbox-header">
                  <h2 class="hndle ui-sortable-handle">Liens globaux</h2>
                  <div class="handle-actions hide-if-no-js"><button type="button" class="handle-order-higher" aria-disabled="true">
                    <button type="button" class="handlediv" aria-expanded="true"><span class="toggle-indicator" aria-hidden="true"></span></button>
                  </div>
                </div>
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

              <!-- Postbox : Liens par post type -->
              <div class="postbox closed">
                <div class="postbox-header">
                  <h2 class="hndle ui-sortable-handle">Liens par post type</h2>
                  <div class="handle-actions hide-if-no-js"><button type="button" class="handle-order-higher" aria-disabled="true">
                    <button type="button" class="handlediv" aria-expanded="true"><span class="toggle-indicator" aria-hidden="true"></span></button>
                  </div>
                </div>
                <div class="inside">
                  <?php foreach ($postTypes as $slug => $obj):
                    $rows = $grouped['post_type'][$slug] ?? []; ?>
                    <div class="helpy-pt-box">
                      <h3 style="margin-top:0;"><?php echo esc_html($obj->labels->singular_name); ?></h3>
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

              <!-- Postbox : Redmine -->
              <div class="postbox closed">
                <div class="postbox-header">
                  <h2 class="hndle ui-sortable-handle">Redmine</h2>
                  <div class="handle-actions hide-if-no-js"><button type="button" class="handle-order-higher" aria-disabled="true">
                    <button type="button" class="handlediv" aria-expanded="true"><span class="toggle-indicator" aria-hidden="true"></span></button>
                  </div>
                </div>
                <div class="inside">
                  <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <?php wp_nonce_field('helpy_save'); ?>
                    <input type="hidden" name="action" value="helpy_save">
                    <input type="hidden" name="scope_type" value="redmine">
                    <table class="form-table">
                      <tr>
                        <th>Activer</th>
                        <td><label><input type="checkbox" name="enabled" value="1" <?php checked($redmine['enabled']); ?>> Oui</label></td>
                      </tr>
                      <tr>
                        <th>Base URL</th>
                        <td><input type="url" name="base_url" class="regular-text" value="<?php echo esc_attr($redmine['base_url']); ?>" placeholder="https://redmine.example.com"></td>
                      </tr>
                      <tr>
                        <th>Projet</th>
                        <td><input type="text" name="project" class="regular-text" value="<?php echo esc_attr($redmine['project']); ?>"></td>
                      </tr>
                      <tr>
                        <th>Chemin création issue</th>
                        <td><input type="text" name="new_issue_path" class="regular-text" value="<?php echo esc_attr($redmine['new_issue_path']); ?>"></td>
                      </tr>
                    </table>
                    <p><button type="submit" class="button button-primary">Enregistrer</button></p>
                  </form>
                </div>
              </div>

              <!-- Postbox : Import / Export -->
              <div class="postbox closed">
                <div class="postbox-header">
                  <h2 class="hndle ui-sortable-handle">Import / Export</h2>
                  <div class="handle-actions hide-if-no-js"><button type="button" class="handle-order-higher" aria-disabled="true">
                    <button type="button" class="handlediv" aria-expanded="true"><span class="toggle-indicator" aria-hidden="true"></span></button>
                  </div>
                </div>
                <div class="inside">
                  <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-right:16px;">
                    <?php wp_nonce_field('helpy_export'); ?>
                    <input type="hidden" name="action" value="helpy_export">
                    <button type="submit" class="button">Exporter JSON</button>
                  </form>

                  <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;vertical-align:top;max-width:100%;">
                    <?php wp_nonce_field('helpy_import'); ?>
                    <input type="hidden" name="action" value="helpy_import">
                    <p><textarea name="payload" rows="8" cols="80" style="width:100%;" placeholder='Collez ici le JSON'></textarea></p>
                    <p><button type="submit" class="button button-secondary">Importer (remplace)</button></p>
                  </form>
                </div>
              </div>
            </div>
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

    if ($scopeType === 'global' || $scopeType === 'post_type') {
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

      // label + url requis
      $items = array_values(array_filter($items, fn($i) => $i['label'] && $i['url']));

      $repo = new LinkRepository();
      $repo->deleteScope($scopeType, $scopeKey);
      $repo->bulkInsert($scopeType, $scopeKey, $items);
    } elseif ($scopeType === 'redmine') {
      (new RedmineRepository())->save([
        'enabled'        => !empty($_POST['enabled']),
        'base_url'       => $_POST['base_url'] ?? '',
        'project'        => $_POST['project'] ?? '',
        'new_issue_path' => $_POST['new_issue_path'] ?? '/projects/{project}/issues/new',
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
      wp_die('JSON invalide.');
    }
    exit;
  }
}
