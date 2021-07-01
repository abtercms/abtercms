<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Extra;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Tag;

class Help extends Tag
{
    public const CLASS_HELP_BLOCK = 'help-block';

    protected const DEFAULT_TAG = Html5::TAG_DIV;

    /**
     * Help constructor.
     *
     * @param INode[]|INode|string|null $content
     * @param array                     $intents
     * @param array<string,mixed>       $attributes
     * @param string|null               $tag
     */
    public function __construct(
        $content = null,
        array $intents = [],
        array $attributes = [],
        ?string $tag = null
    ) {
        parent::__construct($content, $intents, $attributes, $tag);

        $this->addAttribute(Html5::ATTR_CLASS, self::CLASS_HELP_BLOCK);
    }
}
