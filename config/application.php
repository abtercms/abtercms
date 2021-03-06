<?php

declare(strict_types=1);

use Opulence\Framework\Configuration\Config;
use Opulence\Ioc\Bootstrappers\BootstrapperResolver;
use Opulence\Ioc\IContainer;

/**
 * ----------------------------------------------------------
 * Load some global config settings
 * ----------------------------------------------------------
 *
 * @var array $paths The list of paths
 */
Config::setCategory('paths', $paths);
Config::setCategory('views', require __DIR__ . '/http/views.php');
Config::setCategory('authorization', require __DIR__ . '/authorization.php');
Config::setCategory('templates', require __DIR__ . '/templates.php');
Config::setCategory('cache', require __DIR__ . '/cache.php');

/**
 * ----------------------------------------------------------
 * Set up the bootstrappers
 * ----------------------------------------------------------
 *
 * The following starts settings up the bootstrappers
 */
$container = require __DIR__ . '/ioc.php';
$bootstrapperResolver = new BootstrapperResolver();
$globalBootstrapperPath = __DIR__ . '/bootstrappers.php';
$globalBootstrappers = require $globalBootstrapperPath;

/**
 * ----------------------------------------------------------
 * Set some bindings
 * ----------------------------------------------------------
 *
 * You don't do this in a bootstrapper because you need them
 * bound before bootstrappers are even run
 */
$container->bindInstance(IContainer::class, $container);
