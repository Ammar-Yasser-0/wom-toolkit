=== Mirox Toolkit ===
Contributors: mirox
Requires at least: 6.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.2.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A lightweight modular toolkit for frontend enhancements and admin experience in WordPress.

== Description ==

Mirox Toolkit is a modular WordPress plugin that provides lightweight frontend and admin enhancements. Each module can be independently enabled or disabled from the admin dashboard.

**Modules:**

* **Custom Cursor** — Desktop-only custom cursor with dot, ring, hover state, and click pulse animation. Fully configurable colors, sizes, and speeds. Respects `prefers-reduced-motion`.
* **Smooth Scrolling** — Lenis-based smooth scrolling with built-in anchor support. Configurable duration, wheel/touch multipliers, and mobile breakpoint. Respects `prefers-reduced-motion`.
* **Login Branding** — Customize the WordPress login page logo, form styling, background (solid or gradient), and link colors. Includes a media uploader for logo selection.
* **Scrollbar Styles** — Customize the frontend scrollbar appearance with WebKit pseudo-elements and standard `scrollbar-color`/`scrollbar-width` properties. Includes RTL support.

**Features:**

* Modular architecture — enable only what you need
* Settings stored per-module in a single option
* GitHub-based automatic updates
* Responsive admin UI
* No external dependencies beyond bundled Lenis

== Installation ==

1. Upload the `wom-toolkit` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Navigate to **Mirox Toolkit** in the admin menu.
4. Enable desired modules and configure their settings.

== Frequently Asked Questions ==

= Does this plugin affect performance when modules are disabled? =

No. Disabled modules are not loaded on the frontend. Only the admin UI and module discovery run on every request, with negligible overhead.

= Is the custom cursor compatible with Elementor? =

Yes. The cursor includes Elementor-specific hover selectors (buttons, icons, image boxes) by default. Developers can extend the selector list using the `wom_toolkit_cursor_hover_selectors` filter.

= Does smooth scrolling work on mobile? =

Smooth scrolling is automatically disabled below the configured breakpoint (default: 992px) and when the user has `prefers-reduced-motion` enabled.

= Is the plugin compatible with WooCommerce? =

Yes. The custom cursor recognizes standard WooCommerce button and link elements. Smooth scrolling does not interfere with WooCommerce tab navigation.

= Can I add custom hover selectors for the cursor? =

Yes. Use the `wom_toolkit_cursor_hover_selectors` filter to modify the comma-separated selector string.

== Screenshots ==

1. Admin dashboard with module overview
2. Module toggle interface
3. Custom cursor settings
4. Smooth scrolling settings
5. Login branding settings
6. Scrollbar styles settings

== Changelog ==

= 1.2.1 =
* Fixed: Release ZIP paths generated on Windows that could extract incorrectly on Linux servers and cause "Plugin file does not exist" errors.
* Added: Deterministic cross-platform release packaging via tools/build-release.py.
* Added: Automated ZIP path validation (zero backslash entries enforced).
* Added: Linux extraction verification in CI workflow.

= 1.2.0 =
* Security: Added nonce verification and capability checks to Custom Cursor settings
* Security: Added nonce verification and capability checks to Smooth Scrolling settings
* Fix: Login Branding filters now respect module enabled/disabled state
* Fix: Login Branding CSS inputs validated against whitelists (object-fit, object-position, background type)
* Fix: Smooth scrolling anchor handling no longer crashes on special-character IDs
* Fix: Smooth scrolling no longer intercepts Elementor tabs, accordions, popups, or WooCommerce tabs
* Fix: GitHub updater uses explicit tested WordPress version instead of runtime version
* Fix: GitHub updater caches failed API requests for 30 minutes
* Fix: GitHub updater validates HTTPS for package URLs
* Improvement: Smooth scrolling now uses Lenis built-in anchor support
* Improvement: Smooth scrolling handles viewport resize with debounced breakpoint detection
* Improvement: Smooth scrolling respects `prefers-reduced-motion` via Lenis
* Improvement: Custom cursor now initializes on DOMContentLoaded instead of window.load
* Improvement: Custom cursor pauses animation loop when tab is hidden
* Improvement: Custom cursor respects `prefers-reduced-motion`
* Improvement: Custom cursor hover selectors are filterable via `wom_toolkit_cursor_hover_selectors`
* Improvement: Added Firefox scrollbar support via `scrollbar-color` and `scrollbar-width`
* Improvement: Updated bundled Lenis from 1.3.8 to 1.3.26
* Improvement: Added recommended Lenis CSS for correct behavior
* Improvement: Added plugin header metadata (Requires at least, Requires PHP, License, Update URI)
* Improvement: Removed unused `WOM_TOOLKIT_GITHUB_BRANCH` constant
* Improvement: Sanitized all inline CSS generation with proper type casting
* Cleanup: Removed empty dead files (templates/admin-page.php, assets/admin/admin.js, modules/login-branding/assets/css/admin.css, core/helpers.php, modules/smooth-scrolling/module.json)
* Cleanup: Added .gitignore, .gitattributes, .distignore for release packaging
* Added GitHub Actions CI workflow for PHP syntax checking

= 1.1.0 =
* Initial public release with Custom Cursor, Smooth Scrolling, Login Branding, and Scrollbar Styles modules
* GitHub-based automatic updates

== Upgrade Notice ==

= 1.2.0 =
Security hardening and browser compatibility improvements. All existing settings are preserved.

== Additional Note ==

This plugin bundles [Lenis](https://github.com/darkroomengineering/lenis) v1.3.26 under the MIT license for smooth scrolling functionality.
