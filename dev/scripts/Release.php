<?php

namespace OpenMage\Scripts;

use Composer\Script\Event;
use Composer\Semver\Constraint;
use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class Release
{
    /** @var Symfony\Component\Console\Output\ConsoleOutput $output */
    static $output;

    /** @var Composer\Composer $composer */
    static $composer;

    /** @var string $vendorPath */
    static $vendorPath;

    private static function _init(Event $event)
    {
        self::$output = new ConsoleOutput();
        self::$composer = $event->getComposer();
        self::$vendorPath = self::$composer->getConfig()->get('vendor-dir');
    }

    /**
     * Run all commands
     *
     * @param Event $event
     */
    public static function run(Event $event)
    {
        self::updatePackages($event);
        self::clean($event);
    }

    /**
     * Remove packages not needed for the release ZIP while keeping sub-dependencies
     *
     * @param Event $event
     */
    public static function updatePackages(Event $event)
    {
        self::_init($event);

        // Get all installed packages including dependencies
        /** @var Composer\Package\CompletePackage[] $packages */
        $packages = self::$composer->getRepositoryManager()->getLocalRepository()->getCanonicalPackages();

        // List of packages for `composer require`
        /** @var (Constraint\ConstraintInterface|string)[] $require */
        $require = [
            // We can manually add packages here
            'symfony/polyfill-intl-idn' => ["^1.26"],
        ];

        // List of packages for `composer remove`
        /** @var string[] $remove */
        $remove = [
            // We can manually remove packages here
            // 'magento-hackathon/magento-composer-installer',
        ];

        /** @var Composer\Package\CompletePackage $package */
        foreach ($packages as $package) {

            /** @var string $type */
            $type = $package->getType();

            // Remove Magento module from composer.json, but we need to separately install its dependencies
            if ($type === 'magento-module') {
                /** @var Composer\Package\Link $dependency */
                foreach ($package->getRequires() as $name => $dependency) {
                    $require[$name] = $require[$name] ?? [];
                    $require[$name][] = $dependency->getConstraint();
                }
                $remove[] = $package->getName();
            }

            // Remove this repo when installed via aydin-hassan/magento-core-composer-installer
            if ($type === 'magento-source') {
                $remove[] = $package->getName();
            }

            // Remove any composer plugins, such as magento-hackathon/magento-composer-installer
            if ($type === 'composer-plugin') {
                $remove[] = $package->getName();
            }
        }

        /** @var string[] $require */
        $require = array_map(
            function($name, $constraints) {
                // Combine constraints from all packages that required this dependency
                // This does not necessarily return a MultiConstraint instance if
                // things can be reduced to a simple constraint
                /** @var Composer\Semver\Constraint\ConstraintInterface $constraint */
                $constraint = Constraint\MultiConstraint::create(array_unique($constraints));

                // Return string that can be used for composer install, i.e.:
                // "colinmollenhour/php-redis-session-abstract:>= 1.4.0.0-dev < 1.5.0.0-dev"
                return $name . ':' . trim($constraint, '[]');
            },
            array_keys($require), $require
        );

        // Bootstrap composer application instance
        /** @var Composer\Console\Application $app */
        $app = new Application();
        $app->setAutoExit(false);

        $app->run(
            new ArrayInput([
                'command' => 'remove',
                '--no-plugins' => true,
                '--update-no-dev' => true,
                '--ignore-platform-req' => ['ext-*'],
                'packages' => $remove,
            ]),
            self::$output
        );

        $app->run(
            new ArrayInput([
                'command' => 'require',
                '--no-plugins' => true,
                '--update-no-dev' => true,
                '--ignore-platform-req' => ['ext-*'],
                'packages' => array_values($require),
            ]),
            self::$output
        );

        // // Alternatively we could use exec
        // exec('composer remove --no-plugins --update-no-dev --ignore-platform-req=ext-* ' . implode(' ', $remove));
        // exec('composer require --no-plugins --update-no-dev --ignore-platform-req=ext-* ' . '"' . implode('" "', $require) . '"');

    }

    /**
     * Remove files such as markdown and unneeded files
     *
     * @param Event $event
     */
    public static function clean(Event $event)
    {
        self::_init($event);

        $filesystem = new Filesystem();

        // Remove markdown files
        {
            $finder = Finder::create()
                    ->in(self::$vendorPath)
                    ->files()
                    ->name('*.md')
                    ->name('*.markdown');

            $filesystem->remove($finder);
        }

        // Remove .git directories
        {
            $finder = Finder::create()
                    ->in(self::$vendorPath)
                    ->directories()
                    ->ignoreDotFiles(false)
                    ->ignoreVCS(false)
                    ->name('.git');

            $filesystem->remove($finder);
        }
    }
}
