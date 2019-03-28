<?php

declare(strict_types=1);

namespace AbterPhp\Files\Events\Listeners;

use AbterPhp\Files\Template\FileLoader as Loader;
use AbterPhp\Framework\Events\TemplateEngineReady;
use AbterPhp\Framework\Template\ILoader;

class TemplateInitializer
{
    const TEMPLATE_TYPE = 'files';

    /** @var ILoader */
    protected $loader;

    /**
     * TemplateRegistrar constructor.
     *
     * @param Loader $loader
     */
    public function __construct(Loader $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param TemplateEngineReady $event
     */
    public function handle(TemplateEngineReady $event)
    {
        $event->getTemplateEngine()->addLoader(static::TEMPLATE_TYPE, $this->loader);
    }
}