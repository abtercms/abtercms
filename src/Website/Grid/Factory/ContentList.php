<?php

declare(strict_types=1);

namespace AbterPhp\Website\Grid\Factory;

use AbterPhp\Admin\Grid\Factory\BaseFactory;
use AbterPhp\Admin\Grid\Factory\GridFactory;
use AbterPhp\Admin\Grid\Factory\PaginationFactory;
use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Grid\Action\Action;
use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Website\Constant\Route;
use AbterPhp\Website\Grid\Factory\Table\Header\ContentList as HeaderFactory;
use AbterPhp\Website\Grid\Factory\Table\ContentList as TableFactory;
use AbterPhp\Website\Grid\Filters\ContentList as Filters;
use Opulence\Routing\Urls\UrlGenerator;

class ContentList extends BaseFactory
{
    private const GETTER_IDENTIFIER = 'getIdentifier';
    private const GETTER_NAME       = 'getName';

    /**
     * ContentList constructor.
     *
     * @param UrlGenerator      $urlGenerator
     * @param PaginationFactory $paginationFactory
     * @param TableFactory      $tableFactory
     * @param GridFactory       $gridFactory
     * @param Filters           $blockFilters
     */
    public function __construct(
        UrlGenerator $urlGenerator,
        PaginationFactory $paginationFactory,
        TableFactory $tableFactory,
        GridFactory $gridFactory,
        Filters $blockFilters
    ) {
        parent::__construct($urlGenerator, $paginationFactory, $tableFactory, $gridFactory, $blockFilters);
    }

    /**
     * @return array
     */
    public function getGetters(): array
    {
        return [
            HeaderFactory::GROUP_IDENTIFIER => static::GETTER_IDENTIFIER,
            HeaderFactory::GROUP_NAME       => static::GETTER_NAME,
        ];
    }

    /**
     * @return Actions
     */
    protected function getRowActions(): Actions
    {
        $attributeCallbacks = $this->getAttributeCallbacks();

        $editAttributes   = [
            Html5::ATTR_HREF => Route::CONTENT_LISTS_EDIT,
        ];
        $deleteAttributes = [
            Html5::ATTR_HREF => Route::CONTENT_LISTS_DELETE,
        ];

        $cellActions   = new Actions();
        $cellActions[] = new Action(
            static::LABEL_EDIT,
            $this->editIntents,
            $editAttributes,
            $attributeCallbacks,
            Html5::TAG_A
        );
        $cellActions[] = new Action(
            static::LABEL_DELETE,
            $this->deleteIntents,
            $deleteAttributes,
            $attributeCallbacks,
            Html5::TAG_A
        );

        return $cellActions;
    }
}
