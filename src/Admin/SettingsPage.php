<?php

namespace Helpy\Admin;

if (!defined('ABSPATH')) exit;

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
		$optsRepo = new OptionsRepository();

		$grouped = $linksRepo->getAllGrouped();
		$postTypes = get_post_types(['public' => true], 'objects');
		$taxonomies = get_taxonomies(['public' => true], 'objects');

		$ticketing = $optsRepo->get('ticketing', []);
		$ticketing = wp_parse_args($ticketing, [
			'enabled' => false,
			'base_url' => '',
			'project' => '',
			'new_issue_path' => '/new?project={project}',
			'button_label' => __('Create ticket', 'helpy'),
		]);
		?>
		<div class="helpy-page wrap">
			<h1 class="helpy-page__title"><?php echo esc_html__('Helpy Settings', 'helpy'); ?></h1>
			<div class="helpy-page__header">
				<img
					src="<?php echo esc_url(plugins_url('../../assets/img/plugin-banner-background.png', __FILE__)); ?>"
					alt="Helpy Banner Background" class="helpy-page__header-banner"/>
				<div class="helpy-page__header__content">
					<img src="<?php echo esc_url(plugins_url('../../assets/img/plugin-logo-white.svg', __FILE__)); ?>"
						 alt="Helpy Logo" class="helpy-page__header-logo"/>
					<p class="helpy-page__header-baseline">
						<?php echo esc_html__('for agencies and project managers who care about users.', 'helpy'); ?>
					</p>
				</div>
			</div>
			<div class="helpy-page__content">
				<ul class="helpy-page__content__menu">
					<li><a href="#global-links">
							<?php echo esc_html__('Liens globaux', 'helpy'); ?>
						</a></li>
					<li><a href="#post-types-links">
							<?php echo esc_html__('Liens par type de contenu', 'helpy'); ?>
						</a></li>
					<li><a href="#taxonomies-links">
							<?php echo esc_html__('Liens par taxonomie', 'helpy'); ?>
						</a></li>
					<li><a href="#ticketing">
							<?php echo esc_html__('Ticketing', 'helpy'); ?>
						</a></li>
					<li><a href="#import-export">
							<?php echo esc_html__('Import/Export', 'helpy'); ?>
						</a></li>
				</ul>
				<div id="global-links" class="helpy-page__content__menu-tab">
					<div class="helpy-page__content__menu-tab__content">
						<p><b><?php echo esc_html__('Liens d’aide globaux', 'helpy'); ?></b></p>
						<p><?php echo esc_html__('Ajoutez ici les liens de documentation, tutoriels vidéo ou pages de support que vous
							souhaitez rendre accessibles sur l’ensemble du site. Ces liens seront affichés dans le
							panneau “Aide” du tableau de bord et dans l’éditeur, pour tous les types de contenu.', 'helpy'); ?></p>
						<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
							<?php wp_nonce_field('helpy_save'); ?>
							<input type="hidden" name="action" value="helpy_save">
							<input type="hidden" name="scope_type" value="global">
							<input type="hidden" name="scope_key" value="global">
							<table class="widefat striped helpy-table">
								<thead>
								<tr>
									<th class="helpy-hidden">
										<?php echo esc_html__('Ordre', 'helpy'); ?>
									</th>
									<th><?php echo esc_html__('Label', 'helpy'); ?></th>
									<th><?php echo esc_html__('URL', 'helpy'); ?></th>
									<th><?php echo esc_html__('Cible', 'helpy'); ?></th>
									<th></th>
								</tr>
								</thead>
								<tbody id="helpy-global-tbody">
								<?php
								$rows = $grouped['global'] ?? [];
								foreach ($rows as $i => $r): ?>
									<tr>
										<td class="helpy-hidden"><input type="number"
																		name="items[<?php echo esc_attr((string) $i); ?>][sort_order]"
																		value="<?php echo (int) $r['sort_order']; ?>"/>
										</td>
										<td><input type="text" name="items[<?php echo esc_attr((string) $i); ?>][label]"
												   value="<?php echo esc_attr($r['label']); ?>"/></td>
										<td><input type="url" name="items[<?php echo esc_attr((string) $i); ?>][url]"
												   value="<?php echo esc_url($r['url']); ?>"/></td>
										<td>
											<select name="items[<?php echo esc_attr((string) $i); ?>][target]">
												<option value="_blank" <?php selected($r['target'], '_blank'); ?>>
													_blank
												</option>
												<option value="_self" <?php selected($r['target'], '_self'); ?>>_self
												</option>
											</select>
										</td>
										<td>
											<button type="button" class="button helpy-remove-row">
												<?php echo esc_html__('Supprimer', 'helpy'); ?>
											</button>
										</td>
									</tr>
								<?php endforeach; ?>
								</tbody>
							</table>
							<div class="helpy__form__buttons">
								<p>
									<button type="button" class="button button-outline-primary"
											data-add-row="#helpy-global-tbody">
										<?php echo esc_html__('Ajouter un lien', 'helpy'); ?>
									</button>
								</p>
								<p>
									<button type="submit" class="button button-secondary">
										<?php echo esc_html__('Enregistrer', 'helpy'); ?>
									</button>
								</p>
							</div>
						</form>
					</div>
				</div>
				<div id="post-types-links" class="helpy-page__content__menu-tab">
					<div class="helpy-page__content__menu-tab__content">
						<p><b><?php echo esc_html__('Liens spécifiques par type de contenu', 'helpy'); ?></b></p>
						<p><?php echo esc_html__('Vous pouvez définir ici des liens d’aide personnalisés pour chaque type de contenu (pages,
							articles, produits, etc.). Ces liens remplaceront ou compléteront les liens globaux afin
							d’offrir une aide ciblée selon le contexte.', 'helpy'); ?></p>
						<hr>
						<?php foreach ($postTypes as $slug => $obj):
							$rows = $grouped['post_type'][$slug] ?? []; ?>
							<details
								class="helpy-details" <?php if ($slug === array_key_first($postTypes)) : ?> open <?php endif; ?>>
								<summary>
									<h3 style="margin-top:0;"><?php echo esc_html($obj->labels->singular_name); ?></h3>
									<small><em><?php echo esc_html($slug); ?></em></small>
								</summary>
								<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
									<?php wp_nonce_field('helpy_save'); ?>
									<input type="hidden" name="action" value="helpy_save">
									<input type="hidden" name="scope_type" value="post_type">
									<input type="hidden" name="scope_key" value="<?php echo esc_attr($slug); ?>">
									<table class="widefat striped helpy-table">
										<thead>
										<tr>
											<th class="helpy-hidden">
												<?php echo esc_html__('Ordre', 'helpy'); ?>
											</th>
											<th><?php echo esc_html__('Label', 'helpy'); ?></th>
											<th><?php echo esc_html__('URL', 'helpy'); ?></th>
											<th><?php echo esc_html__('Cible', 'helpy'); ?></th>
											<th></th>
										</tr>
										</thead>
										<tbody id="helpy-<?php echo esc_attr($slug); ?>-tbody">
										<?php foreach ($rows as $i => $r): ?>
											<tr>
												<td class="helpy-hidden"><input type="number"
																		name="items[<?php echo esc_attr((string) $i); ?>][sort_order]"
																		value="<?php echo (int) $r['sort_order']; ?>"/>
												</td>
												<td><input type="text"
														   name="items[<?php echo esc_attr((string) $i); ?>][label]"
														   value="<?php echo esc_attr($r['label']); ?>"/></td>
												<td><input type="url"
														   name="items[<?php echo esc_attr((string) $i); ?>][url]"
														   value="<?php echo esc_url($r['url']); ?>"/></td>
												<td>
													<select name="items[<?php echo esc_attr((string) $i); ?>][target]">
														<option
															value="_blank" <?php selected($r['target'], '_blank'); ?>>
															_blank
														</option>
														<option value="_self" <?php selected($r['target'], '_self'); ?>>
															_self
														</option>
													</select>
												</td>
												<td>
													<button type="button" class="button helpy-remove-row">
														<?php echo esc_html__('Supprimer', 'helpy'); ?>
													</button>
												</td>
											</tr>
										<?php endforeach; ?>
										</tbody>
									</table>
									<div class="helpy__form__buttons">
										<p>
											<button type="button" class="button button-outline-primary"
													data-add-row="#helpy-<?php echo esc_attr($slug); ?>-tbody">
												<?php echo esc_html__('Ajouter un lien', 'helpy'); ?>
											</button>
										</p>
										<p>
											<button type="submit"
													class="button button-secondary"><?php echo esc_html__('Enregistrer', 'helpy'); ?></button>
										</p>
									</div>
								</form>
							</details>
							<?php if (next($postTypes)) : ?>
							<hr>
						<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
			<div id="taxonomies-links" class="helpy-page__content__menu-tab">
				<div class="helpy-page__content__menu-tab__content">
					<p><b><?php echo esc_html__('Liens d’aide par taxonomie', 'helpy'); ?></b></p>
					<p><?php echo esc_html__('Associez des liens de documentation ou de tutoriels à certaines taxonomies (catégories,
						étiquettes, etc.). Ces liens apparaîtront lors de l’édition des termes correspondants pour aider
						les utilisateurs à mieux comprendre leur utilisation.', 'helpy'); ?></p>
					<hr>
					<?php foreach ($taxonomies as $tax => $obj):
						$rows = $grouped['taxonomy'][$tax] ?? []; ?>
						<details
							class="helpy-details" <?php if ($tax === array_key_first($taxonomies)) : ?> open <?php endif; ?>>
							<summary>
								<h3 style="margin-top:0;"><?php echo esc_html($obj->labels->singular_name); ?></h3>
								<small><em><?php echo esc_html($tax); ?></em></small>
							</summary>
							<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
								<?php wp_nonce_field('helpy_save'); ?>
								<input type="hidden" name="action" value="helpy_save">
								<input type="hidden" name="scope_type" value="taxonomy">
								<input type="hidden" name="scope_key" value="<?php echo esc_attr($tax); ?>">
								<table class="widefat striped helpy-table">
									<thead>
									<tr>
										<th class="helpy-hidden">
											<?php echo esc_html__('Ordre', 'helpy'); ?>
										</th>
										<th><?php echo esc_html__('Label', 'helpy'); ?></th>
										<th><?php echo esc_html__('URL', 'helpy'); ?></th>
										<th><?php echo esc_html__('Cible', 'helpy'); ?></th>
										<th></th>
									</tr>
									</thead>
									<tbody id="helpy-tax-<?php echo esc_attr($tax); ?>-tbody">
									<?php foreach ($rows as $i => $r): ?>
										<tr>
											<td class="helpy-hidden"><input type="number"
																	name="items[<?php echo esc_attr((string) $i); ?>][sort_order]"
																	value="<?php echo (int) $r['sort_order']; ?>"/>
											</td>
											<td><input type="text"
												   name="items[<?php echo esc_attr((string) $i); ?>][label]"
												   value="<?php echo esc_attr($r['label']); ?>"/></td>
											<td><input type="url" name="items[<?php echo esc_attr((string) $i); ?>][url]"
												   value="<?php echo esc_url($r['url']); ?>"/></td>
											<td>
												<select name="items[<?php echo esc_attr((string) $i); ?>][target]">
													<option value="_blank" <?php selected($r['target'], '_blank'); ?>>
														_blank
													</option>
													<option value="_self" <?php selected($r['target'], '_self'); ?>>
														_self
													</option>
												</select>
											</td>
											<td>
												<button type="button" class="button helpy-remove-row">
													<?php echo esc_html__('Supprimer', 'helpy'); ?>
												</button>
											</td>
										</tr>
									<?php endforeach; ?>
									</tbody>
								</table>
								<div class="helpy__form__buttons">
									<p>
										<button type="button" class="button button-outline-primary"
												data-add-row="#helpy-tax-<?php echo esc_attr($tax); ?>-tbody">
											<?php echo esc_html__('Ajouter un lien', 'helpy'); ?>
										</button>
									</p>
									<p>
										<button type="submit"
												class="button button-secondary"><?php echo esc_html__('Enregistrer', 'helpy'); ?></button>
									</p>
								</div>

							</form>
						</details>
						<?php if (next($taxonomies)) : ?>
						<hr>
					<?php endif; ?>
					<?php endforeach; ?>
				</div>
			</div>
			<div id="ticketing" class="helpy-page__content__menu-tab">
				<div class="helpy-page__content__menu-tab__content">
					<p><b><?php echo esc_html__('Configuration du ticketing', 'helpy'); ?></b></p>
					<p><?php echo esc_html__('Définissez ici les paramètres permettant de créer un ticket directement depuis le tableau de
						bord. Vous pouvez renseigner l’URL de votre outil de support (Redmine, Jira, Trello, etc.) ou un
						lien vers un formulaire de contact afin que les utilisateurs puissent soumettre leurs demandes
						facilement.', 'helpy'); ?></p>
					<hr>
					<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
						<?php wp_nonce_field('helpy_save'); ?>
						<input type="hidden" name="action" value="helpy_save">
						<input type="hidden" name="scope_type" value="ticketing">
						<table class="form-table">
							<tr>
								<th><?php echo esc_html__('Activer le bouton vers l’outil de support', 'helpy'); ?></th>
								<td><label><input type="checkbox" name="enabled"
													  value="1" <?php checked($ticketing['enabled']); ?>></label></td>
							</tr>
							<tr>
								<th><?php echo esc_html__('URL de base', 'helpy'); ?></th>
								<td><input type="url" name="base_url" class="regular-text"
										   value="<?php echo esc_attr($ticketing['base_url']); ?>"
										   placeholder="https://yourdesk.example.com"></td>
							</tr>
							<tr>
								<th><?php echo esc_html__('Projet (facultatif)', 'helpy'); ?></th>
								<td><input type="text" name="project" class="regular-text"
										   value="<?php echo esc_attr($ticketing['project']); ?>"></td>
							</tr>
							<tr>
								<th><?php echo esc_html__('Chemin du nouveau ticket', 'helpy'); ?></th>
								<td><input type="text" name="new_issue_path" class="regular-text"
										   value="<?php echo esc_attr($ticketing['new_issue_path']); ?>"
										   placeholder="/new?project={project}&title={title}"></td>
							</tr>
							<tr>
								<th><?php echo esc_html__('Libellé du bouton', 'helpy'); ?></th>
								<td><input type="text" name="button_label" class="regular-text"
										   value="<?php echo esc_attr($ticketing['button_label']); ?>"></td>
							</tr>
						</table>
						<div class="helpy__form__buttons">
							<p>
								<button type="submit"
										class="button button-secondary"><?php echo esc_html__('Enregistrer', 'helpy'); ?></button>
							</p>
						</div>
					</form>
				</div>
			</div>
			<div id="import-export" class="helpy-page__content__menu-tab">
				<div class="helpy-page__content__menu-tab__content">
					<p><b><?php echo esc_html__('Importer une configuration', 'helpy'); ?></b></p>
					<p><?php echo esc_html__('Chargez un fichier JSON précédemment exporté pour restaurer les réglages du plugin. L’import remplacera les paramètres existants par ceux contenus dans le fichier.', 'helpy'); ?></p>
					<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
						<?php wp_nonce_field('helpy_import'); ?>
						<input type="hidden" name="action" value="helpy_import">
						<p><textarea name="payload" rows="8" cols="80" style="width:100%;"
									 placeholder="<?php echo esc_attr__('Collez le JSON ici', 'helpy'); ?>"></textarea></p>
						<div class="helpy__form__buttons">
							<p>
								<button type="submit" class="button button-secondary">
									<?php echo esc_html__('Importer la configuration', 'helpy'); ?>
								</button>
							</p>
						</div>
					</form>
					<hr>
					<p><b><?php echo esc_html__('Exporter vos réglages', 'helpy'); ?></b></p>
					<p><?php echo esc_html__('Sauvegardez la configuration actuelle du plugin au format JSON. Vous pourrez ainsi la réutiliser plus tard ou la partager avec un autre site pour retrouver exactement les mêmes paramètres.', 'helpy'); ?></p>
					<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
						<?php wp_nonce_field('helpy_export'); ?>
						<input type="hidden" name="action" value="helpy_export">
						<button type="submit" class="button"><?php echo esc_html__('Exporter la configuration JSON', 'helpy'); ?></button>
					</form>
				</div>
			</div>
		</div>
		<?php
	}

	public static function handleSave(): void
	{
		if (!current_user_can('manage_options')) wp_die('Forbidden');
		check_admin_referer('helpy_save');

		$scopeType = isset($_POST['scope_type']) ? sanitize_text_field(wp_unslash($_POST['scope_type'])) : '';

		if (in_array($scopeType, ['global', 'post_type', 'taxonomy'], true)) {
			$scopeKey = isset($_POST['scope_key']) ? sanitize_text_field(wp_unslash($_POST['scope_key'])) : '';
			if ($scopeType === 'global') $scopeKey = Scope::GLOBAL;

			$rawItems = isset($_POST['items']) && is_array($_POST['items']) ? wp_unslash($_POST['items']) : [];

			$items = array_values(array_map(function ($row) {
				if (!is_array($row)) {
					$row = [];
				}

				return [
					'sort_order' => intval($row['sort_order'] ?? 0),
					'label' => sanitize_text_field($row['label'] ?? ''),
					'url' => esc_url_raw($row['url'] ?? ''),
					'target' => ($row['target'] ?? '_blank') === '_self' ? '_self' : '_blank',
				];
			}, $rawItems));

			$items = array_values(array_filter($items, fn($i) => $i['label'] && $i['url']));

			$repo = new LinkRepository();
			$repo->deleteScope($scopeType, $scopeKey);
			$repo->bulkInsert($scopeType, $scopeKey, $items);
		} elseif ($scopeType === 'ticketing') {
			(new OptionsRepository())->set('ticketing', [
				'enabled' => isset($_POST['enabled']) && '1' === wp_unslash($_POST['enabled']),
				'base_url' => isset($_POST['base_url']) ? esc_url_raw(wp_unslash($_POST['base_url'])) : '',
				'project' => isset($_POST['project']) ? sanitize_text_field(wp_unslash($_POST['project'])) : '',
				'new_issue_path' => isset($_POST['new_issue_path']) ? sanitize_text_field(wp_unslash($_POST['new_issue_path'])) : '/new?project={project}',
				'button_label' => isset($_POST['button_label']) ? sanitize_text_field(wp_unslash($_POST['button_label'])) : __('Create ticket', 'helpy'),
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

		$payload = isset($_POST['payload']) ? wp_unslash((string)$_POST['payload']) : '';
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
