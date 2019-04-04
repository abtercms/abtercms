<?php

declare(strict_types=1);

namespace AbterPhp\Website\Http\Controllers\Admin\Form;

use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Http\Controllers\Admin\FormAbstract;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use AbterPhp\Website\Domain\Entities\Page as Entity;
use AbterPhp\Website\Form\Factory\Page as FormFactory;
use AbterPhp\Website\Orm\PageRepo as Repo;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Orm\OrmException;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;

class Page extends FormAbstract
{
    const ENTITY_PLURAL   = 'pages';
    const ENTITY_SINGULAR = 'page';

    const ENTITY_TITLE_SINGULAR = 'pages:page';
    const ENTITY_TITLE_PLURAL   = 'pages:pages';

    /** @var AssetManager */
    protected $assetManager;

    /** @var string */
    protected $resource = 'pages';

    /**
     * Page constructor.
     *
     * @param FlashService     $flashService
     * @param ITranslator      $translator
     * @param UrlGenerator     $urlGenerator
     * @param Repo             $repo
     * @param ISession         $session
     * @param FormFactory      $formFactory
     * @param AssetManager     $assetManager
     * @param IEventDispatcher $eventDispatcher
     */
    public function __construct(
        FlashService $flashService,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        Repo $repo,
        ISession $session,
        FormFactory $formFactory,
        AssetManager $assetManager,
        IEventDispatcher $eventDispatcher
    ) {
        parent::__construct($flashService, $translator, $urlGenerator, $repo, $session, $formFactory, $eventDispatcher);

        $this->assetManager = $assetManager;
    }

    /**
     * @param string $entityId
     *
     * @return Entity
     */
    protected function createEntity(string $entityId): IStringerEntity
    {
        return new Entity((string)$entityId, '', '', '', '', '', null);
    }

    /**
     * @param Entity|null $entity
     *
     * @throws OrmException
     */
    protected function addCustomAssets(?IStringerEntity $entity = null)
    {
        parent::addCustomAssets($entity);

        if (!($entity instanceof Entity)) {
            return;
        }

        $styles = $this->getResourceName(static::RESOURCE_DEFAULT);
        $this->assetManager->addCss($styles, '/admin-assets/vendor/trumbowyg/ui/trumbowyg.css');

        $footer = $this->getResourceName(static::RESOURCE_FOOTER);
        $this->assetManager->addJs($footer, '/admin-assets/vendor/trumbowyg/trumbowyg.js');
        $this->assetManager->addJs($footer, '/admin-assets/vendor/trumbowyg/langs/hu.js');
        $this->assetManager->addJs($footer, '/admin-assets/js/editor.js');
        $this->assetManager->addJs($footer, '/admin-assets/js/countable-textarea.js');
        $this->assetManager->addJs($footer, '/admin-assets/js/hideable-container.js');
        $this->assetManager->addJs($footer, '/admin-assets/js/layout-or-id.js');
    }
}
