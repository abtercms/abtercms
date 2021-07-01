<?php

declare(strict_types=1);

namespace AbterPhp\Admin\Http\Controllers\Api;

use AbterPhp\Admin\Http\Controllers\ApiAbstract;
use AbterPhp\Admin\Service\Execute\User as RepoService;
use AbterPhp\Framework\Databases\Queries\FoundRows;
use Psr\Log\LoggerInterface;

class User extends ApiAbstract
{
    public const ENTITY_SINGULAR = 'user';
    public const ENTITY_PLURAL   = 'users';

    /**
     * User constructor.
     *
     * @param LoggerInterface $logger
     * @param RepoService     $repoService
     * @param FoundRows       $foundRows
     * @param string          $problemBaseUrl
     */
    public function __construct(
        LoggerInterface $logger,
        RepoService $repoService,
        FoundRows $foundRows,
        string $problemBaseUrl
    ) {
        parent::__construct($logger, $repoService, $foundRows, $problemBaseUrl);
    }

    /**
     * @return array
     */
    public function getSharedData(): array
    {
        $data = $this->request->getJsonBody();

        if (array_key_exists('password', $data)) {
            $data['password_repeated'] = $data['password'];
        }

        return $data;
    }
}
