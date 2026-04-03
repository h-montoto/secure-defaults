# Secure Defaults

A lightweight, production-ready WordPress plugin that applies opinionated security hardening as a baseline for any WordPress installation.

It works entirely through WordPress hooks — no database writes, no persistent state. Deactivating the plugin fully restores the original behaviour.

---

## Requirements

- PHP **8.0** or higher
- WordPress **6.0** or higher

---

## Installation

1. Copy the `secure-defaults/` directory into `wp-content/plugins/`.
2. Activate the plugin from **Plugins > Installed Plugins**.

That's it. All features are enabled by default.

---

## What it does

### Security

| Feature | Filter to disable |
|---------|-------------------|
| Disable comments globally (all post types, REST endpoint, admin UI) | `secure_defaults_disable_comments` |
| Disable XML-RPC and pingbacks | `secure_defaults_disable_xmlrpc` |
| Restrict REST API to authenticated users only | `secure_defaults_restrict_rest_api` |
| Remove `/wp/v2/users` REST endpoint | `secure_defaults_restrict_rest_users` |
| Block `?author=N` user enumeration | `secure_defaults_prevent_user_enumeration` |
| Disable file editor in wp-admin (`DISALLOW_FILE_EDIT`) | `secure_defaults_admin_hardening` |
| Remove dashboard widgets (Recent Comments, WP Events & News) | `secure_defaults_admin_hardening` |

### Cleanup

| Feature | Filter to disable |
|---------|-------------------|
| Remove RSD, WLW, generator, shortlink, REST link from `<head>` | `secure_defaults_clean_head` |
| Remove WP version from RSS feeds (`the_generator`) | `secure_defaults_clean_head` |
| Disable emoji scripts, styles, and DNS prefetch | `secure_defaults_disable_emojis` |
| Disable oEmbed scripts, rewrite rules, and REST route | `secure_defaults_disable_embeds` |

---

## Configuration

Every feature can be toggled via a WordPress filter. Add the relevant code to your theme's `functions.php` or a site-specific plugin.

### Disable a single feature

```php
// Keep comments enabled
add_filter('secure_defaults_disable_comments', '__return_false');

// Keep XML-RPC enabled
add_filter('secure_defaults_disable_xmlrpc', '__return_false');

// Allow public REST API access
add_filter('secure_defaults_restrict_rest_api', '__return_false');

// Keep /wp/v2/users endpoint available
add_filter('secure_defaults_restrict_rest_users', '__return_false');

// Allow author enumeration
add_filter('secure_defaults_prevent_user_enumeration', '__return_false');

// Allow file editing in wp-admin
add_filter('secure_defaults_admin_hardening', '__return_false');

// Keep full <head> output
add_filter('secure_defaults_clean_head', '__return_false');

// Keep emoji support
add_filter('secure_defaults_disable_emojis', '__return_false');

// Keep oEmbed support
add_filter('secure_defaults_disable_embeds', '__return_false');
```

### Disable everything (use as a conditional baseline)

```php
$features = [
    'disable_comments',
    'disable_xmlrpc',
    'restrict_rest_api',
    'restrict_rest_users',
    'prevent_user_enumeration',
    'admin_hardening',
    'clean_head',
    'disable_emojis',
    'disable_embeds',
];

foreach ($features as $feature) {
    add_filter("secure_defaults_{$feature}", '__return_false');
}
```

---

## What it does NOT do automatically

These require server-level configuration and are intentionally outside the scope of a PHP plugin:

- **Block `wp-comments-post.php`** directly (recommended via `.htaccess` or nginx):
  ```nginx
  location = /wp-comments-post.php { deny all; }
  ```

- **Block `xmlrpc.php`** at the web server level:
  ```nginx
  location = /xmlrpc.php { deny all; }
  ```

- **Security response headers** (Content-Security-Policy, X-Frame-Options, etc.) — set these in your web server config or a dedicated header plugin.

---

## Safety guarantees

- **REST API**: Logged-in users (including the Gutenberg block editor) are never blocked. The restriction only applies to unauthenticated requests. Explicit authentication from other plugins (Application Passwords, OAuth) is respected.
- **DISALLOW_FILE_EDIT**: Checked with `defined()` before defining — safe if already set in `wp-config.php`.
- **No side effects on deactivation**: All changes are runtime hooks. Deactivating the plugin restores WordPress defaults immediately.
- **Author archives**: Logged-in users can still access author archives; only unauthenticated `?author=N` probing is blocked.

---

## License

GPL-2.0-or-later — see [https://www.gnu.org/licenses/gpl-2.0.html](https://www.gnu.org/licenses/gpl-2.0.html)
