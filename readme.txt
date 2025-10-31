=== Helpy ===
Contributors: denisdums
Donate link: https://denisdums.com/
Tags: help, admin, dashboard, gutenberg, documentation, tutorial, ticketing, workflow, client, taxonomy
Requires at least: 6.2
Tested up to: 6.8.3
Requires PHP: 8.0
Stable tag: 1.2.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Contextual help links for clients, directly in the WordPress editor, dashboard, and taxonomy screens.

== Description ==

Helpy is a lightweight WordPress plugin built for web agencies and project managers who want to offer contextual help and training resources directly inside the WordPress admin.

It displays a “Helpy” panel inside the editor sidebar (Gutenberg) or as a classic metabox, adds a Dashboard widget with global links, and now supports displaying contextual help inside taxonomy term edit pages.

You can define links **globally**, **per post type**, **per taxonomy**, or **per specific term** - ideal for showing clients Loom or YouTube tutorials, documentation links, or a “Create ticket” button linked to your favorite ticketing tool (ClickUp, Jira, Notion, Redmine, etc.).

The plugin is fully object-oriented, uses custom database tables, and provides a clean admin interface with collapsible panels and JSON import/export.

== Features ==

* Contextual help panel in the editor sidebar (Gutenberg)
* Fallback metabox in the Classic Editor
* Dashboard widget with global help links
* Per post type, taxonomy, or term help lists
* Link types: Video (Loom, YouTube, Vimeo), Documentation, or Custom URL
* Generic ticketing integration (Jira, Notion, ClickUp, Redmine, etc.)
* Simple admin settings page with collapsible panels
* JSON import/export for duplicating configuration
* Custom DB tables (`wp_helpy_links`, `wp_helpy_options`)
* WP-CLI commands for export/import/doctor/seed
* Fully OOP architecture following WordPress coding standards
* Safe sanitization, capability checks, and uninstall cleanup

== Typical Use Cases ==

* Delivering WordPress sites to clients who need onboarding or tutorials
* Internal documentation or training for content editors
* Centralizing links for projects using an external ticketing system
* Providing contextual documentation for categories or product taxonomies

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

1. Upload the `helpy` folder to the `/wp-content/plugins/` directory.
2. Activate Helpy through the Plugins screen.
3. Go to **Settings → Helpy** to configure global, post type, and taxonomy links.
4. (Optional) Configure your ticketing tool (base URL and project name).
5. Edit a post, page, or term - the Helpy box will appear in the sidebar or in the term editor.

== Frequently Asked Questions ==

= Does Helpy work without Gutenberg? =

Yes. If Gutenberg is disabled, Helpy automatically uses a classic metabox instead of the sidebar panel.

= Can I use it with taxonomies and terms? =

Yes. Helpy can display links on taxonomy edit pages and inside individual term forms (e.g. categories, tags, WooCommerce product categories).

= How does the ticketing button work? =

Helpy provides a generic ticketing system.  
You define a base URL and placeholders (e.g. `{project}`, `{title}`, `{term}`, `{postType}`), and it automatically builds a link to your issue creation page.

Examples:
- `https://jira.example.com/create?project={project}&title={title}`
- `https://clickup.com/t/create?name={title}`
- `https://redmine.example.com/projects/{project}/issues/new`

= Where are the settings stored? =

Helpy uses its own custom tables (`wp_helpy_links` and `wp_helpy_options`) to avoid polluting the `wp_options` table.

= Can I export or migrate my configuration? =

Yes. You can export or import your entire configuration as a JSON file from the admin settings page or via WP-CLI.

== Screenshots ==

1. Admin settings page with collapsible panels for Global Links, Post Types, Taxonomies, and Ticketing.
2. Helpy panel inside the Gutenberg editor sidebar.
3. Dashboard widget showing global help links.
4. Helpy box above taxonomy term edit forms.

== Changelog ==

= 0.1.2 =
* Added support for taxonomy and term-specific help links.
* Replaced Redmine integration with a generic ticketing system.
* Added new `wp_helpy_options` table for plugin configuration.
* Updated Import/Export and WP-CLI to handle taxonomy/term data.
* Improved admin UI with collapsible panels.

= 0.1.1 =
* Introduced generic ticketing system replacing Redmine.
* Added internal `helpy_options` table.
* UI and style improvements.

= 0.1.0 =
* Initial release.
* Gutenberg sidebar panel + Classic Editor fallback.
* Dashboard widget.
* Custom DB tables for configuration.
* JSON import/export + WP-CLI support.
* Fully OOP plugin architecture.

== Upgrade Notice ==

= 1.2.0 =
Adds taxonomy and term-level contextual help, a generic ticketing system, and improved import/export.  
Update recommended for expanded flexibility.

== Developer Notes ==

Namespaces:

* Helpy\Admin - Admin settings UI
* Helpy\Editor - Gutenberg + Classic editor logic
* Helpy\Dashboard - Dashboard widget
* Helpy\Taxonomy - Term and taxonomy support
* Helpy\DB - Database and repositories
* Helpy\Application - Core services
* Helpy\CLI - WP-CLI commands

Custom tables:

* wp_helpy_links
* wp_helpy_options

Schema version stored in `helpy_schema_version`.

Available placeholders for ticketing links:

`{project}`, `{title}`, `{postId}`, `{postType}`, `{taxonomy}`, `{termId}`, `{term}`

Helpy follows WordPress security best practices (nonces, capabilities, sanitization, uninstall cleanup).

== Example ==

1. Add or edit a post or page.
2. Look for the **Helpy** panel on the right.
3. Click on a link to open a tutorial or to view documentation.
4. Use the **Create a ticket** button to report issues.
5. Edit a taxonomy term - the Helpy box appears above the form with relevant links.

== Credits ==

Built with ❤️ by [Denis Dumont](https://denisdums.com/)  
for agencies and project managers who care about users
