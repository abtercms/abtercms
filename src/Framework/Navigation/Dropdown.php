<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Collection;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Helper\StringHelper;
use AbterPhp\Framework\Html\ICollection;
use AbterPhp\Framework\Html\IComponent;
use AbterPhp\Framework\Html\INode;

class Dropdown extends Component
{
    const DEFAULT_TAG = Html5::TAG_UL;

    const WRAPPER_INTENT = 'dropdown-wrapper-intent';

    const CONTENT = 'framework:logout';

    /** @var IComponent|null */
    protected $wrapper;

    /** @var ICollection */
    protected $prefix;

    /** @var ICollection */
    protected $postfix;

    /** @var Item[] */
    protected $nodes;

    /** @var string */
    protected $nodeClass = Item::class;

    /**
     * Component constructor.
     *
     * @param INode[]|INode|string|null $content
     * @param string[]                  $intents
     * @param array                     $attributes
     * @param string|null               $tag
     */
    public function __construct($content = null, array $intents = [], array $attributes = [], ?string $tag = null)
    {
        $this->wrapper = new Component(null, [static::WRAPPER_INTENT], [], Html5::TAG_DIV);

        $this->prefix  = new Collection();
        $this->postfix = new Collection();

        parent::__construct($content, $intents, $attributes, $tag);
    }

    /**
     * @return IComponent|null
     */
    public function getWrapper(): ?IComponent
    {
        return $this->wrapper;
    }

    /**
     * @param IComponent|null $wrapper
     *
     * @return $this
     */
    public function setWrapper(?IComponent $wrapper): Dropdown
    {
        $this->wrapper = $wrapper;

        return $this;
    }

    /**
     * @return ICollection
     */
    public function getPrefix(): ICollection
    {
        return $this->prefix;
    }

    /**
     * @return ICollection
     */
    public function getPostfix(): ICollection
    {
        return $this->postfix;
    }

    /**
     * @return INode[]
     */
    public function getNodes(): array
    {
        return $this->getAllNodes(0);
    }

    /**
     * @param int $depth
     *
     * @return INode[]
     */
    public function getAllNodes(int $depth = -1): array
    {
        $nodes = [$this->prefix, $this->postfix];
        if ($this->wrapper) {
            $nodes = [$this->wrapper, $this->prefix, $this->postfix];
        }

        if ($depth !== 0) {
            if ($this->wrapper) {
                $nodes = array_merge($nodes, [$this->wrapper->getAllNodes($depth - 1)]);
            }

            $nodes = array_merge(
                $nodes,
                $this->prefix->getAllNodes($depth - 1),
                $this->postfix->getAllNodes($depth - 1)
            );
        }

        return array_merge($nodes, parent::getAllNodes($depth));
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $content = Collection::__toString();
        $content = StringHelper::wrapInTag($content, $this->tag, $this->attributes);
        $content = (string)$this->prefix . $content . (string)$this->postfix;

        if ($this->wrapper) {
            $this->wrapper->setContent($content);

            return (string)$this->wrapper;
        }

        return $content;
    }
}