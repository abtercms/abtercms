<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Authorization\Constant\Role;
use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Collection;
use AbterPhp\Framework\Html\Helper\StringHelper;
use AbterPhp\Framework\Html\IComponent;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\INodeContainer;
use AbterPhp\Framework\Html\Tag;
use AbterPhp\Framework\I18n\ITranslator;
use Casbin\Enforcer;

class Navigation extends Tag implements INodeContainer
{
    const DEFAULT_TAG = Html5::TAG_UL;

    const ERROR_INVALID_TAG_FOR_ITEM_CREATION = 'item creation is not allowed for navigation type: %s';

    const ROLE_NAVIGATION = 'navigation';

    const INTENT_NAVBAR    = 'navbar';
    const INTENT_FOOTER    = 'footer';
    const INTENT_PRIMARY   = 'primary';
    const INTENT_SECONDARY = 'secondary';

    /** @var string */
    protected $username;

    /** @var Collection */
    protected $prefix;

    /** @var Collection */
    protected $postfix;

    /** @var IComponent|null */
    protected $wrapper;

    /** @var Enforcer|null */
    protected $enforcer;

    /** @var Item[][] */
    protected $itemsByWeight = [];

    /** @var Item[] */
    protected $nodes = [];

    /**
     * Navigation constructor.
     *
     * @param string        $username
     * @param string[]      $intents
     * @param array         $attributes
     * @param Enforcer|null $enforcer
     * @param string|null   $tag
     */
    public function __construct(
        string $username = '',
        array $intents = [],
        array $attributes = [],
        ?Enforcer $enforcer = null,
        ?string $tag = null
    ) {
        $this->username = $username;
        $this->enforcer = $enforcer;

        parent::__construct(null, $intents, $attributes, $tag);

        $this->prefix  = new Collection();
        $this->postfix = new Collection();
    }

    /**
     * @param Item   $component
     * @param int    $weight
     * @param string $resource
     * @param string $role
     *
     * @return $this
     */
    public function addItem(
        Item $component,
        int $weight = PHP_INT_MAX,
        string $resource = '',
        string $role = Role::READ
    ): Navigation {
        if (!$this->isAllowed($resource, $role)) {
            return $this;
        }

        $this->itemsByWeight[$weight][] = $component;

        return $this;
    }

    /**
     * @param string $resource
     * @param string $role
     *
     * @return bool
     */
    protected function isAllowed(string $resource, string $role): bool
    {
        if (!$resource) {
            return true;
        }

        if (!$this->enforcer) {
            return false;
        }

        try {
            return (bool)$this->enforcer->enforce($this->username, $resource, $role);
        } catch (\Exception $e) {
            return false;
        }
    }

    protected function resort()
    {
        ksort($this->itemsByWeight);

        $nodes = [];
        foreach ($this->itemsByWeight as $nodesByWeight) {
            $nodes = array_merge($nodes, $nodesByWeight);
        }

        $this->nodes = $nodes;
    }

    /**
     * @return Collection
     */
    public function getPrefix(): Collection
    {
        return $this->prefix;
    }

    /**
     * @param IComponent $prefix
     *
     * @return $this
     */
    public function setPrefix(IComponent $prefix): Navigation
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getPostfix(): Collection
    {
        return $this->postfix;
    }

    /**
     * @param Collection $postfix
     *
     * @return $this
     */
    public function setPostfix(Collection $postfix): Navigation
    {
        $this->postfix = $postfix;

        return $this;
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
    public function setWrapper(?IComponent $wrapper): Navigation
    {
        $this->wrapper = $wrapper;

        return $this;
    }

    /**
     * @return INode[]
     */
    public function getExtendedNodes(): array
    {
        $nodes = [$this->prefix, $this->postfix];
        if ($this->wrapper) {
            $nodes[] = $this->wrapper;
        }

        return array_merge($nodes, $this->getNodes());
    }

    /**
     * @return INode[]
     */
    public function getNodes(): array
    {
        $this->resort();

        return $this->nodes;
    }

    /**
     * @param int $depth
     *
     * @return INode[]
     */
    public function getDescendantNodes(int $depth = -1): array
    {
        $nodes = [];
        foreach ($this->getNodes() as $node) {
            $nodes[] = $node;

            if ($depth !== 0 && $node instanceof INodeContainer) {
                $nodes = array_merge($nodes, $node->getDescendantNodes($depth - 1));
            }
        }

        return $nodes;
    }

    /**
     * @param int $depth
     *
     * @return INode[]
     */
    public function getExtendedDescendantNodes(int $depth = -1): array
    {
        $nodes = [];
        foreach ($this->getExtendedNodes() as $node) {
            $nodes[] = $node;

            if ($depth !== 0 && $node instanceof INodeContainer) {
                $nodes = array_merge($nodes, $node->getExtendedDescendantNodes($depth - 1));
            }
        }

        return $nodes;
    }

    /**
     * @param ITranslator|null $translator
     *
     * @return $this
     */
    public function setTranslator(?ITranslator $translator): INode
    {
        $this->translator = $translator;

        $nodes = $this->getExtendedNodes();
        /** @var INode $node */
        foreach ($nodes as $node) {
            $node->setTranslator($translator);
        }

        return $this;
    }

    /**
     * @param string|INode
     *
     * @return $this
     * @deprecated
     *
     */
    public function setContent($content): INode
    {
        if ($content !== null) {
            throw new \LogicException('Navigation::setContent must not be called');
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        $this->resort();

        $itemContentList = [];
        foreach ($this->nodes as $node) {
            $itemContentList[] = (string)$node;
        }
        $content = implode("\n", $itemContentList);

        $content = StringHelper::wrapInTag($content, $this->tag, $this->attributes);
        if ($this->wrapper) {
            $content = (string)$this->wrapper->setContent($content);
        }

        $prefix  = $this->prefix ? (string)$this->prefix : '';
        $postfix = $this->postfix ? (string)$this->postfix : '';

        return $prefix . $content . $postfix;
    }
}
