<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Http\Service\Execute;

use AbterPhp\Framework\Constant\Event;
use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Events\EntityChange;
use AbterPhp\Framework\Orm\IGridRepo;
use Opulence\Events\Dispatchers\IEventDispatcher;
use Opulence\Http\Requests\UploadedFile;
use Opulence\Orm\IUnitOfWork;
use Opulence\Orm\OrmException;
use Opulence\Validation\Factories\IValidatorFactory;
use Opulence\Validation\IValidator;

abstract class RepoServiceAbstract implements IRepoService
{
    /** @var IGridRepo */
    protected $repo;

    /** @var IValidatorFactory */
    protected $validatorFactory;

    /** @var IUnitOfWork */
    protected $unitOfWork;

    /** @var IEventDispatcher */
    protected $eventDispatcher;

    /** @var IValidator */
    protected $validator;

    /**
     * RepoExecuteAbstract constructor.
     *
     * @param IGridRepo         $repo
     * @param IValidatorFactory $validatorFactory
     * @param IUnitOfWork       $unitOfWork
     * @param IEventDispatcher  $eventDispatcher
     */
    public function __construct(
        IGridRepo $repo,
        IValidatorFactory $validatorFactory,
        IUnitOfWork $unitOfWork,
        IEventDispatcher $eventDispatcher
    ) {
        $this->repo             = $repo;
        $this->validatorFactory = $validatorFactory;
        $this->unitOfWork       = $unitOfWork;
        $this->eventDispatcher  = $eventDispatcher;
    }

    /**
     * @param array $postData
     *
     * @return array
     */
    public function validateForm(array $postData): array
    {
        if ($this->getValidator()->isValid($postData)) {
            return [];
        }

        return $this->validator->getErrors()->getAll();
    }

    /**
     * @return IValidator
     */
    protected function getValidator(): IValidator
    {
        if ($this->validator) {
            return $this->validator;
        }

        $this->validator = $this->validatorFactory->createValidator();

        return $this->validator;
    }

    /**
     * @param string[]       $postData
     * @param UploadedFile[] $fileData
     *
     * @return string
     * @throws OrmException
     */
    public function create(array $postData, array $fileData): string
    {
        $entity = $this->fillEntity($this->createEntity(''), $postData);

        $this->repo->add($entity);

        $this->commitCreate($entity);

        return $entity->getId();
    }

    /**
     * @param string         $entityId
     * @param string[]       $postData
     * @param UploadedFile[] $fileData
     *
     * @return bool
     * @throws OrmException
     */
    public function update(string $entityId, array $postData, array $fileData): bool
    {
        $entity = $this->retrieveEntity($entityId);

        $this->fillEntity($entity, $postData);

        $this->commitUpdate($entity);

        return true;
    }

    /**
     * @param string $entityId
     *
     * @return bool
     * @throws OrmException
     */
    public function delete(string $entityId): bool
    {
        $entity = $this->createEntity($entityId);

        $this->repo->delete($entity);

        $this->commitDelete($entity);

        return true;
    }

    /**
     * @param IStringerEntity $entity
     *
     * @throws OrmException
     */
    protected function commitCreate(IStringerEntity $entity)
    {
        $event = new EntityChange($entity, Event::ENTITY_CREATE);

        $this->eventDispatcher->dispatch(Event::ENTITY_PRE_CHANGE, $event);

        $this->unitOfWork->commit();

        $this->eventDispatcher->dispatch(Event::ENTITY_POST_CHANGE, $event);
    }

    /**
     * @param IStringerEntity $entity
     *
     * @throws OrmException
     */
    protected function commitUpdate(IStringerEntity $entity)
    {
        $event = new EntityChange($entity, Event::ENTITY_UPDATE);

        $this->eventDispatcher->dispatch(Event::ENTITY_PRE_CHANGE, $event);

        $this->unitOfWork->commit();

        $this->eventDispatcher->dispatch(Event::ENTITY_POST_CHANGE, $event);
    }

    /**
     * @param IStringerEntity $entity
     *
     * @throws OrmException
     */
    protected function commitDelete(IStringerEntity $entity)
    {
        $event = new EntityChange($entity, Event::ENTITY_DELETE);

        $this->eventDispatcher->dispatch(Event::ENTITY_PRE_CHANGE, $event);

        $this->unitOfWork->commit();

        $this->eventDispatcher->dispatch(Event::ENTITY_POST_CHANGE, $event);
    }

    /**
     * @param string $entityId
     *
     * @return IStringerEntity
     * @throws OrmException
     */
    protected function retrieveEntity(string $entityId): IStringerEntity
    {
        /** @var IStringerEntity $entity */
        $entity = $this->repo->getById($entityId);

        return $entity;
    }

    /**
     * @param string $entityId
     *
     * @return IStringerEntity
     */
    abstract protected function createEntity(string $entityId): IStringerEntity;

    /**
     * @param IStringerEntity $entity
     * @param array           $data
     *
     * @return IStringerEntity
     */
    abstract protected function fillEntity(IStringerEntity $entity, array $data): IStringerEntity;
}