<?php

declare(strict_types=1);

namespace SecureDefaults\Core;

use SecureDefaults\Cleanup;
use SecureDefaults\Security;
use SecureDefaults\Utils\Config;

/**
 * Main plugin orchestrator.
 *
 * Reads the Config to determine which modules are enabled, instantiates
 * only those modules, and calls register() on each one.
 */
final class Plugin
{
    /** @var array<int, object> */
    private array $modules = [];

    public function run(): void
    {
        $this->registerModules();

        foreach ($this->modules as $module) {
            $module->register();
        }
    }

    private function registerModules(): void
    {
        /**
         * Maps each feature key (from Config) to its module class.
         * Modules are only instantiated when their feature is enabled.
         *
         * @var array<string, class-string>
         */
        $map = [
            'disable_comments'         => Security\CommentsDisabler::class,
            'disable_xmlrpc'           => Security\XmlRpcDisabler::class,
            'restrict_rest_api'        => Security\RestApiHardener::class,
            'prevent_user_enumeration' => Security\UserEnumerationProtection::class,
            'admin_hardening'          => Security\AdminHardener::class,
            'clean_head'               => Cleanup\HeadCleanup::class,
            'disable_emojis'           => Cleanup\EmojiDisabler::class,
            'disable_embeds'           => Cleanup\EmbedDisabler::class,
        ];

        foreach ($map as $feature => $className) {
            if (Config::isEnabled($feature)) {
                $this->modules[] = new $className();
            }
        }
    }
}
