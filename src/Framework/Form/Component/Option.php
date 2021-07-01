<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Tag;

class Option extends Tag
{
    protected const DEFAULT_TAG = Html5::TAG_OPTION;

    protected const PROTECTED_KEYS = [Html5::ATTR_VALUE];

    /**
     * Option constructor.
     *
     * @param string                    $value
     * @param INode[]|INode|string|null $content
     * @param bool                      $isSelected
     * @param string[]                  $intents
     * @param array<string,mixed>       $attributes
     * @param string|null               $tag
     */
    public function __construct(
        string $value,
        $content,
        bool $isSelected = false,
        array $intents = [],
        array $attributes = [],
        ?string $tag = null
    ) {
        parent::__construct($content, $intents, $attributes, $tag);

        $this->addAttribute(Html5::ATTR_VALUE, $value);
        if ($isSelected) {
            $this->addAttribute(Html5::ATTR_SELECTED);
        }
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->attributes[Html5::ATTR_VALUE]->getValue();
    }
}
