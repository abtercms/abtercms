<?php

declare(strict_types=1);

namespace AbterPhp\Website\Http\Controllers\Admin\Form;

use AbterPhp\Admin\Http\Controllers\Admin\FormAbstract;
use AbterPhp\Framework\Assets\AssetManager;
use AbterPhp\Framework\Constant\Session;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\Session\FlashService;
use AbterPhp\Website\Domain\Entities\ContentList as Entity;
use AbterPhp\Website\Form\Factory\ContentList as FormFactory;
use AbterPhp\Website\Orm\ContentListRepo as Repo;
use League\Flysystem\FilesystemException;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Routing\Urls\UrlGenerator;
use Opulence\Sessions\ISession;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 */
class ContentList extends FormAbstract
{
    public const ENTITY_SINGULAR = 'contentList';
    public const ENTITY_PLURAL   = 'contentLists';

    public const ENTITY_TITLE_SINGULAR = 'website:contentList';
    public const ENTITY_TITLE_PLURAL   = 'website:contentLists';

    public const ROUTING_PATH = 'lists';

    protected AssetManager $assetManager;

    /** @var string */
    protected string $resource = 'lists';

    /**
     * Block constructor.
     *
     * @param FlashService     $flashService
     * @param LoggerInterface  $logger
     * @param ITranslator      $translator
     * @param UrlGenerator     $urlGenerator
     * @param Repo             $repo
     * @param ISession         $session
     * @param IEventDispatcher $eventDispatcher
     * @param FormFactory      $formFactory
     * @param AssetManager     $assetManager
     */
    public function __construct(
        FlashService $flashService,
        LoggerInterface $logger,
        ITranslator $translator,
        UrlGenerator $urlGenerator,
        Repo $repo,
        ISession $session,
        IEventDispatcher $eventDispatcher,
        FormFactory $formFactory,
        AssetManager $assetManager
    ) {
        parent::__construct(
            $flashService,
            $logger,
            $translator,
            $urlGenerator,
            $repo,
            $session,
            $formFactory,
            $eventDispatcher
        );

        $this->formFactory  = $formFactory;
        $this->assetManager = $assetManager;
    }

    /**
     * @param string $entityId
     *
     * @return Entity
     */
    protected function createEntity(string $entityId): IStringerEntity
    {
        return new Entity($entityId, '', '', '', false, false, false, false, false, false);
    }

    /**
     * @param IStringerEntity|null $entity
     *
     * @throws FilesystemException
     */
    protected function addCustomAssets(?IStringerEntity $entity = null)
    {
        parent::addCustomAssets($entity);

        if (!($entity instanceof Entity)) {
            return;
        }

        $styles = $this->getResourceName(static::RESOURCE_DEFAULT);
        $footer = $this->getResourceName(static::RESOURCE_FOOTER);

        $editorLang = $this->session->get(Session::LANGUAGE_IDENTIFIER);
        $this->assetManager->addJsVar($footer, 'editorLang', $editorLang);

        $this->assetManager->addCss($styles, '/admin-assets/vendor/trumbowyg/ui/trumbowyg.css');
        $this->assetManager->addCss($styles, '/admin-assets/vendor/trumbowyg/plugins/table/ui/trumbowyg.table.css');
        $this->assetManager->addCss(
            $styles,
            '/admin-assets/vendor/trumbowyg/plugins/specialchars/ui/trumbowyg.specialchars.css'
        );
        $this->assetManager->addCss($styles, '/admin-assets/css/trumbowyg.css');
        $this->assetManager->addCss($styles, '/admin-assets/css/list.css');

        $this->assetManager->addJs($footer, '/admin-assets/vendor/jquery/jquery-resizable.js');
        $this->assetManager->addJs($footer, '/admin-assets/vendor/trumbowyg/trumbowyg.js');
        $this->assetManager->addJs($footer, "/admin-assets/vendor/trumbowyg/langs/${editorLang}.js");
        $this->assetManager->addJs($footer, '/admin-assets/vendor/trumbowyg/plugins/base64/trumbowyg.base64.js');
        $this->assetManager->addJs(
            $footer,
            '/admin-assets/vendor/trumbowyg/plugins/cleanpaste/trumbowyg.cleanpaste.js'
        );
        $this->assetManager->addJs($footer, '/admin-assets/vendor/trumbowyg/plugins/history/trumbowyg.history.js');
        $this->assetManager->addJs(
            $footer,
            '/admin-assets/vendor/trumbowyg/plugins/preformatted/trumbowyg.preformatted.js'
        );
        $this->assetManager->addJs($footer, '/admin-assets/vendor/trumbowyg/plugins/resizimg/trumbowyg.resizimg.js');
        $this->assetManager->addJs(
            $footer,
            '/admin-assets/vendor/trumbowyg/plugins/specialchars/trumbowyg.specialchars.js'
        );
        $this->assetManager->addJs($footer, '/admin-assets/vendor/trumbowyg/plugins/table/trumbowyg.table.js');
        $this->assetManager->addJs($footer, '/admin-assets/js/editor.js');
        $this->assetManager->addJs($footer, '/admin-assets/js/list.js');
        $this->assetManager->addJs($footer, '/admin-assets/js/hideable-container.js');
        $this->assetManager->addJs($footer, '/admin-assets/js/semi-auto.js');
        $this->assetManager->addJs($footer, '/admin-assets/js/required.js');
        $this->assetManager->addJs($footer, '/admin-assets/js/validation.js');
    }
}