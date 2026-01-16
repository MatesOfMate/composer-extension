<?php

/*
 * This file is part of the MatesOfMate Organisation.
 *
 * (c) Johannes Wachter <johannes@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use MatesOfMate\Common\Process\ProcessExecutor;
use MatesOfMate\ComposerExtension\Capability\ConfigResource;
use MatesOfMate\ComposerExtension\Capability\InstallTool;
use MatesOfMate\ComposerExtension\Capability\RemoveTool;
use MatesOfMate\ComposerExtension\Capability\RequireTool;
use MatesOfMate\ComposerExtension\Capability\UpdateTool;
use MatesOfMate\ComposerExtension\Capability\WhyNotTool;
use MatesOfMate\ComposerExtension\Capability\WhyTool;
use MatesOfMate\ComposerExtension\Config\ConfigurationDetector;
use MatesOfMate\ComposerExtension\Formatter\ToonFormatter;
use MatesOfMate\ComposerExtension\Parser\OutputParser;
use MatesOfMate\ComposerExtension\Runner\ComposerRunner;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container): void {
    $services = $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure();

    // Core infrastructure - composer is found via PATH using ExecutableFinder
    $services->set('matesofmate_composer.process_executor', ProcessExecutor::class);
    $services->set(ComposerRunner::class)
        ->arg('$executor', service('matesofmate_composer.process_executor'));

    $services->set(OutputParser::class);
    $services->set(ToonFormatter::class);

    // Configuration detection
    $services->set(ConfigurationDetector::class)
        ->arg('$projectRoot', '%mate.root_dir%');

    // Tools - automatically discovered by #[McpTool] attribute
    $services->set(InstallTool::class);
    $services->set(RemoveTool::class);
    $services->set(RequireTool::class);
    $services->set(UpdateTool::class);
    $services->set(WhyTool::class);
    $services->set(WhyNotTool::class);

    // Resources - automatically discovered by #[McpResource] attribute
    $services->set(ConfigResource::class);
};
