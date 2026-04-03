<?php

declare(strict_types=1);

namespace WPSecureDefaults\Core;

/**
 * Simple PSR-4 autoloader for the WPSecureDefaults namespace.
 *
 * Maps WPSecureDefaults\ to the src/ directory using the plugin
 * constant WP_SECURE_DEFAULTS_DIR defined in the bootstrap file.
 */
final class Autoloader
{
    private const NAMESPACE_PREFIX = 'WPSecureDefaults\\';

    public static function register(): void
    {
        spl_autoload_register([self::class, 'autoload']);
    }

    public static function autoload(string $class): void
    {
        // Only handle classes in our namespace
        if (!str_starts_with($class, self::NAMESPACE_PREFIX)) {
            return;
        }

        // Strip the namespace prefix and convert namespace separators to directory separators
        $relativePath = str_replace(
            '\\',
            DIRECTORY_SEPARATOR,
            substr($class, strlen(self::NAMESPACE_PREFIX))
        );

        $filePath = WP_SECURE_DEFAULTS_DIR . 'src' . DIRECTORY_SEPARATOR . $relativePath . '.php';

        // Fail silently — other autoloaders in the chain should still get a chance
        if (file_exists($filePath)) {
            require_once $filePath;
        }
    }
}
