=== Helpy ===
Contributors: denisdums
Donate link: https://denisdums.com/
Tags: help, admin, dashboard, gutenberg, documentation, tutorial, redmine, workflow, client
Requires at least: 6.2
Tested up to: 6.8.3
Requires PHP: 8.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Contextual help and video tutorial links for clients, right inside the WordPress dashboard and editor.

== Description ==

Helpy is a lightweight WordPress plugin built for web agencies and project managers who want to offer contextual help and training resources directly in the WordPress back office.

It displays a “Help” panel inside the editor sidebar (Gutenberg) or as a classic metabox, and adds a Dashboard widget with global documentation links. You can define help links globally or per post type — ideal for showing clients Loom or YouTube tutorials, documentation, or a “Create ticket” button linked to your Redmine project.

The plugin is fully object-oriented, uses its own database tables, and provides a clean admin interface with import/export tools.

== Features ==

* Contextual help box in the editor sidebar (Gutenberg)
* Fallback metabox in the Classic Editor
* Dashboard widget with global help links
* Per post type or global help lists
* Link types: Video (Loom, YouTube, Vimeo), Documentation, Custom URL
* Optional Redmine integration (“Create ticket” button)
* Simple admin page with collapsible panels
* JSON import/export for duplicating configuration
* Custom DB tables (no data stored in wp_options)
* WP-CLI commands for export/import/doctor/seed
* Fully OOP architecture following WordPress coding standards
* Safe sanitization, capability checks, uninstall cleanup

== Typical Use Cases ==

* Delivering WordPress sites to clients who need onboarding or video tutorials
* Internal documentation or training for content editors
* Projects managed via Redmine or other issue trackers

== WP-CLI Integration ==

# Check installation
wp helpy doctor

# Export configuration to JSON
wp helpy export --out=helpy.json

# Import configuration from JSON
wp helpy import --file=helpy.json

# Seed example data
wp helpy seed

== Installation ==

1. Upload the helpy folder to the /wp-content/plugins/ directory.
2. Activate Helpy through the Plugins screen.
3. Go to Settings → Aide projet (Helpy) to configure your links.
4. (Optional) Configure Redmine (base URL and project identifier).
5. Edit a post or page — the Helpy box will appear in the sidebar (or as a metabox if using the Classic Editor).

== Frequently Asked Questions ==

= Does Helpy work without Gutenberg? =

Yes. If Gutenberg is disabled, Helpy automatically uses a classic metabox instead of the sidebar panel.

= Where are the settings stored? =

Helpy uses its own custom tables (wp_helpy_links and wp_helpy_redmine) to avoid polluting the wp_options table.

= Can I export or migrate my configuration? =

Yes. You can export or import your entire configuration as a JSON file from the admin settings page or via WP-CLI.

= How does the Redmine button work? =

If enabled, Helpy displays a “Create ticket” button that opens your Redmine issue creation page. Authentication is handled by Redmine — no API tokens or credentials are stored.

== Screenshots ==

1. Admin settings page with collapsible panels for Global Links, Post Type Links, Redmine, and Import/Export.
2. Helpy panel inside the Gutenberg editor sidebar.
3. Dashboard widget showing global help links.
4. Classic editor metabox fallback view.

== Changelog ==

= 1.0.0 =
* Initial release.
* Gutenberg sidebar panel + Classic metabox fallback.
* Dashboard widget.
* Redmine integration.
* Custom DB tables for configuration.
* Admin settings page with collapsible panels.
* JSON import/export + WP-CLI support.
* Fully OOP plugin architecture.

== Upgrade Notice ==

= 1.0.0 =
First stable release — adds contextual help system with Redmine integration, Gutenberg panel, and dashboard widget.

== Developer Notes ==

Namespaces:

* Helpy\Admin — Admin settings UI.
* Helpy\Editor — Gutenberg + Classic editor logic.
* Helpy\Dashboard — Dashboard widget.
* Helpy\DB — Database and repositories.
* Helpy\Application — Core services.
* Helpy\CLI — WP-CLI commands.

Custom tables:

* wp_helpy_links
* wp_helpy_redmine

Schema version is stored in helpy_schema_version for migrations.

Helpy follows WordPress security best practices (nonces, capabilities, sanitization, uninstall cleanup).

== Example ==

1. Add or edit a post or page.
2. Look for the Helpy panel on the right.
3. Click 🎥 to open a tutorial or 📄 to view documentation.
4. Use the Create a ticket button to report issues (if Redmine is enabled).

== Credits ==

Built with ❤️ by Denis Dumont (https://denisdums.com/)  
for creative agencies and project managers who care about user autonomy.
